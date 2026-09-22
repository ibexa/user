<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\User\Permission;

use Ibexa\Contracts\Core\Exception\InvalidArgumentType;
use Ibexa\Contracts\Core\Persistence\Content\Handler as ContentHandlerInterface;
use Ibexa\Contracts\Core\Persistence\User\Handler as UserHandlerInterface;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Contracts\Core\Repository\Values\User\User as APIUser;
use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Core\Base\Exceptions\NotFoundException;
use Ibexa\Core\Repository\Values\Content\Content;
use Ibexa\Core\Repository\Values\Content\VersionInfo;
use Ibexa\Core\Repository\Values\User\Role;
use Ibexa\Core\Repository\Values\User\UserGroup;
use Ibexa\Tests\Core\Limitation\Base;
use Ibexa\User\Permission\UserPermissionsLimitation;
use Ibexa\User\Permission\UserPermissionsLimitationType;
use PHPUnit\Framework\Attributes\DataProvider;

class UserPermissionsLimitationTypeTest extends Base
{
    #[DataProvider('providerForTestAcceptValue')]
    public function testAcceptValue(UserPermissionsLimitation $limitation): void
    {
        $this->expectNotToPerformAssertions();
        (new UserPermissionsLimitationType($this->getPersistenceMock()))->acceptValue($limitation);
    }

    /**
     * @return array<int, array{
     *     0: \Ibexa\User\Permission\UserPermissionsLimitation
     * }>
     */
    public static function providerForTestAcceptValue(): array
    {
        return [
            [
                new UserPermissionsLimitation(
                    [
                        'limitationValues' => [
                            'roles' => null,
                            'user_groups' => null,
                        ],
                    ]
                ),
            ],
            [
                new UserPermissionsLimitation(
                    [
                        'limitationValues' => [
                            'roles' => [],
                            'user_groups' => [],
                        ],
                    ]
                ),
            ],
            [
                new UserPermissionsLimitation(
                    [
                        'limitationValues' => [
                            'roles' => [4, 8],
                            'user_groups' => [14, 21],
                        ],
                    ]
                ),
            ],
        ];
    }

    #[DataProvider('providerForTestAcceptValueException')]
    public function testAcceptValueException(UserPermissionsLimitation $limitation): void
    {
        $this->expectException(InvalidArgumentType::class);
        (new UserPermissionsLimitationType($this->getPersistenceMock()))->acceptValue($limitation);
    }

    /**
     * @return array<int, array{
     *     0: \Ibexa\User\Permission\UserPermissionsLimitation
     * }>
     */
    public static function providerForTestAcceptValueException(): array
    {
        return [
            [
                new UserPermissionsLimitation(),
            ],
            [
                new UserPermissionsLimitation([]),
            ],
            [
                new UserPermissionsLimitation([
                    'limitationValues' => [
                        'user_groups' => [],
                    ],
                ]),
            ],
            [
                new UserPermissionsLimitation([
                    'limitationValues' => [
                        'roles' => 1,
                        'user_groups' => [14, 21],
                    ],
                ]),
            ],
        ];
    }

    #[DataProvider('providerForTestAcceptValue')]
    public function testValidatePass(UserPermissionsLimitation $limitation): void
    {
        $userHandlerMock = $this->createMock(UserHandlerInterface::class);
        $contentHandlerMock = $this->createMock(ContentHandlerInterface::class);

        if ($limitation->limitationValues['roles'] !== null) {
            // Original test never asserted an invocation count here (bare ->method(), no
            // ->expects()), only the per-call arguments via withConsecutive - some data sets
            // (e.g. empty 'roles' arrays) legitimately invoke loadRole() zero times.
            $roleMatcher = self::any();
            $userHandlerMock->expects($roleMatcher)
                ->method('loadRole')
                ->willReturnCallback(static function (int $roleId, int $status) use ($roleMatcher): ?Role {
                    if ($roleMatcher->numberOfInvocations() === 1) {
                        self::assertSame([4, Role::STATUS_DEFINED], [$roleId, $status]);
                    } else {
                        self::assertSame([8, Role::STATUS_DEFINED], [$roleId, $status]);
                    }

                    return null;
                });

            $this->getPersistenceMock()
                ->method('userHandler')
                ->willReturn($userHandlerMock);
        }

        if ($limitation->limitationValues['roles'] !== null) {
            $contentMatcher = self::any();
            $contentHandlerMock->expects($contentMatcher)
                ->method('loadContentInfo')
                ->willReturnCallback(static function (int $contentId) use ($contentMatcher): ?ContentInfo {
                    if ($contentMatcher->numberOfInvocations() === 1) {
                        self::assertSame(14, $contentId);
                    } else {
                        self::assertSame(21, $contentId);
                    }

                    return null;
                });

            $this->getPersistenceMock()
                ->method('contentHandler')
                ->willReturn($contentHandlerMock);
        }

        $validationErrors = (new UserPermissionsLimitationType($this->getPersistenceMock()))->validate($limitation);

        self::assertEmpty($validationErrors);
    }

    #[DataProvider('providerForTestValidateError')]
    public function testValidateError(UserPermissionsLimitation $limitation, int $errorCount): void
    {
        $userHandlerMock = $this->createMock(UserHandlerInterface::class);
        $contentHandlerMock = $this->createMock(ContentHandlerInterface::class);

        if ($limitation->limitationValues['roles'] !== null) {
            $roleMatcher = self::exactly(2);
            $userHandlerMock->expects($roleMatcher)
                ->method('loadRole')
                ->willReturnCallback(static function (int $roleId, int $status) use ($roleMatcher): Role {
                    if ($roleMatcher->numberOfInvocations() === 1) {
                        self::assertSame([4, Role::STATUS_DEFINED], [$roleId, $status]);

                        throw new NotFoundException('Role', 4);
                    }

                    self::assertSame([8, Role::STATUS_DEFINED], [$roleId, $status]);

                    return new Role();
                });

            $this->getPersistenceMock()
                ->method('userHandler')
                ->willReturn($userHandlerMock);
        }

        if ($limitation->limitationValues['user_groups'] !== null) {
            $contentMatcher = self::exactly(2);
            $contentHandlerMock->expects($contentMatcher)
                ->method('loadContentInfo')
                ->willReturnCallback(static function (int $contentId) use ($contentMatcher): ContentInfo {
                    if ($contentMatcher->numberOfInvocations() === 1) {
                        self::assertSame(14, $contentId);

                        throw new NotFoundException('Role', 4);
                    }

                    self::assertSame(18, $contentId);

                    return new ContentInfo();
                });

            $this->getPersistenceMock()
                ->method('contentHandler')
                ->willReturn($contentHandlerMock);
        }

        $validationErrors = (new UserPermissionsLimitationType($this->getPersistenceMock()))->validate($limitation);
        self::assertCount($errorCount, $validationErrors);
    }

    /**
     * @return array<string, array{
     *     0: \Ibexa\User\Permission\UserPermissionsLimitation,
     *     1: int
     * }>
     */
    public static function providerForTestValidateError(): array
    {
        return [
            'roles_limitation_only' => [
                new UserPermissionsLimitation([
                    'limitationValues' => [
                        'roles' => [4, 8],
                        'user_groups' => null,
                    ],
                ]),
                1,
            ],
            'user_groups_limitation_only' => [
                new UserPermissionsLimitation([
                    'limitationValues' => [
                        'roles' => null,
                        'user_groups' => [14, 18],
                    ],
                ]),
                1,
            ],
            'both_limitations' => [
                new UserPermissionsLimitation(
                    [
                        'limitationValues' => [
                            'roles' => [4, 8],
                            'user_groups' => [14, 18],
                        ],
                    ]
                ),
                2,
            ],
        ];
    }

    #[DataProvider('providerForTestEvaluate')]
    public function testEvaluate(
        UserPermissionsLimitation $limitation,
        ValueObject $object,
        ?bool $expected
    ): void {
        $value = (new UserPermissionsLimitationType($this->getPersistenceMock()))->evaluate(
            $limitation,
            self::createStub(APIUser::class),
            $object,
        );

        self::assertEquals($expected, $value);
    }

    /**
     * @return array<string, array{
     *     limitation: \Ibexa\User\Permission\UserPermissionsLimitation,
     *     object: \Ibexa\Contracts\Core\Repository\Values\ValueObject,
     *     expected: bool|null
     * }>
     */
    public static function providerForTestEvaluate(): array
    {
        return [
            'valid_role_limitation' => [
                'limitation' => new UserPermissionsLimitation([
                    'limitationValues' => [
                        'roles' => [4, 8],
                        'user_groups' => [14, 18],
                    ],
                ]),
                'object' => new Role(['id' => 4]),
                'expected' => true,
            ],
            'valid_group_limitation' => [
                'limitation' => new UserPermissionsLimitation([
                    'limitationValues' => [
                        'roles' => [4, 8],
                        'user_groups' => [14, 18],
                    ],
                ]),
                'object' => new UserGroup([
                    'content' => new Content([
                        'versionInfo' => new VersionInfo([
                            'contentInfo' => new ContentInfo([
                                'id' => 14,
                            ]),
                        ]),
                    ]),
                ]),
                'expected' => true,
            ],
            'allow_non_role_limitation' => [
                'limitation' => new UserPermissionsLimitation([
                    'limitationValues' => [
                        'roles' => null,
                        'user_groups' => [14, 18],
                    ],
                ]),
                'object' => new Role(['id' => 4]),
                'expected' => false,
            ],
            'allow_all_role_limitation' => [
                'limitation' => new UserPermissionsLimitation([
                    'limitationValues' => [
                        'roles' => [],
                        'user_groups' => [14, 18],
                    ],
                ]),
                'object' => new Role(['id' => 4]),
                'expected' => true,
            ],
            'mixed_role_id_with_group' => [
                'limitation' => new UserPermissionsLimitation([
                    'limitationValues' => [
                        'roles' => [4, 8],
                        'user_groups' => [14, 18],
                    ],
                ]),
                'object' => new Role(['id' => 14]),
                'expected' => false,
            ],
            'pass_to_next_limitation' => [
                'limitation' => new UserPermissionsLimitation([
                    'limitationValues' => [
                        'roles' => [4, 8],
                        'user_groups' => [14, 18],
                    ],
                ]),
                'object' => new VersionInfo([
                    'contentInfo' => new ContentInfo([
                        'id' => 14,
                    ]),
                ]),
                'expected' => null,
            ],
        ];
    }
}
