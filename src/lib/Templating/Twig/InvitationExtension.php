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
    private InvitationService $invitationService;

    public function __construct(
        InvitationService $invitationService
    ) {
        $this->invitationService = $invitationService;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'ibexa_is_invitation_expired',
                fn (Invitation $invitation) => $this->invitationService->isExpired($invitation)
            ),
        ];
    }
}
