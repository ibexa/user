<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\User\Form\Type;

use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\Contracts\Core\Repository\RoleService;
use Ibexa\Core\Repository\Repository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\ChoiceList;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RoleChoiceType extends AbstractType
{
    private RoleService $roleService;

    private Repository $repository;

    private PermissionResolver $permissionResolver;

    public function __construct(
        RoleService $roleService,
        Repository $repository,
        PermissionResolver $permissionResolver
    ) {
        $this->roleService = $roleService;
        $this->repository = $repository;
        $this->permissionResolver = $permissionResolver;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'choice_loader' => ChoiceList::lazy(
                    $this,
                    fn () => $this->loadFilteredRoles(),
                ),
                'choice_label' => 'identifier',
                'choice_name' => 'id',
                'choice_value' => 'id',
            ]);
    }

    protected function loadFilteredRoles(): array
    {
        return array_filter(
            $this->repository->sudo(fn () => $this->roleService->loadRoles()),
            fn ($role) => $this->permissionResolver->canUser('user', 'invite', $role)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getParent(): ?string
    {
        return ChoiceType::class;
    }
}
