<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\User\Validator\Constraint;

use Ibexa\Contracts\Core\Repository\UserService;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Contracts\Core\Repository\Values\User\PasswordValidationContext;
use Ibexa\Contracts\Core\Repository\Values\User\User;
use Ibexa\Core\FieldType\ValidationError;
use Ibexa\User\Validator\Constraints\Password;
use Ibexa\User\Validator\Constraints\PasswordValidator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

class PasswordValidatorTest extends TestCase
{
    private UserService&MockObject $userService;

    private ExecutionContextInterface&MockObject $executionContext;

    private PasswordValidator $validator;

    protected function setUp(): void
    {
        $this->userService = $this->createMock(UserService::class);
        $this->executionContext = $this->createMock(ExecutionContextInterface::class);
        $this->validator = new PasswordValidator($this->userService);
        $this->validator->initialize($this->executionContext);
    }

    /**
     * @dataProvider dataProviderForValidateNotSupportedValueType
     */
    public function testValidateShouldBeSkipped(mixed $value): void
    {
        $this->userService
            ->expects(self::never())
            ->method('validatePassword');

        $this->executionContext
            ->expects(self::never())
            ->method('buildViolation');

        $this->validator->validate($value, new Password());
    }

    public function testValid(): void
    {
        $password = 'pass';
        $contentType = $this->createMock(ContentType::class);
        $user = $this->createMock(User::class);

        $this->userService
            ->expects(self::once())
            ->method('validatePassword')
            ->willReturnCallback(
                static function (string $actualPassword, PasswordValidationContext $actualContext) use (
                    $password,
                    $contentType,
                    $user
                ): array {
                    self::assertEquals($password, $actualPassword);
                    self::assertInstanceOf(PasswordValidationContext::class, $actualContext);
                    self::assertSame($contentType, $actualContext->contentType);
                    self::assertSame($user, $actualContext->user);

                    return [];
                }
            );

        $this->executionContext
            ->expects(self::never())
            ->method('buildViolation');

        $this->validator->validate(
            $password,
            new Password([
                'contentType' => $contentType,
                'user' => $user,
            ])
        );
    }

    public function testInvalid(): void
    {
        $contentType = $this->createMock(ContentType::class);
        $password = 'pass';
        $errorParameter = 'foo';
        $errorMessage = 'error';

        $this->userService
            ->expects(self::once())
            ->method('validatePassword')
            ->willReturnCallback(function (string $actualPassword, PasswordValidationContext $actualContext) use (
                $password,
                $contentType,
                $errorMessage,
                $errorParameter
            ): array {
                $this->assertEquals($password, $actualPassword);
                $this->assertInstanceOf(PasswordValidationContext::class, $actualContext);
                $this->assertSame($contentType, $actualContext->contentType);

                return [
                    new ValidationError($errorMessage, null, ['%foo%' => $errorParameter]),
                ];
            });

        $constraintViolationBuilder = $this->createMock(ConstraintViolationBuilderInterface::class);

        $this->executionContext
            ->expects(self::once())
            ->method('buildViolation')
            ->willReturn($constraintViolationBuilder);
        $this->executionContext
            ->expects(self::once())
            ->method('buildViolation')
            ->with($errorMessage)
            ->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder
            ->expects(self::once())
            ->method('setParameters')
            ->with(['%foo%' => $errorParameter])
            ->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder
            ->expects(self::once())
            ->method('addViolation');

        $this->validator->validate('pass', new Password([
            'contentType' => $contentType,
        ]));
    }

    public function dataProviderForValidateNotSupportedValueType(): array
    {
        return [
            [new stdClass()],
            [null],
            [''],
        ];
    }
}
