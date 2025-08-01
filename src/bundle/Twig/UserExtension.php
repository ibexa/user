<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\User\Twig;

use Twig\DeprecatedCallableInfo;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class UserExtension extends AbstractExtension
{
    #[\Override]
    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'ibexa_user_get_current',
                [UserRuntime::class, 'getCurrentUser'],
                [
                    'deprecation_info' => new DeprecatedCallableInfo('ibexa/user', '4.6', 'ibexa_current_user'),
                ]
            ),
        ];
    }
}
