<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\User\Validator\Constraint;

use Ibexa\Contracts\Core\Repository\UserService;
use Ibexa\Contracts\Core\Repository\Values\User\User as APIUser;
use Ibexa\Core\MVC\Symfony\Security\ReferenceUserInterface;
use Ibexa\User\Validator\Constraints\UserPassword;
use Ibexa\User\Validator\Constraints\UserPasswordValidator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

class UserPasswordValidatorTest extends TestCase
{
    /**
     * @var \Ibexa\Contracts\Core\Repository\UserService|\PHPUnit\Framework\MockObject\MockObject
     */
    private MockObject $userService;

    /**
     * @var \Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private MockObject $tokenStorage;

    /**
     * @var \Symfony\Component\Validator\Context\ExecutionContextInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private MockObject $executionContext;

    /**
     * @var \Ibexa\User\Validator\Constraints\UserPasswordValidator
     */
    private UserPasswordValidator $validator;

    protected function setUp(): void
    {
        $this->userService = $this->createMock(UserService::class);
        $this->tokenStorage = $this->createMock(TokenStorageInterface::class);
        $this->executionContext = $this->createMock(ExecutionContextInterface::class);
        $this->validator = new UserPasswordValidator($this->userService, $this->tokenStorage);
        $this->validator->initialize($this->executionContext);
    }

    /**
     * @dataProvider emptyDataProvider
     *
     * @param string|null $value
     */
    public function testEmptyValueType(?string $value): void
    {
        $this->userService
            ->expects(self::never())
            ->method('checkUserCredentials');
        $this->tokenStorage
            ->expects(self::never())
            ->method('getToken');
        $this->executionContext
            ->expects(self::never())
            ->method('buildViolation');

        $this->validator->validate($value, new UserPassword());
    }

    public function emptyDataProvider(): array
    {
        return [
            'empty_string' => [''],
            'null' => [null],
        ];
    }

    public function testValid(): void
    {
        $apiUser = $this->getMockForAbstractClass(APIUser::class, [], '', true, true, true, ['__get']);
        $apiUser->method('__get')->with(self::equalTo('login'))->willReturn('login');
        $user = $this->createMock(ReferenceUserInterface::class);
        $user->method('getAPIUser')->willReturn($apiUser);
        $token = $this->createMock(TokenInterface::class);
        $token->method('getUser')->willReturn($user);
        $this->tokenStorage->method('getToken')->willReturn($token);
        $this->userService
            ->method('checkUserCredentials')
            ->with($apiUser, 'password')
            ->willReturn(true);
        $this->executionContext
            ->expects(self::never())
            ->method('buildViolation');

        $this->validator->validate('password', new UserPassword());
    }

    public function testInvalid(): void
    {
        $apiUser = $this->getMockForAbstractClass(APIUser::class, [], '', true, true, true, ['__get']);
        $apiUser->method('__get')->with(self::equalTo('login'))->willReturn('login');
        $user = $this->createMock(ReferenceUserInterface::class);
        $user->method('getAPIUser')->willReturn($apiUser);
        $token = $this->createMock(TokenInterface::class);
        $token->method('getUser')->willReturn($user);
        $this->tokenStorage->method('getToken')->willReturn($token);
        $this->userService
            ->method('checkUserCredentials')
            ->with($apiUser, 'password')
            ->willReturn(false);
        $constraint = new UserPassword();
        $constraintViolationBuilder = $this->createMock(ConstraintViolationBuilderInterface::class);
        $this->executionContext
            ->expects(self::once())
            ->method('buildViolation')
            ->with($constraint->message)
            ->willReturn($constraintViolationBuilder);

        $this->validator->validate('password', new UserPassword());
    }
}
