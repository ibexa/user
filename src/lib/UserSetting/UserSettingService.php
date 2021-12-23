<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\User\UserSetting;

use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\UserPreferenceService;
use Ibexa\Contracts\Core\Repository\Values\UserPreference\UserPreferenceSetStruct;
use Ibexa\Contracts\User\UserSetting\ValueDefinitionGroupInterface;
use Ibexa\Contracts\User\UserSetting\ValueDefinitionInterface;

/**
 * @internal
 */
class UserSettingService
{
    /** @var \Ibexa\Contracts\Core\Repository\UserPreferenceService */
    protected $userPreferenceService;

    /** @var \Ibexa\User\UserSetting\ValueDefinitionRegistry */
    protected $valueRegistry;

    /**
     * @param \Ibexa\Contracts\Core\Repository\UserPreferenceService $userPreferenceService
     * @param \Ibexa\User\UserSetting\ValueDefinitionRegistry $valueRegistry
     */
    public function __construct(
        UserPreferenceService $userPreferenceService,
        ValueDefinitionRegistry $valueRegistry
    ) {
        $this->userPreferenceService = $userPreferenceService;
        $this->valueRegistry = $valueRegistry;
    }

    /**
     * @param string $identifier
     * @param string $value
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     * @throws \Ibexa\Core\Base\Exceptions\InvalidArgumentException
     */
    public function setUserSetting(string $identifier, string $value): void
    {
        $userPreferenceSetStructs = [
            new UserPreferenceSetStruct(['name' => $identifier, 'value' => $value]),
        ];

        $this->userPreferenceService->setUserPreference($userPreferenceSetStructs);
    }

    /**
     * @param string $identifier
     *
     * @return \Ibexa\User\UserSetting\UserSetting
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function getUserSetting(string $identifier): UserSetting
    {
        $valueDefinition = $this->valueRegistry->getValueDefinition($identifier);

        $userPreferenceValue = $this->getUserSettingValue($identifier, $valueDefinition);

        return $this->createUserSetting($identifier, $valueDefinition, $userPreferenceValue);
    }

    public function getUserSettingGroup(string $identifier): UserSettingGroup
    {
        $group = $this->valueRegistry->getValueDefinitionGroup($identifier);

        $userPreferences = [];
        foreach ($group->getValues() as $settingIdentifier => $userSettingDefinition) {
            $userPreferences[$settingIdentifier] = $this->getUserSettingValue($settingIdentifier, $userSettingDefinition);
        }

        return $this->createUserSettings($identifier, $group, $userPreferences);
    }

    /**
     * @param int $offset
     * @param int $limit
     *
     * @return array
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function loadUserSettings(int $offset = 0, int $limit = 25): array
    {
        $values = $this->valueRegistry->getValueDefinitions();
        /** @var \Ibexa\Contracts\User\UserSetting\ValueDefinitionInterface[] $slice */
        $slice = \array_slice($values, $offset, $limit, true);

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

    public function loadGroupedUserSettings(int $offset = 0, int $limit = 25): array
    {
        $groups = $this->valueRegistry->getValueDefinitionGroups();

        $slice = \array_slice($groups, $offset, $limit, true);

        $settings = [];
        foreach ($slice as $groupId => $group) {
            $userPreferences = [];
            foreach ($group->getValues() as $identifier => $userSettingDefinition) {
                $userPreferences[$identifier] = $this->getUserSettingValue($identifier, $userSettingDefinition);
            }
            $settings[$groupId] = $this->createUserSettings($groupId, $group, $userPreferences);
        }

        return $settings;
    }

    /**
     * @return int
     */
    public function countUserSettings(): int
    {
        return $this->valueRegistry->countValueDefinitions();
    }

    private function createUserSettings(string $groupId, ValueDefinitionGroupInterface $group, array $userPreferences): UserSettingGroup
    {
        $userSettings = [];
        foreach ($group->getValues() as $identifier => $value) {
            $userSettings[] = $this->createUserSetting($identifier, $value, $userPreferences[$identifier]);
        }

        return new UserSettingGroup([
            'identifier' => $groupId,
            'name' => $group->getName(),
            'description' => $group->getDescription(),
            'settings' => $userSettings,
        ]);
    }

    /**
     * @param string $identifier
     * @param \Ibexa\Contracts\User\UserSetting\ValueDefinitionInterface $value
     * @param string $userPreferenceValue
     *
     * @return \Ibexa\User\UserSetting\UserSetting
     */
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
     * @param string $identifier
     * @param \Ibexa\Contracts\User\UserSetting\ValueDefinitionInterface $value
     *
     * @return string
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    private function getUserSettingValue(string $identifier, ValueDefinitionInterface $value): string
    {
        try {
            $userPreference = $this->userPreferenceService->getUserPreference($identifier);
            $userPreferenceValue = $userPreference->value;
        } catch (NotFoundException $e) {
            $userPreferenceValue = $value->getDefaultValue();
        }

        return $userPreferenceValue;
    }

    public function countGroupedUserSettings()
    {
        return $this->valueRegistry->countValueDefinitionGroups();
    }
}

class_alias(UserSettingService::class, 'EzSystems\EzPlatformUser\UserSetting\UserSettingService');
