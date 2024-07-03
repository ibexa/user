<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\User\Notification;

use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Contracts\User\Invitation\Invitation;
use Symfony\Bridge\Twig\Mime\NotificationEmail;
use Symfony\Component\Notifier\Message\EmailMessage;
use Symfony\Component\Notifier\Message\SmsMessage;
use Symfony\Component\Notifier\Notification\EmailNotificationInterface;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\Notification\SmsNotificationInterface;
use Symfony\Component\Notifier\Recipient\EmailRecipientInterface;
use Symfony\Component\Notifier\Recipient\SmsRecipientInterface;
use Twig\Environment;

final class UserInvitation extends Notification implements EmailNotificationInterface, SmsNotificationInterface
{
    private Invitation $invitation;

    private ConfigResolverInterface $configResolver;

    private Environment $twig;

    public function __construct(
        Invitation $invitation,
        ConfigResolverInterface $configResolver,
        Environment $twig
    ) {
        parent::__construct();

        $this->configResolver = $configResolver;
        $this->twig = $twig;
        $this->invitation = $invitation;
    }

    public function asEmailMessage(EmailRecipientInterface $recipient, string $transport = null): ?EmailMessage
    {
        $templatePath = $this->twig->load(
            $this->configResolver->getParameter(
                'user_invitation.templates.mail',
                null,
                $this->invitation->getSiteAccessIdentifier()
            )
        );

        $template = $this->twig->load($templatePath);

        $subject = $template->renderBlock('subject');
        $from = $template->renderBlock('from') ?: null;
        $body = $template->renderBlock('body', [
            'invite_hash' => $this->invitation->getHash(),
            'siteaccess' => $this->invitation->getSiteAccessIdentifier(),
            'invitation' => $this->invitation,
        ]);

        $email = NotificationEmail::asPublicEmail()
            ->html($body)
            ->to($recipient->getEmail())
            ->subject($subject)
        ;

        if ($from !== null) {
            $email->from($from);
        }

        return new EmailMessage($email);
    }

    public function asSmsMessage(SmsRecipientInterface $recipient, string $transport = null): ?SmsMessage
    {
        return null;
    }
}
