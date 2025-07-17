<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\User\DependencyInjection\Configuration\Parser;

use Ibexa\Bundle\Core\DependencyInjection\IbexaCoreExtension;
use Ibexa\Bundle\User\DependencyInjection\Configuration\Parser\ForgotPassword;
use Ibexa\Bundle\User\DependencyInjection\IbexaUserExtension;
use Ibexa\Tests\Bundle\Core\DependencyInjection\Configuration\Parser\AbstractParserTestCase;

final class ForgotPasswordTest extends AbstractParserTestCase
{
    protected function getContainerExtensions(): array
    {
        return [
            new IbexaCoreExtension([
                new ForgotPassword(),
            ]),
            new IbexaUserExtension(),
        ];
    }

    #[\Override]
    protected function getMinimalConfiguration(): array
    {
        return [
            'system' => [
                'default' => [
                    'user_forgot_password' => [
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
            'user_forgot_password.templates.form',
            'default/path/template.html.twig',
            'ibexa_demo_site'
        );
    }

    public function testOverwrittenConfig(): void
    {
        $this->load([
            'system' => [
                'ibexa_demo_site' => [
                    'user_forgot_password' => [
                        'templates' => [
                            'form' => '@yourOwnBundle/path/to/template.html.twig',
                            'mail' => '@yourOwnBundle/path/to/mail.html.twig',
                        ],
                    ],
                    'user_forgot_password_success' => [
                        'templates' => [
                            'form' => '@yourOwnBundle/path/to/template_success.html.twig',
                        ],
                    ],
                    'user_forgot_password_login' => [
                        'templates' => [
                            'form' => '@yourOwnBundle/path/to/template_login.html.twig',
                        ],
                    ],
                ],
            ],
        ]);

        $this->assertConfigResolverParameterValue(
            'user_forgot_password.templates.form',
            '@yourOwnBundle/path/to/template.html.twig',
            'ibexa_demo_site'
        );
        $this->assertConfigResolverParameterValue(
            'user_forgot_password.templates.mail',
            '@yourOwnBundle/path/to/mail.html.twig',
            'ibexa_demo_site'
        );
        $this->assertConfigResolverParameterValue(
            'user_forgot_password_success.templates.form',
            '@yourOwnBundle/path/to/template_success.html.twig',
            'ibexa_demo_site'
        );
        $this->assertConfigResolverParameterValue(
            'user_forgot_password_login.templates.form',
            '@yourOwnBundle/path/to/template_login.html.twig',
            'ibexa_demo_site'
        );
    }
}
