<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\User\Security\Authentication;

use Ibexa\Bundle\User\Security\Exception\BlankCredentialsException;
use Ibexa\Contracts\Core\Repository\Exceptions\PasswordInUnsupportedFormatException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Http\Authentication\DefaultAuthenticationFailureHandler as HttpDefaultAuthenticationFailureHandler;

final class DefaultAuthenticationFailureHandler extends HttpDefaultAuthenticationFailureHandler
{
    private const string USERNAME_PARAMETER = '_username';

    private const string PASSWORD_PARAMETER = '_password';

    #[\Override]
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): Response
    {
        if ($exception instanceof PasswordInUnsupportedFormatException) {
            $resetPasswordUrl = $this->httpUtils->generateUri($request, 'ibexa.user.forgot_password.migration');
            $this->setOptions([
                'failure_path' => $resetPasswordUrl,
            ]);
        }

        if ($exception instanceof BadCredentialsException) {
            $previous = $exception->getPrevious();
            $code = $previous ? $previous->getCode() : 0;
            $blankFields = $this->getBlankCredentialFields($request);

            $exception = $blankFields === []
                ? new BadCredentialsException(
                    'Bad credentials.',
                    $code,
                    $previous
                )
                : new BlankCredentialsException(
                    $blankFields,
                    'Bad credentials.',
                    $code,
                    $previous
                );
        }

        return parent::onAuthenticationFailure($request, $exception);
    }

    /**
     * @return list<BlankCredentialsException::FIELD_*>
     */
    private function getBlankCredentialFields(Request $request): array
    {
        $blankFields = [];

        if (trim((string)$request->request->get(self::USERNAME_PARAMETER, '')) === '') {
            $blankFields[] = BlankCredentialsException::FIELD_USERNAME;
        }

        if ((string)$request->request->get(self::PASSWORD_PARAMETER, '') === '') {
            $blankFields[] = BlankCredentialsException::FIELD_PASSWORD;
        }

        return $blankFields;
    }
}
