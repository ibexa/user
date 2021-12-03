<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\User\Validator\Constraint;

use Ibexa\User\Validator\Constraints\UserPassword;
use PHPUnit\Framework\TestCase;

class UserPasswordTest extends TestCase
{
    public function testConstruct()
    {
        $constraint = new UserPassword();
        self::assertSame('ezplatform.change_user_password.not_match', $constraint->message);
    }
}

class_alias(UserPasswordTest::class, 'EzSystems\EzPlatformUser\Tests\Validator\Constraint\UserPasswordTest');
