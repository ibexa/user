<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\User;

use Ibexa\Bundle\Notifications\IbexaNotificationsBundle;
use Ibexa\Bundle\User\IbexaUserBundle;
use Ibexa\ContentForms\Form\ActionDispatcher\UserDispatcher;
use Ibexa\Contracts\Core\Test\IbexaTestKernel as BaseIbexaTestKernel;
use Ibexa\Contracts\User\Invitation\InvitationService;
use LogicException;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

final class IbexaTestKernel extends BaseIbexaTestKernel
{
    #[\Override]
    public function getSchemaFiles(): iterable
    {
        yield from parent::getSchemaFiles();

        yield from [
            $this->locateResource('@IbexaUserBundle/Resources/config/storage/schema.yaml'),
        ];
    }

    #[\Override]
    public function registerBundles(): iterable
    {
        yield from parent::registerBundles();

        yield from [
            new IbexaUserBundle(),
            new IbexaNotificationsBundle(),
        ];
    }

    #[\Override]
    protected static function getExposedServicesByClass(): iterable
    {
        yield from parent::getExposedServicesByClass();

        yield InvitationService::class;
    }

    #[\Override]
    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        parent::registerContainerConfiguration($loader);

        $loader->load(static function (ContainerBuilder $container): void {
            $container->setParameter('locale_fallback', 'en');

            self::createSyntheticService($container);

            $container->loadFromExtension('framework', [
                'router' => [
                    'resource' => __DIR__ . '/Resources/routing.yaml',
                ],
            ]);
        });
    }

    /**
     * Creates synthetic services in container, allowing compilation of container when some services are missing.
     * Additionally, those services can be replaced with mock implementations at runtime, allowing integration testing.
     *
     * You can set them up in KernelTestCase by calling `self::getContainer()->set($id, $this->createMock($class));`
     */
    private static function createSyntheticService(ContainerBuilder $container): void
    {
        $id = UserDispatcher::class;
        if ($container->has($id)) {
            throw new LogicException(sprintf(
                'Expected test kernel to not contain "%s" service. A real service should not be overwritten by a mock',
                $id,
            ));
        }

        $definition = new Definition(UserDispatcher::class);
        $definition->setSynthetic(true);
        $container->setDefinition($id, $definition);
    }
}
