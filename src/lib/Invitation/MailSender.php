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

final class MailSender implements InvitationSender
{
    private Environment $twig;

    private ConfigResolverInterface $configResolver;

    private NotificationServiceInterface $notificationService;

    public function __construct(
        Environment $twig,
        ConfigResolverInterface $configResolver,
        NotificationServiceInterface $notificationService
    ) {
        $this->twig = $twig;
        $this->configResolver = $configResolver;
        $this->notificationService = $notificationService;
    }

    public function sendInvitation(Invitation $invitation): void
    {
        if (!$this->isNotifierConfigured()) {
            return;
        }

        $this->notificationService->send(
            new SymfonyNotificationAdapter(
                new UserInvitation($invitation, $this->configResolver, $this->twig),
            ),
            [new SymfonyRecipientAdapter(new Recipient($invitation->getEmail()))],
        );
    }

    private function isNotifierConfigured(): bool
    {
        $subscriptions = $this->configResolver->getParameter('notifications.subscriptions');

        return array_key_exists(UserInvitation::class, $subscriptions)
            && !empty($subscriptions[UserInvitation::class]['channels']);
    }
}
