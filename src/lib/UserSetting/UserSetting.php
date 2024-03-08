<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\User\UserSetting;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;

/**
 * @property string $identifier @deprecated use {@see UserSetting::getIdentifier()} instead.
 * @property string $name @deprecated use {@see UserSetting::getName()} instead.
 * @property string $description @deprecated use {@see UserSetting::getDescription()} instead.
 * @property string $value @deprecated use {@see UserSetting::getValue()} instead.
 */
class UserSetting extends ValueObject
{
    /** @var string */
    protected $identifier;
    /** @var string */
    protected $name;

    /** @var string */
    protected $description;

    /** @var string */
    protected $value;

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

    public function getValue(): string
    {
        return $this->value;
    }
}

class_alias(UserSetting::class, 'EzSystems\EzPlatformUser\UserSetting\UserSetting');
