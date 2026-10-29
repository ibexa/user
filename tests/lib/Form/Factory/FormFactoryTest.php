<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\User\Form\Factory;

use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Contracts\Core\Repository\Values\User\User;
use Ibexa\User\Form\Data\UserPasswordResetData;
use Ibexa\User\Form\Factory\FormFactory;
use Ibexa\User\Form\Type\UserPasswordResetType;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[CoversClass(FormFactory::class)]
final class FormFactoryTest extends TestCase
{
    public function testResetUserPasswordPassesContentTypeAndUserAsOptions(): void
    {
        $data = new UserPasswordResetData();
        $contentType = self::createStub(ContentType::class);
        $user = self::createStub(User::class);
        $form = self::createStub(FormInterface::class);

        $symfonyFormFactory = $this->createMock(FormFactoryInterface::class);
        $symfonyFormFactory
            ->expects($this->once())
            ->method('createNamed')
            ->with(
                'user_password_reset',
                UserPasswordResetType::class,
                $data,
                [
                    'content_type' => $contentType,
                    'user' => $user,
                ],
            )
            ->willReturn($form);

        $formFactory = new FormFactory($symfonyFormFactory, self::createStub(UrlGeneratorInterface::class));

        self::assertSame($form, $formFactory->resetUserPassword($data, null, $contentType, $user));
    }
}
