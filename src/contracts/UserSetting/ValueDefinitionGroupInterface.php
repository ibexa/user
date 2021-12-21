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
interface ValueDefinitionGroupInterface
{
    /**
     * Returns name of a User Preference Group displayed in UI.
     */
    public function getName(): string;

    /**
     * Returns description of a User Preference Group displayed in UI.
     */
    public function getDescription(): string;

    public function addToGroup(string $identifier, ValueDefinitionInterface $valueDefinition): void;

    public function getValues(): array;
}
