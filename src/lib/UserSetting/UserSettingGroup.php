<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\User\UserSetting;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;

class UserSettingGroup extends ValueObject
{
    protected string $identifier;

    protected string $name;

    protected string $description;

    /** @var list<\Ibexa\User\UserSetting\UserSetting> */
    protected array $settings;

    /**
     * @param list<\Ibexa\User\UserSetting\UserSetting> $settings
     */
    public function __construct(string $identifier, string $name = '', string $description = '', array $settings = [])
    {
        parent::__construct([
            'identifier' => $identifier,
            'name' => $name,
            'description' => $description,
            'settings' => $settings,
        ]);
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return list<\Ibexa\User\UserSetting\UserSetting>
     */
    public function getSettings(): array
    {
        return $this->settings;
    }
}
