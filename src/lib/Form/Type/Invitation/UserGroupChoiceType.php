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
use Ibexa\User\Form\ChoiceList\Loader\UserGroupsChoiceLoader;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\Loader\CallbackChoiceLoader;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class UserGroupChoiceType extends AbstractType
{
    private UserService $userService;

    private SearchService $searchService;

    private Repository $repository;

    private PermissionResolver $permissionResolver;

    public function __construct(
        Repository $repository,
        UserService $userService,
        SearchService $searchService,
        PermissionResolver $permissionResolver
    ) {
        $this->userService = $userService;
        $this->searchService = $searchService;
        $this->repository = $repository;
        $this->permissionResolver = $permissionResolver;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'choice_loader' => new CallbackChoiceLoader([$this, 'loadFilteredGroups']),
                'choice_label' => 'name',
                'choice_name' => 'id',
                'choice_value' => 'id',
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent(): ?string
    {
        return ChoiceType::class;
    }

    protected function loadFilteredGroups(): array
    {
        return array_filter(
            (new UserGroupsChoiceLoader(
                $this->repository,
                $this->searchService,
                $this->userService
            ))->loadChoiceList()->getChoices(),
            fn ($group) => $this->permissionResolver->canUser('user', 'invite', $group)
        );
    }
}
