<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\User\Stub;

use Ibexa\Contracts\User\Controller\RestrictedControllerInterface;

final class RestrictedControllerStub implements RestrictedControllerInterface
{
    private bool $checkPerformed = false;

    public function performAccessCheck(): void
    {
        $this->checkPerformed = true;
    }

    public function wasCheckPerformed(): bool
    {
        return $this->checkPerformed;
    }

    public function __invoke(): void
    {
    }

    public function action(): void
    {
    }

    public static function staticAction(): void
    {
    }
}
