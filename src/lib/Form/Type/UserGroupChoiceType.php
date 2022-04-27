<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\User\Form\Type;

use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Contracts\Core\Repository\SearchService;
use Ibexa\Contracts\Core\Repository\UserService;
use Ibexa\Contracts\Core\Repository\Values\Content\LocationQuery;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\ContentTypeIdentifier;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause\ContentName;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\ChoiceList;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserGroupChoiceType extends AbstractType
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
                'choice_loader' => ChoiceList::lazy(
                    $this, fn() => $this->loadFilteredGroups(),
                ),
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
            $this->getUserGroups(),
            fn($group) => $this->permissionResolver->canUser('user', 'invite', $group)
        );
    }

    protected function getUserGroups(): array
    {
        return $this->repository->sudo(function() {
            $query = new LocationQuery();
            $query->filter = new ContentTypeIdentifier('user_group');
            $query->offset = 0;
            $query->limit = 100;
            $query->performCount = true;
            $query->sortClauses[] = new ContentName();

            $groups = [];
            do {
                $results = $this->searchService->findContent($query);
                foreach ($results->searchHits as $hit) {
                    $groups[] = $this->userService->loadUserGroup($hit->valueObject->id);
                }

                $query->offset += $query->limit;
            } while ($query->offset < $results->totalCount);

            return $groups;
        });
    }
}
