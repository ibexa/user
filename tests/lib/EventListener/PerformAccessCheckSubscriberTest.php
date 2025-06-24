<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\User\EventListener;

use Ibexa\Contracts\User\Controller\RestrictedControllerInterface;
use Ibexa\User\EventListener\PerformAccessCheckSubscriber;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ControllerArgumentsEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

final class PerformAccessCheckSubscriberTest extends TestCase
{
    private PerformAccessCheckSubscriber $subscriber;

    private HttpKernelInterface $kernel;

    private Request $request;

    protected function setUp(): void
    {
        $this->kernel = $this->createMock(HttpKernelInterface::class);
        $this->request = new Request();
        $this->subscriber = new PerformAccessCheckSubscriber([]);
    }

    public function testArrayController(): void
    {
        $controller = new MockControllerInterface();
        $event = new ControllerArgumentsEvent(
            $this->kernel,
            [$controller, 'action'],
            [],
            $this->request,
            HttpKernelInterface::MAIN_REQUEST
        );

        $this->subscriber->onControllerArgumentsEvent($event);

        self::assertTrue($controller->wasCheckPerformed());
    }

    public function testInvokableController(): void
    {
        $controller = new MockControllerInterface();
        $event = new ControllerArgumentsEvent(
            $this->kernel,
            $controller,
            [],
            $this->request,
            HttpKernelInterface::MAIN_REQUEST
        );

        $this->subscriber->onControllerArgumentsEvent($event);

        self::assertTrue($controller->wasCheckPerformed());
    }

    public function testStringControllerWithServiceLookup(): void
    {
        $controller = new MockControllerInterface();
        $this->subscriber = new PerformAccessCheckSubscriber([$controller]);

        $event = new ControllerArgumentsEvent(
            $this->kernel,
            MockControllerInterface::class . '::staticAction',
            [],
            $this->request,
            HttpKernelInterface::MAIN_REQUEST
        );

        $this->subscriber->onControllerArgumentsEvent($event);

        self::assertTrue($controller->wasCheckPerformed());
    }
}

final class MockControllerInterface implements RestrictedControllerInterface
{
    private bool $checkPerformed = false;

    public function performAccessCheck(): void
    {
        $this->checkPerformed = true;
    }

    public function wasCheckPerformed(): bool
    {
        return $this->checkPerformed;
    }

    public function __invoke(): void
    {
    }

    public function action(): void
    {
    }

    public static function staticAction(): void
    {
    }
}
