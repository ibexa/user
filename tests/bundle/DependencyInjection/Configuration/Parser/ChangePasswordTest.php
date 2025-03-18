<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\User\DependencyInjection\Configuration\Parser;

use Ibexa\Bundle\Core\DependencyInjection\IbexaCoreExtension;
use Ibexa\Bundle\User\DependencyInjection\Configuration\Parser\ChangePassword;
use Ibexa\Bundle\User\DependencyInjection\IbexaUserExtension;
use Ibexa\Tests\Bundle\Core\DependencyInjection\Configuration\Parser\AbstractParserTestCase;

final class ChangePasswordTest extends AbstractParserTestCase
{
    protected function getContainerExtensions(): array
    {
        return [
            new IbexaCoreExtension([
                new ChangePassword(),
            ]),
            new IbexaUserExtension(),
        ];
    }

    protected function getMinimalConfiguration(): array
    {
        return [
            'system' => [
                'default' => [
                    'user_change_password' => [
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
            'user_change_password.templates.form',
            'default/path/template.html.twig',
            'ibexa_demo_site'
        );
    }

    public function testOverwrittenConfig(): void
    {
        $this->load([
            'system' => [
                'ibexa_demo_site' => [
                    'user_change_password' => [
                        'templates' => [
                            'form' => '@yourOwnBundle/path/to/template.html.twig',
                            'success' => '@yourOwnBundle/path/to/success.html.twig',
                        ],
                    ],
                ],
            ],
        ]);

        $this->assertConfigResolverParameterValue(
            'user_change_password.templates.form',
            '@yourOwnBundle/path/to/template.html.twig',
            'ibexa_demo_site'
        );
        $this->assertConfigResolverParameterValue(
            'user_change_password.templates.success',
            '@yourOwnBundle/path/to/success.html.twig',
            'ibexa_demo_site'
        );
    }
}
