<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\User\EventListener;

use Ibexa\Contracts\User\Controller\RestrictedControllerInterface;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerArgumentsEvent;

final class PerformAccessCheckSubscriber implements EventSubscriberInterface
{
    /**
     * @param iterable<object> $controllers
     */
    public function __construct(
        #[AutowireIterator('controller.service_arguments')]
        private readonly iterable $controllers
    ) {
    }

    public function onControllerArgumentsEvent(ControllerArgumentsEvent $event): void
    {
        $controller = $event->getController();
        if (is_array($controller) && $controller[0] instanceof RestrictedControllerInterface) {
            $controller[0]->performAccessCheck();

            return;
        }

        if ($controller instanceof RestrictedControllerInterface) {
            $controller->performAccessCheck();

            return;
        }

        if (is_string($controller) && str_contains($controller, '::')) {
            [$class] = explode('::', $controller, 2);

            foreach ($this->controllers as $controllerInstance) {
                if ($controllerInstance::class === $class && $controllerInstance instanceof RestrictedControllerInterface) {
                    $controllerInstance->performAccessCheck();
                    break;
                }
            }
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ControllerArgumentsEvent::class => 'onControllerArgumentsEvent',
        ];
    }
}
