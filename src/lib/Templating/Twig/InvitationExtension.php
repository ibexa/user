<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\User\Templating\Twig;

use Ibexa\Contracts\User\Invitation\Invitation;
use Ibexa\Contracts\User\Invitation\InvitationService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class InvitationExtension extends AbstractExtension
{
    public function __construct(
        private readonly InvitationService $invitationService
    ) {
    }

    #[\Override]
    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'ibexa_is_invitation_expired',
                fn (Invitation $invitation): bool => $this->invitationService->isExpired($invitation)
            ),
        ];
    }
}
