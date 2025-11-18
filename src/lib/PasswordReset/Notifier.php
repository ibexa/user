<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\User\PasswordReset;

use Ibexa\Contracts\Core\Repository\Values\User\User;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Contracts\Notifications\Service\NotificationServiceInterface;
use Ibexa\Contracts\Notifications\Value\Notification\SymfonyNotificationAdapter;
use Ibexa\Contracts\Notifications\Value\Recipent\SymfonyRecipientAdapter;
use Ibexa\Contracts\Notifications\Value\Recipent\UserRecipient;
use Ibexa\Contracts\User\Notification\UserPasswordReset;
use Ibexa\Contracts\User\PasswordReset\NotifierInterface;
use Twig\Environment;

final class Notifier implements NotifierInterface
{
    private ConfigResolverInterface $configResolver;

    private Environment $twig;

    private NotificationServiceInterface $notificationService;

    public function __construct(
        ConfigResolverInterface $configResolver,
        Environment $twig,
        NotificationServiceInterface $notificationService
    ) {
        $this->configResolver = $configResolver;
        $this->twig = $twig;
        $this->notificationService = $notificationService;
    }

    public function sendMessage(User $user, string $hashKey): void
    {
        if ($this->isNotifierConfigured()) {
            $this->sendNotification($user, $hashKey);
        }
    }

    private function sendNotification(User $user, string $token): void
    {
        $this->notificationService->send(
            new SymfonyNotificationAdapter(
                new UserPasswordReset($user, $token, $this->configResolver, $this->twig),
            ),
            [new SymfonyRecipientAdapter(new UserRecipient($user))],
        );
    }

    private function isNotifierConfigured(): bool
    {
        $subscriptions = $this->configResolver->getParameter('notifications.subscriptions');

        return array_key_exists(UserPasswordReset::class, $subscriptions)
            && !empty($subscriptions[UserPasswordReset::class]['channels']);
    }
}
