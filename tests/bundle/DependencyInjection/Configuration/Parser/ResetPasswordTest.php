<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\User\DependencyInjection\Configuration\Parser;

use Ibexa\Bundle\Core\DependencyInjection\IbexaCoreExtension;
use Ibexa\Bundle\User\DependencyInjection\Configuration\Parser\ResetPassword;
use Ibexa\Bundle\User\DependencyInjection\IbexaUserExtension;
use Ibexa\Tests\Bundle\Core\DependencyInjection\Configuration\Parser\AbstractParserTestCase;

final class ResetPasswordTest extends AbstractParserTestCase
{
    protected function getContainerExtensions(): array
    {
        return [
            new IbexaCoreExtension([
                new ResetPassword(),
            ]),
            new IbexaUserExtension(),
        ];
    }

    protected function getMinimalConfiguration(): array
    {
        return [
            'system' => [
                'default' => [
                    'user_reset_password' => [
                        'templates' => [
                            'form' => 'default/path/template.html.twig',
                        ],
                    ],
                ],
            ],
        ];
    }

    public function testDefaultSettings(): void
    {
        $this->load();

        $this->assertConfigResolverParameterValue(
            'user_reset_password.templates.form',
            'default/path/template.html.twig',
            'ibexa_demo_site'
        );
    }

    public function testOverwrittenConfig()
    {
        $this->load([
            'system' => [
                'ibexa_demo_site' => [
                    'user_reset_password' => [
                        'templates' => [
                            'form' => '@yourOwnBundle/path/to/template.html.twig',
                            'invalid_link' => '@yourOwnBundle/path/to/invalid_link.html.twig',
                            'success' => '@yourOwnBundle/path/to/success.html.twig',
                        ],
                    ],
                ],
            ],
        ]);

        $this->assertConfigResolverParameterValue(
            'user_reset_password.templates.form',
            '@yourOwnBundle/path/to/template.html.twig',
            'ibexa_demo_site'
        );
        $this->assertConfigResolverParameterValue(
            'user_reset_password.templates.invalid_link',
            '@yourOwnBundle/path/to/invalid_link.html.twig',
            'ibexa_demo_site'
        );
        $this->assertConfigResolverParameterValue(
            'user_reset_password.templates.success',
            '@yourOwnBundle/path/to/success.html.twig',
            'ibexa_demo_site'
        );
    }
}

class_alias(ResetPasswordTest::class, 'EzSystems\EzPlatformUserBundle\Tests\DependencyInjection\Configuration\Parser\ResetPasswordTest');
