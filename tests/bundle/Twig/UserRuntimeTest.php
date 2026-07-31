<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\User\Twig;

use Ibexa\Bundle\User\Twig\UserRuntime;
use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\Contracts\Core\Repository\UserService;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Contracts\Core\Repository\Values\User\User;
use Ibexa\Contracts\Core\Repository\Values\User\UserReference;
use Ibexa\Contracts\User\Password\PasswordRequirement;
use Ibexa\Contracts\User\Password\PasswordRequirementsResolverInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class UserRuntimeTest extends TestCase
{
    private const int CURRENT_USER_ID = 14;

    private PermissionResolver&MockObject $permissionResolver;

    private UserService&MockObject $userService;

    private PasswordRequirementsResolverInterface&MockObject $passwordRequirementsResolver;

    private UserRuntime $runtime;

    protected function setUp(): void
    {
        $this->permissionResolver = $this->createMock(PermissionResolver::class);
        $this->userService = $this->createMock(UserService::class);
        $this->passwordRequirementsResolver = $this->createMock(
            PasswordRequirementsResolverInterface::class
        );

        $this->runtime = new UserRuntime(
            $this->permissionResolver,
            $this->userService,
            $this->passwordRequirementsResolver
        );
    }

    public function testGetPasswordRequirementsFallsBackToCurrentUserContentType(): void
    {
        $contentType = $this->createMock(ContentType::class);
        $requirements = [new PasswordRequirement(PasswordRequirement::MIN_LENGTH, ['%length%' => 10])];

        $this->mockCurrentUserWithContentType($contentType);
        $this->passwordRequirementsResolver
            ->expects(self::once())
            ->method('getRequirements')
            ->with($contentType)
            ->willReturn($requirements);

        self::assertSame($requirements, $this->runtime->getPasswordRequirements());
    }

    public function testGetPasswordRequirementsForGivenContentType(): void
    {
        $contentType = $this->createMock(ContentType::class);
        $requirements = [new PasswordRequirement(PasswordRequirement::UPPER_CASE)];

        $this->userService
            ->expects(self::never())
            ->method('loadUser');
        $this->passwordRequirementsResolver
            ->expects(self::once())
            ->method('getRequirements')
            ->with($contentType)
            ->willReturn($requirements);

        self::assertSame($requirements, $this->runtime->getPasswordRequirements($contentType));
    }

    private function mockCurrentUserWithContentType(ContentType $contentType): void
    {
        $userReference = $this->createMock(UserReference::class);
        $userReference->method('getUserId')->willReturn(self::CURRENT_USER_ID);

        $user = $this->createMock(User::class);
        $user->method('getContentType')->willReturn($contentType);

        $this->permissionResolver
            ->method('getCurrentUserReference')
            ->willReturn($userReference);
        $this->userService
            ->method('loadUser')
            ->with(self::CURRENT_USER_ID)
            ->willReturn($user);
    }
}
