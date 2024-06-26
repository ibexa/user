<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\User\UserSetting;

/**
 * Interface for displaying User Preferences in the Admin UI.
 *
 * User Preferences are not displayed by default unless
 * ValueDefinitionInterface implementation is provided.
 */
interface ValueDefinitionInterface
{
    /**
     * Returns name of a User Preference displayed in UI.
     */
    public function getName(): string;

    /**
     * Returns description of a User Preference displayed in UI.
     */
    public function getDescription(): string;

    /**
     * Returns formatted value to be displayed in UI.
     */
    public function getDisplayValue(string $storageValue): string;

    /**
     * Returns default value for User Preference if none is defined.
     */
    public function getDefaultValue(): string;
}
