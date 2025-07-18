<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\User\Form\Type\Invitation;

use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Contracts\Core\Repository\SearchService;
use Ibexa\Contracts\Core\Repository\UserService;
use Ibexa\Contracts\Core\Repository\Values\User\UserGroup;
use Ibexa\User\Form\ChoiceList\Loader\UserGroupsChoiceLoader;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\Loader\CallbackChoiceLoader;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class UserGroupChoiceType extends AbstractType
{
    public function __construct(
        private readonly Repository $repository,
        private readonly UserService $userService,
        private readonly SearchService $searchService,
        private readonly PermissionResolver $permissionResolver
    ) {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'choice_loader' => new CallbackChoiceLoader($this->loadFilteredGroups(...)),
                'choice_label' => 'name',
                'choice_name' => 'id',
                'choice_value' => 'id',
            ]);
    }

    #[\Override]
    public function getParent(): string
    {
        return ChoiceType::class;
    }

    /**
     * @return array<int, \Ibexa\Contracts\Core\Repository\Values\User\UserGroup>
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\BadStateException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function loadFilteredGroups(): array
    {
        return array_filter(
            (new UserGroupsChoiceLoader(
                $this->repository,
                $this->searchService,
                $this->userService
            ))->loadChoiceList()->getChoices(),
            fn (UserGroup $group): bool => $this->permissionResolver->canUser('user', 'invite', $group)
        );
    }
}
