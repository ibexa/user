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
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;
use Ibexa\Contracts\Core\Repository\Values\User\User;
use Ibexa\Contracts\Core\Repository\Values\User\UserReference;
use Ibexa\User\Password\PasswordRequirement;
use Ibexa\User\Password\PasswordRequirementsResolver;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class UserRuntimeTest extends TestCase
{
    private const int CURRENT_USER_ID = 14;

    private PermissionResolver&MockObject $permissionResolver;

    private UserService&MockObject $userService;

    private UserRuntime $runtime;

    protected function setUp(): void
    {
        $this->permissionResolver = $this->createMock(PermissionResolver::class);
        $this->userService = $this->createMock(UserService::class);

        $this->runtime = new UserRuntime(
            $this->permissionResolver,
            $this->userService,
            new PasswordRequirementsResolver()
        );
    }

    public function testGetPasswordRequirementsFallsBackToCurrentUserContentType(): void
    {
        $this->mockCurrentUserWithContentType($this->createContentTypeWithMinLength(10));

        $requirements = $this->runtime->getPasswordRequirements();

        self::assertCount(1, $requirements);
        self::assertSame(PasswordRequirement::MIN_LENGTH, $requirements[0]->getIdentifier());
        self::assertSame(['%length%' => 10], $requirements[0]->getParameters());
    }

    public function testGetPasswordRequirementsForGivenContentType(): void
    {
        $this->userService
            ->expects(self::never())
            ->method('loadUser');

        $requirements = $this->runtime->getPasswordRequirements(
            $this->createContentTypeWithMinLength(16)
        );

        self::assertCount(1, $requirements);
        self::assertSame(PasswordRequirement::MIN_LENGTH, $requirements[0]->getIdentifier());
        self::assertSame(['%length%' => 16], $requirements[0]->getParameters());
    }

    private function createContentTypeWithMinLength(int $minLength): ContentType
    {
        $fieldDefinition = $this->createMock(FieldDefinition::class);
        $fieldDefinition
            ->expects(self::once())
            ->method('getValidatorConfiguration')
            ->willReturn(['PasswordValueValidator' => ['minLength' => $minLength]]);
        $fieldDefinition
            ->expects(self::once())
            ->method('getFieldSettings')
            ->willReturn([]);

        $contentType = $this->createMock(ContentType::class);
        $contentType
            ->expects(self::once())
            ->method('getFirstFieldDefinitionOfType')
            ->with('ibexa_user')
            ->willReturn($fieldDefinition);

        return $contentType;
    }

    private function mockCurrentUserWithContentType(ContentType $contentType): void
    {
        $userReference = $this->createMock(UserReference::class);
        $userReference
            ->expects(self::once())
            ->method('getUserId')
            ->willReturn(self::CURRENT_USER_ID);

        $user = $this->createMock(User::class);
        $user
            ->expects(self::once())
            ->method('getContentType')
            ->willReturn($contentType);

        $this->permissionResolver
            ->expects(self::once())
            ->method('getCurrentUserReference')
            ->willReturn($userReference);
        $this->userService
            ->expects(self::once())
            ->method('loadUser')
            ->with(self::CURRENT_USER_ID)
            ->willReturn($user);
    }
}
