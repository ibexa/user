<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\User\UserSetting;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;

/**
 * @property string $identifier @deprecated 4.6.7 accessing magic getter is deprecated and will be removed in 5.0.0. Use {@see UserSetting::getIdentifier()} instead.
 * @property string $name @deprecated 4.6.7 accessing magic getter is deprecated and will be removed in 5.0.0. Use {@see UserSetting::getName()} instead.
 * @property string $description @deprecated 4.6.7 accessing magic getter is deprecated and will be removed in 5.0.0. Use {@see UserSetting::getDescription()} instead.
 * @property string $value @deprecated 4.6.7 accessing magic getter is deprecated and will be removed in 5.0.0. Use {@see UserSetting::getValue()} instead.
 */
class UserSetting extends ValueObject
{
    protected string $identifier;

    protected string $name;

    protected string $description;

    protected string $value;

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
