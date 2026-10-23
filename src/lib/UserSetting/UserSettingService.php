<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\User\UserSetting;

use Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException;
use Ibexa\Contracts\Core\Repository\UserPreferenceService;
use Ibexa\Contracts\Core\Repository\Values\UserPreference\UserPreferenceSetStruct;
use Ibexa\Contracts\User\UserSetting\ValueDefinitionGroupInterface;
use Ibexa\Contracts\User\UserSetting\ValueDefinitionInterface;

/**
 * @internal
 */
class UserSettingService
{
    public function __construct(
        protected UserPreferenceService $userPreferenceService,
        protected ValueDefinitionRegistry $valueRegistry
    ) {}

    /**
     * @throws UnauthorizedException
     * @throws InvalidArgumentException
     */
    public function setUserSetting(
        string $identifier,
        string $value
    ): void {
        $userPreferenceSetStructs = [
            new UserPreferenceSetStruct(['name' => $identifier, 'value' => $value]),
        ];

        $this->userPreferenceService->setUserPreference($userPreferenceSetStructs);
    }

    /**
     * @throws InvalidArgumentException
     * @throws UnauthorizedException
     */
    public function getUserSetting(string $identifier): UserSetting
    {
        $valueDefinition = $this->valueRegistry->getValueDefinition($identifier);

        $userPreferenceValue = $this->getUserSettingValue($identifier, $valueDefinition);

        return $this->createUserSetting($identifier, $valueDefinition, $userPreferenceValue);
    }

    /**
     * @throws InvalidArgumentException
     * @throws UnauthorizedException
     */
    public function getUserSettingGroup(string $identifier): UserSettingGroup
    {
        $group = $this->valueRegistry->getValueDefinitionGroup($identifier);

        $userPreferences = [];
        foreach ($group->getValueDefinitions() as $settingIdentifier => $userSettingDefinition) {
            $userPreferences[$settingIdentifier] = $this->getUserSettingValue($settingIdentifier, $userSettingDefinition);
        }

        return $this->createUserSettingsGroup($identifier, $group, $userPreferences);
    }

    /**
     * @return list<UserSetting>
     *
     * @throws UnauthorizedException
     */
    public function loadUserSettings(
        int $offset = 0,
        int $limit = 25
    ): array {
        $values = $this->valueRegistry->getValueDefinitions();
        /** @var ValueDefinitionInterface[] $slice */
        $slice = array_slice($values, $offset, $limit, true);

        $userPreferences = [];
        foreach ($slice as $identifier => $userSettingDefinition) {
            $userPreferences[$identifier] = $this->getUserSettingValue($identifier, $userSettingDefinition);
        }

        $userSettings = [];
        foreach ($slice as $identifier => $value) {
            $userSettings[] = $this->createUserSetting($identifier, $value, $userPreferences[$identifier]);
        }

        return $userSettings;
    }

    /**
     * @return array<string, UserSettingGroup>
     *
     * @throws UnauthorizedException
     */
    public function loadGroupedUserSettings(): array
    {
        $groups = $this->valueRegistry->getValueDefinitionGroups();

        $settings = [];
        foreach ($groups as $groupId => $group) {
            $userSettingsValues = [];
            foreach ($group->getValueDefinitions() as $identifier => $userSettingDefinition) {
                $userSettingsValues[$identifier] = $this->getUserSettingValue($identifier, $userSettingDefinition);
            }
            $settings[$groupId] = $this->createUserSettingsGroup($groupId, $group, $userSettingsValues);
        }

        return $settings;
    }

    public function countUserSettings(): int
    {
        return $this->valueRegistry->countValueDefinitions();
    }

    /**
     * @param array<string, string> $userPreferences
     */
    private function createUserSettingsGroup(
        string $groupId,
        ValueDefinitionGroupInterface $group,
        array $userPreferences
    ): UserSettingGroup {
        $userSettings = [];
        foreach ($group->getValueDefinitions() as $identifier => $value) {
            $userSettings[] = $this->createUserSetting($identifier, $value, $userPreferences[$identifier]);
        }

        return new UserSettingGroup(
            $groupId,
            $group->getName(),
            $group->getDescription(),
            $userSettings
        );
    }

    private function createUserSetting(
        string $identifier,
        ValueDefinitionInterface $value,
        string $userPreferenceValue
    ): UserSetting {
        return new UserSetting([
            'identifier' => $identifier,
            'name' => $value->getName(),
            'description' => $value->getDescription(),
            'value' => $userPreferenceValue,
        ]);
    }

    /**
     * @throws UnauthorizedException
     */
    private function getUserSettingValue(
        string $identifier,
        ValueDefinitionInterface $value
    ): string {
        try {
            $userPreference = $this->userPreferenceService->getUserPreference($identifier);
            $userPreferenceValue = $userPreference->value;
        } catch (NotFoundException) {
            $userPreferenceValue = $value->getDefaultValue();
        }

        return $userPreferenceValue;
    }

    public function countGroupedUserSettings(): int
    {
        return $this->valueRegistry->countValueDefinitionGroups();
    }
}
