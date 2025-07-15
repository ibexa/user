<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\User\Form\ChoiceList\Loader;

use Ibexa\Contracts\Core\Repository\Iterator\BatchIterator;
use Ibexa\Contracts\Core\Repository\Iterator\BatchIteratorAdapter\ContentSearchAdapter;
use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Contracts\Core\Repository\SearchService;
use Ibexa\Contracts\Core\Repository\UserService;
use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\ContentTypeIdentifier;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause\ContentName;
use Symfony\Component\Form\ChoiceList\Loader\AbstractChoiceLoader;

final class UserGroupsChoiceLoader extends AbstractChoiceLoader
{
    public function __construct(
        private readonly Repository $repository,
        private readonly SearchService $searchService,
        private readonly UserService $userService
    ) {
    }

    /**
     * @return iterable<\Ibexa\Contracts\Core\Repository\Values\User\UserGroup>
     */
    protected function loadChoices(): iterable
    {
        return $this->repository->sudo(function () {
            $query = new Query();
            $query->filter = new ContentTypeIdentifier('user_group');
            $query->offset = 0;
            $query->limit = 100;
            $query->performCount = true;
            $query->sortClauses[] = new ContentName();

            $groups = [];
            $iterator = new BatchIterator(new ContentSearchAdapter($this->searchService, $query));
            foreach ($iterator as $result) {
                $groups[] = $this->userService->loadUserGroup($result->valueObject->id);
            }

            return $groups;
        });
    }
}
