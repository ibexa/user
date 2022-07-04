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
use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Core\Base\Exceptions\NotFoundException;
use Ibexa\Core\Repository\Values\Content\Content;
use Ibexa\Core\Repository\Values\Content\VersionInfo;
use Ibexa\Core\Repository\Values\User\Role;
use Ibexa\Core\Repository\Values\User\UserGroup;
use Ibexa\Tests\Core\Limitation\Base;
use Ibexa\User\Permission\UserPermissionsLimitation;
use Ibexa\User\Permission\UserPermissionsLimitationType;

class UserPermissionsLimitationTypeTest extends Base
{
    /**
     * @dataProvider providerForTestAcceptValue
     */
    public function testAcceptValue(UserPermissionsLimitation $limitation): void
    {
        $this->expectNotToPerformAssertions();
        (new UserPermissionsLimitationType($this->getPersistenceMock()))->acceptValue($limitation);
    }

    public function providerForTestAcceptValue(): array
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

    /**
     * @dataProvider providerForTestAcceptValueException
     */
    public function testAcceptValueException(UserPermissionsLimitation $limitation): void
    {
        $this->expectException(InvalidArgumentType::class);
        (new UserPermissionsLimitationType($this->getPersistenceMock()))->acceptValue($limitation);
    }

    public function providerForTestAcceptValueException(): array
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

    /**
     * @dataProvider providerForTestAcceptValue
     */
    public function testValidatePass(UserPermissionsLimitation $limitation): void
    {
        $userHandlerMock = $this->createMock(UserHandlerInterface::class);
        $contentHandlerMock = $this->createMock(ContentHandlerInterface::class);

        if ($limitation->limitationValues['roles'] !== null) {
            $userHandlerMock
                ->method('loadRole')
                ->withConsecutive([4, Role::STATUS_DEFINED], [8, Role::STATUS_DEFINED]);

            $this->getPersistenceMock()
                ->method('userHandler')
                ->willReturn($userHandlerMock);
        }

        if ($limitation->limitationValues['roles'] !== null) {
            $contentHandlerMock
                ->method('loadContentInfo')
                ->withConsecutive([14], [21]);

            $this->getPersistenceMock()
                ->method('contentHandler')
                ->willReturn($contentHandlerMock);
        }

        $validationErrors = (new UserPermissionsLimitationType($this->getPersistenceMock()))->validate($limitation);

        self::assertEmpty($validationErrors);
    }

    /**
     * @dataProvider providerForTestValidateError
     */
    public function testValidateError(UserPermissionsLimitation $limitation, int $errorCount): void
    {
        $userHandlerMock = $this->createMock(UserHandlerInterface::class);
        $contentHandlerMock = $this->createMock(ContentHandlerInterface::class);

        if ($limitation->limitationValues['roles'] !== null) {
            $userHandlerMock
                ->method('loadRole')
                ->withConsecutive([4, Role::STATUS_DEFINED], [8, Role::STATUS_DEFINED])
                ->willReturnOnConsecutiveCalls(
                    $this->throwException(new NotFoundException('Role', 4)),
                    new Role()
                );

            $this->getPersistenceMock()
                ->method('userHandler')
                ->willReturn($userHandlerMock);
        }

        if ($limitation->limitationValues['user_groups'] !== null) {
            $contentHandlerMock
                ->method('loadContentInfo')
                ->withConsecutive([14], [18])
                ->willReturnOnConsecutiveCalls(
                    $this->throwException(new NotFoundException('Role', 4)),
                    new ContentInfo()
                );

            $this->getPersistenceMock()
                ->method('contentHandler')
                ->willReturn($contentHandlerMock);
        }

        $validationErrors = (new UserPermissionsLimitationType($this->getPersistenceMock()))->validate($limitation);
        self::assertCount($errorCount, $validationErrors);
    }

    public function providerForTestValidateError()
    {
        return [
            [
                new UserPermissionsLimitation([
                    'limitationValues' => [
                        'roles' => [4, 8],
                        'user_groups' => null,
                    ],
                ]),
                1,
            ],
            [
                new UserPermissionsLimitation([
                    'limitationValues' => [
                        'roles' => null,
                        'user_groups' => [14, 18],
                    ],
                ]),
                1,
            ],
            [
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

    /**
     * @dataProvider providerForTestEvaluate
     */
    public function testEvaluate(
        UserPermissionsLimitation $limitation,
        ValueObject $object,
        ?bool $expected
    ): void {
        $value = (new UserPermissionsLimitationType($this->getPersistenceMock()))->evaluate(
            $limitation,
            $this->getUserMock(),
            $object,
        );

        self::assertEquals($expected, $value);
    }

    public function providerForTestEvaluate()
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
