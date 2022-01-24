<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\User\Security\Authentication;

use Ibexa\Contracts\Core\Repository\Exceptions\PasswordInUnsupportedFormatException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authentication\DefaultAuthenticationFailureHandler as HttpDefaultAuthenticationFailureHandler;

final class DefaultAuthenticationFailureHandler extends HttpDefaultAuthenticationFailureHandler
{
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        if ($exception instanceof PasswordInUnsupportedFormatException) {
            $resetPasswordUrl = $this->httpUtils->generateUri($request, 'ibexa.user.forgot_password.migration');
            $this->setOptions([
                'failure_path' => $resetPasswordUrl,
            ]);
        }

        return parent::onAuthenticationFailure($request, $exception);
    }
}

class_alias(DefaultAuthenticationFailureHandler::class, 'EzSystems\EzPlatformUserBundle\Security\Authentication\DefaultAuthenticationFailureHandler');
