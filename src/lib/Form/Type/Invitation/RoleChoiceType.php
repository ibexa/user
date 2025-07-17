<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\User\Form\Type\Invitation;

use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\Contracts\Core\Repository\RoleService;
use Ibexa\Contracts\Core\Repository\Values\User\Role;
use Ibexa\Core\Repository\Repository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\Loader\CallbackChoiceLoader;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class RoleChoiceType extends AbstractType
{
    public function __construct(
        private readonly RoleService $roleService,
        private readonly Repository $repository,
        private readonly PermissionResolver $permissionResolver
    ) {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'choice_loader' => new CallbackChoiceLoader($this->loadFilteredRoles(...)),
                'choice_label' => 'identifier',
                'choice_name' => 'id',
                'choice_value' => 'id',
            ]);
    }

    /**
     * @return array<int, \Ibexa\Contracts\Core\Repository\Values\User\Role>
     */
    public function loadFilteredRoles(): array
    {
        return array_filter(
            $this->repository->sudo(fn (): iterable => $this->roleService->loadRoles()),
            fn (Role $role): bool => $this->permissionResolver->canUser('user', 'invite', $role)
        );
    }

    #[\Override]
    public function getParent(): string
    {
        return ChoiceType::class;
    }
}
