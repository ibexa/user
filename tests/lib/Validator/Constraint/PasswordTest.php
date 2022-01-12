<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\User\Validator\Constraint;

use Ibexa\User\Validator\Constraints\Password;
use Ibexa\User\Validator\Constraints\PasswordValidator;
use PHPUnit\Framework\TestCase;

class PasswordTest extends TestCase
{
    /** @var \Ibexa\User\Validator\Constraints\Password */
    private $constraint;

    protected function setUp(): void
    {
        $this->constraint = new Password();
    }

    public function testConstruct(): void
    {
        $this->assertSame('ez.user.password.invalid', $this->constraint->message);
    }

    public function testValidatedBy(): void
    {
        $this->assertSame(PasswordValidator::class, $this->constraint->validatedBy());
    }

    public function testGetTargets(): void
    {
        $this->assertSame([Password::CLASS_CONSTRAINT, Password::PROPERTY_CONSTRAINT], $this->constraint->getTargets());
    }
}

class_alias(PasswordTest::class, 'EzSystems\EzPlatformUser\Tests\Validator\Constraint\PasswordTest');
