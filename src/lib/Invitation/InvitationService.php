<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\User\Invitation;

use DateTime;
use Exception;
use Ibexa\Contracts\Core\HashGenerator;
use Ibexa\Contracts\Core\Persistence\TransactionHandler;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\Contracts\Core\Repository\UserService;
use Ibexa\Contracts\Core\Repository\Values\User\Limitation\RoleLimitation;
use Ibexa\Contracts\Core\Repository\Values\User\Role;
use Ibexa\Contracts\Core\Repository\Values\User\UserGroup;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Contracts\User\Invitation\Exception\InvitationExist;
use Ibexa\Contracts\User\Invitation\Exception\UserExist;
use Ibexa\Contracts\User\Invitation\Invitation;
use Ibexa\Contracts\User\Invitation\InvitationService as InvitationServiceInterface;
use Ibexa\Core\Base\Exceptions\UnauthorizedException;
use Ibexa\Core\MVC\Symfony\SiteAccess;
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

    public function __construct(
        PermissionResolver $permissionResolver,
        Handler $handler,
        HashGenerator $hashGenerator,
        UserService $userService,
        SiteAccessServiceInterface $siteAccessService,
        TransactionHandler $transactionHandler,
        ConfigResolverInterface $configResolver
    ) {
        $this->handler = $handler;
        $this->hashGenerator = $hashGenerator;
        $this->siteAccessService = $siteAccessService;
        $this->permissionResolver = $permissionResolver;
        $this->userService = $userService;
        $this->transactionHandler = $transactionHandler;
        $this->configResolver = $configResolver;
    }

    public function createInvitation(
        string $email,
        SiteAccess $siteAccess,
        ?UserGroup $userGroup = null,
        ?Role $role = null,
        ?RoleLimitation $roleLimitation = null
    ): Invitation {
        if (!$this->permissionResolver->hasAccess('user', 'invite')) {
            throw new UnauthorizedException('user', 'invite');
        }

        if ($userGroup
            && !$this->permissionResolver->canUser('user', 'invite', $userGroup)
        ) {
            throw new UnauthorizedException('user', 'invite', ['user_group' => $userGroup->getName()]);
        }

        if ($role
            && !$this->permissionResolver->canUser('user', 'invite', $role)
        ) {
            throw new UnauthorizedException('user', 'invite', ['role' => $role->identifier]);
        }

        if (
            $this->handler->invitationExistsForEmail($email)
        ) {
            throw new InvitationExist();
        }
        $userExists = false;
        try {
            $userExists = $this->userService->loadUserByEmail($email);
        } catch (NotFoundException $exception) {
        }
        if ($userExists) {
            throw new UserExist();
        }

        $this->transactionHandler->beginTransaction();

        try {
            $invitation = $this->handler->createInvitation(
                $email,
                $siteAccess->name,
                $this->hashGenerator->generate(),
                $role ? $role->id : null,
                $userGroup ? $userGroup->id : null,
                $roleLimitation ? $roleLimitation->getIdentifier() : null,
                $roleLimitation ? json_encode($roleLimitation->limitationValues) : null
            );

            $this->transactionHandler->commit();
        } catch (Exception $e) {
            $this->transactionHandler->rollback();
            throw $e;
        }

        return $invitation;
    }

    public function isValid(Invitation $invitation): bool
    {
        $current = new DateTime();
        $expirationTime = $this->configResolver->getParameter(
            'user_invitation.hash_expiration_time',
            null,
            $invitation->getSiteAccess()->name
        );

        if ($invitation->createdAt()->add(new \DateInterval($expirationTime)) <= $current) {
            return false;
        }

        if ($invitation->getSiteAccess()->name !== $this->siteAccessService->getCurrent()->name) {
            return false;
        }

        if ($invitation->isUsed()) {
            return false;
        }

        return true;
    }

    public function getInvitation(string $hash): Invitation
    {
        return $this->handler->getInvitation($hash);
    }

    public function getInviteForEmail(string $email): Invitation
    {
        return $this->handler->getInvitationForEmail($email);
    }

    public function markAsUsed(Invitation $invitation): void
    {
        $this->handler->markAsUsed($invitation);
    }
}
