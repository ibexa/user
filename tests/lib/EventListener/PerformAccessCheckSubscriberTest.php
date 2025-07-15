<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\User\EventListener;

use Ibexa\Tests\User\Stub\RestrictedControllerStub;
use Ibexa\User\EventListener\PerformAccessCheckSubscriber;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

final class PerformAccessCheckSubscriberTest extends TestCase
{
    private PerformAccessCheckSubscriber $subscriber;

    private HttpKernelInterface&MockObject $kernel;

    private Request $request;

    protected function setUp(): void
    {
        $this->kernel = $this->createMock(HttpKernelInterface::class);
        $this->request = new Request();
        $this->subscriber = new PerformAccessCheckSubscriber([]);
    }

    public function testArrayController(): void
    {
        $controller = new RestrictedControllerStub();
        $event = new ControllerEvent(
            $this->kernel,
            $controller->action(...),
            $this->request,
            HttpKernelInterface::MAIN_REQUEST
        );

        $this->subscriber->onControllerEvent($event);

        self::assertTrue($controller->wasCheckPerformed());
    }

    public function testInvokableController(): void
    {
        $controller = new RestrictedControllerStub();
        $event = new ControllerEvent(
            $this->kernel,
            $controller,
            $this->request,
            HttpKernelInterface::MAIN_REQUEST
        );

        $this->subscriber->onControllerEvent($event);

        self::assertTrue($controller->wasCheckPerformed());
    }

    public function testStringControllerWithServiceLookup(): void
    {
        $controller = new RestrictedControllerStub();
        $this->subscriber = new PerformAccessCheckSubscriber([$controller]);

        $event = new ControllerEvent(
            $this->kernel,
            RestrictedControllerStub::class . '::staticAction',
            $this->request,
            HttpKernelInterface::MAIN_REQUEST
        );

        $this->subscriber->onControllerEvent($event);

        self::assertTrue($controller->wasCheckPerformed());
    }
}
