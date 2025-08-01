<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\User\Invitation;

use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Contracts\Notifications\Service\NotificationServiceInterface;
use Ibexa\Contracts\Notifications\Value\Notification\SymfonyNotificationAdapter;
use Ibexa\Contracts\Notifications\Value\Recipent\SymfonyRecipientAdapter;
use Ibexa\Contracts\User\Invitation\Invitation;
use Ibexa\Contracts\User\Invitation\InvitationSender;
use Ibexa\Contracts\User\Notification\UserInvitation;
use Symfony\Component\Notifier\Recipient\Recipient;
use Twig\Environment;

final readonly class MailSender implements InvitationSender
{
    public function __construct(
        private Environment $twig,
        private ConfigResolverInterface $configResolver,
        private NotificationServiceInterface $notificationService
    ) {
    }

    public function sendInvitation(Invitation $invitation): void
    {
        $this->notificationService->send(
            new SymfonyNotificationAdapter(
                new UserInvitation($invitation, $this->configResolver, $this->twig),
            ),
            [new SymfonyRecipientAdapter(new Recipient($invitation->getEmail()))],
        );
    }
}
