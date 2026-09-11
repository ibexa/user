<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\User\Security\Authentication;

use Ibexa\Bundle\User\Security\Authentication\DefaultAuthenticationFailureHandler;
use Ibexa\Bundle\User\Security\Exception\BlankCredentialsException;
use Ibexa\Contracts\Core\Repository\Exceptions\PasswordInUnsupportedFormatException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Http\HttpUtils;

final class DefaultAuthenticationFailureHandlerTest extends TestCase
{
    private DefaultAuthenticationFailureHandler $handler;

    private MockObject&HttpUtils $httpUtils;

    protected function setUp(): void
    {
        $this->httpUtils = $this->createMock(HttpUtils::class);
        $this->handler = new DefaultAuthenticationFailureHandler(
            $this->createMock(HttpKernelInterface::class),
            $this->httpUtils
        );
    }

    public function testHandlePasswordInUnsupportedFormatException(): void
    {
        $request = $this->getRequest();
        $exception = new PasswordInUnsupportedFormatException();
        $expectedUrl = '/forgot-password';

        $this->httpUtils
            ->expects(self::once())
            ->method('generateUri')
            ->with($request, 'ibexa.user.forgot_password.migration')
            ->willReturn($expectedUrl);

        $this->handler->onAuthenticationFailure($request, $exception);
    }

    public function testOnAuthenticationFailureAltersBadCredentialsExceptionMessage(): void
    {
        $session = $this->getSession();
        $session
            ->expects(self::once())
            ->method('set')
            ->with(
                '_security.last_error',
                self::callback(static function (AuthenticationException $exception): bool {
                    self::assertInstanceOf(BadCredentialsException::class, $exception);
                    self::assertNotInstanceOf(BlankCredentialsException::class, $exception);
                    self::assertSame('Bad credentials.', $exception->getMessage());

                    return true;
                })
            );

        $request = $this->getRequest($session, 'admin', 'secret');
        $originalException = new BadCredentialsException('Original message');

        $this->httpUtils
            ->expects(self::once())
            ->method('createRedirectResponse')
            ->with($request);

        $this->handler->onAuthenticationFailure($request, $originalException);
    }

    /**
     * @dataProvider dataProviderForBlankCredentials
     *
     * @param list<BlankCredentialsException::FIELD_*> $expectedBlankFields
     */
    public function testOnAuthenticationFailureReportsBlankCredentialFields(
        string $username,
        string $password,
        array $expectedBlankFields
    ): void {
        $session = $this->getSession();
        $session
            ->expects(self::once())
            ->method('set')
            ->with(
                '_security.last_error',
                self::callback(
                    static function (AuthenticationException $exception) use ($expectedBlankFields): bool {
                        self::assertInstanceOf(BlankCredentialsException::class, $exception);
                        self::assertSame($expectedBlankFields, $exception->getBlankFields());
                        self::assertSame('Bad credentials.', $exception->getMessage());

                        return true;
                    }
                )
            );

        $request = $this->getRequest($session, $username, $password);

        $this->handler->onAuthenticationFailure($request, new BadCredentialsException('Original message'));
    }

    public function testOnAuthenticationFailureTreatsWhitespacePasswordAsFilled(): void
    {
        $session = $this->getSession();
        $session
            ->expects(self::once())
            ->method('set')
            ->with(
                '_security.last_error',
                self::callback(static function (AuthenticationException $exception): bool {
                    self::assertNotInstanceOf(BlankCredentialsException::class, $exception);

                    return true;
                })
            );

        $request = $this->getRequest($session, 'admin', '   ');

        $this->handler->onAuthenticationFailure($request, new BadCredentialsException('Original message'));
    }

    /**
     * @return array<string, array{0: string, 1: string, 2: list<BlankCredentialsException::FIELD_*>}>
     */
    public function dataProviderForBlankCredentials(): array
    {
        return [
            'both fields blank' => [
                '',
                '',
                [BlankCredentialsException::FIELD_USERNAME, BlankCredentialsException::FIELD_PASSWORD],
            ],
            'blank username only' => [
                '',
                'secret',
                [BlankCredentialsException::FIELD_USERNAME],
            ],
            'blank password only' => [
                'admin',
                '',
                [BlankCredentialsException::FIELD_PASSWORD],
            ],
            'whitespace-only username' => [
                '   ',
                'secret',
                [BlankCredentialsException::FIELD_USERNAME],
            ],
        ];
    }

    private function getRequest(?Session $session = null, ?string $username = null, ?string $password = null): Request
    {
        $parameters = [];

        if ($username !== null) {
            $parameters['_username'] = $username;
        }

        if ($password !== null) {
            $parameters['_password'] = $password;
        }

        $request = new Request([], $parameters);
        $request->setSession($session ?? $this->getSession());

        return $request;
    }

    public function getSession(): MockObject&Session
    {
        $session = $this->createMock(Session::class);
        $session->expects(self::any())->method('isStarted')->willReturn(true);

        return $session;
    }
}
