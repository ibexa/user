<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\User\Notification;

use Ibexa\Contracts\Core\Repository\Values\User\User;
use Symfony\Component\Notifier\Message\EmailMessage;
use Symfony\Component\Notifier\Notification\EmailNotificationInterface;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\Recipient\EmailRecipientInterface;

final class UserRegister extends Notification implements EmailNotificationInterface, UserAwareNotificationInterface
{
    public function __construct(
        private readonly User $user
    ) {
        parent::__construct();
    }

    public function asEmailMessage(EmailRecipientInterface $recipient, ?string $transport = null): ?EmailMessage
    {
        return null;
    }

    public function getUser(): User
    {
        return $this->user;
    }
}
