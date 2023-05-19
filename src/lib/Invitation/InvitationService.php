<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\User\Invitation;

use DateInterval;
use DateTime;
use Exception;
use Ibexa\Contracts\Core\HashGenerator;
use Ibexa\Contracts\Core\Persistence\TransactionHandler;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\Contracts\Core\Repository\UserService;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Contracts\User\Invitation\DomainMapper;
use Ibexa\Contracts\User\Invitation\Exception\InvitationAlreadyExistsException;
use Ibexa\Contracts\User\Invitation\Exception\UserAlreadyExistsException;
use Ibexa\Contracts\User\Invitation\Invitation;
use Ibexa\Contracts\User\Invitation\InvitationCreateStruct;
use Ibexa\Contracts\User\Invitation\InvitationService as InvitationServiceInterface;
use Ibexa\Contracts\User\Invitation\Query\InvitationFilter;
use Ibexa\Core\Base\Exceptions\UnauthorizedException;
use Ibexa\Core\MVC\Symfony\SiteAccess\SiteAccessServiceInterface;
use Ibexa\User\Invitation\Persistence\Handler;

final class InvitationService implements InvitationServiceInterface
{
    private Handler $handler;

    private HashGenerator $hashGenerator;

    private SiteaccessServiceInterface $siteAccessService;

    private PermissionResolver $permissionResolver;

    private UserService $userService;

    private TransactionHandler $transactionHandler;

    private ConfigResolverInterface $configResolver;

    private DomainMapper $domainMapper;

    public function __construct(
        PermissionResolver $permissionResolver,
        Handler $handler,
        HashGenerator $hashGenerator,
        UserService $userService,
        SiteAccessServiceInterface $siteAccessService,
        TransactionHandler $transactionHandler,
        ConfigResolverInterface $configResolver,
        DomainMapper $domainMapper
    ) {
        $this->handler = $handler;
        $this->hashGenerator = $hashGenerator;
        $this->siteAccessService = $siteAccessService;
        $this->permissionResolver = $permissionResolver;
        $this->userService = $userService;
        $this->transactionHandler = $transactionHandler;
        $this->configResolver = $configResolver;
        $this->domainMapper = $domainMapper;
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\BadStateException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     * @throws \JsonException
     */
    public function createInvitation(
        InvitationCreateStruct $createStruct
    ): Invitation {
        if (!$this->permissionResolver->hasAccess('user', 'invite')) {
            throw new UnauthorizedException('user', 'invite');
        }

        $userGroup = $createStruct->getUserGroup();
        if ($userGroup
            && !$this->permissionResolver->canUser('user', 'invite', $userGroup)
        ) {
            throw new UnauthorizedException('user', 'invite', ['user_group' => $userGroup->getName()]);
        }

        $role = $createStruct->getRole();
        if ($role
            && !$this->permissionResolver->canUser('user', 'invite', $role)
        ) {
            throw new UnauthorizedException('user', 'invite', ['role' => $role->identifier]);
        }

        if (
            $this->handler->invitationExistsForEmail($createStruct->getEmail())
        ) {
            throw new InvitationAlreadyExistsException();
        }
        try {
            if ($this->userService->loadUserByEmail($createStruct->getEmail())) {
                throw new UserAlreadyExistsException();
            }
        } catch (NotFoundException $exception) {
        }

        $roleLimitation = $createStruct->getRoleLimitation();

        $this->transactionHandler->beginTransaction();

        try {
            $invitation = $this->handler->createInvitation(
                $createStruct->getEmail(),
                $createStruct->getSiteAccessIdentifier(),
                $this->hashGenerator->generate(),
                $role ? $role->id : null,
                $userGroup ? $userGroup->id : null,
                $roleLimitation ? $roleLimitation->getIdentifier() : null,
                $roleLimitation ? json_encode($roleLimitation->limitationValues, JSON_THROW_ON_ERROR) : null
            );

            $this->transactionHandler->commit();
        } catch (Exception $e) {
            $this->transactionHandler->rollback();
            throw $e;
        }

        return $this->domainMapper->buildDomainObject($invitation);
    }

    public function isExpired(Invitation $invitation): bool
    {
        $current = new DateTime();
        $expirationTime = $this->configResolver->getParameter(
            'user_invitation.hash_expiration_time',
            null,
            $invitation->getSiteAccessIdentifier()
        );

        return $current >= $invitation->createdAt()->add(new DateInterval($expirationTime));
    }

    public function isValid(Invitation $invitation): bool
    {
        if ($this->isExpired($invitation)) {
            return false;
        }

        if ($invitation->getSiteAccessIdentifier() !== $this->siteAccessService->getCurrent()->name) {
            return false;
        }

        if ($invitation->isUsed()) {
            return false;
        }

        return true;
    }

    public function getInvitation(string $hash): Invitation
    {
        return $this->domainMapper->buildDomainObject(
            $this->handler->getInvitation($hash)
        );
    }

    public function getInvitationByEmail(string $email): Invitation
    {
        return $this->domainMapper->buildDomainObject(
            $this->handler->getInvitationForEmail($email)
        );
    }

    public function markAsUsed(Invitation $invitation): void
    {
        $this->handler->markAsUsed($invitation->getHash());
    }

    /**
     * @return \Ibexa\Contracts\User\Invitation\Invitation[]
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\BadStateException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function findInvitations(?InvitationFilter $invitationsFilter = null): array
    {
        if (!$this->permissionResolver->hasAccess('user', 'invite')) {
            throw new UnauthorizedException('user', 'invite');
        }

        $invitations = [];

        foreach ($this->handler->findInvitations($invitationsFilter) as $invitation) {
            $invitations[] = $this->domainMapper->buildDomainObject($invitation);
        }

        return array_filter($invitations, function (Invitation $invitation): bool {
            $access = true;
            if ($invitation->getUserGroup()) {
                $access = $this->permissionResolver->canUser('user', 'invite', $invitation->getUserGroup());
            }
            if ($invitation->getRole()) {
                $access = $this->permissionResolver->canUser('user', 'invite', $invitation->getRole());
            }

            return $access;
        });
    }

    public function refreshInvitation(Invitation $invitation): Invitation
    {
        if (!$this->permissionResolver->hasAccess('user', 'invite')) {
            throw new UnauthorizedException('user', 'invite');
        }

        $userGroup = $invitation->getUserGroup();
        if ($userGroup
            && !$this->permissionResolver->canUser('user', 'invite', $userGroup)
        ) {
            throw new UnauthorizedException('user', 'invite', ['user_group' => $userGroup->getName()]);
        }

        $role = $invitation->getRole();
        if ($role
            && !$this->permissionResolver->canUser('user', 'invite', $role)
        ) {
            throw new UnauthorizedException('user', 'invite', ['role' => $role->identifier]);
        }

        return $this->domainMapper->buildDomainObject(
            $this->handler->refreshInvitation($invitation->getHash(), $this->hashGenerator->generate())
        );
    }
}
