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

    protected array $settings;
}
