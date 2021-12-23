<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\User\View\UserSettings\Matcher;

use Ibexa\Core\MVC\Symfony\Matcher\ViewMatcherInterface;
use Ibexa\Core\MVC\Symfony\View\View;
use Ibexa\User\UserSetting\UserSetting;
use Ibexa\User\View\UserSettings\UpdateView;

/**
 * Match based on the user setting identifier.
 */
class Identifier implements ViewMatcherInterface
{
    /** @var string[] */
    private $identifiers = [];

    /**
     * {@inheritdoc}
     */
    public function setMatchingConfig($matchingConfig): void
    {
        $this->identifiers = (array)$matchingConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function match(View $view): bool
    {
        if (!$view instanceof UpdateView || $view->getUserSettingGroup() === null) {
            return false;
        }

        $identifiersInGroup = array_column($view->getUserSettingGroup()->getSettings(), 'identifier');

        return !empty(array_intersect($identifiersInGroup, $this->identifiers));
    }
}

class_alias(Identifier::class, 'EzSystems\EzPlatformUser\View\UserSettings\Matcher\Identifier');
