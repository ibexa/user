<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\User\UserSetting;

use ArrayAccess;
use Ibexa\Contracts\Core\Repository\Exceptions\NotImplementedException;

/**
 * @internal
 */
class UserSettingArrayAccessor implements ArrayAccess
{
    public function __construct(
        protected UserSettingService $userSettingService
    ) {
    }

    public function offsetExists(mixed $offset): bool
    {
        // @todo refactor once UserSettingService supports this natively

        return null !== $this->userSettingService->getUserSetting($offset);
    }

    public function offsetGet(mixed $offset): string
    {
        return $this->userSettingService->getUserSetting($offset)->value;
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->userSettingService->setUserSetting($offset, $value);
    }

    public function offsetUnset(mixed $offset): void
    {
        // UserSettingService doesn't provide this feature

        throw new NotImplementedException('offsetUnset');
    }
}
