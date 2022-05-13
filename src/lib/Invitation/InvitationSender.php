<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\User\Invitation;

use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Contracts\User\Invitation\Invitation;
use Swift_Mailer;
use Swift_Message;
use Twig\Environment;

final class InvitationSender implements \Ibexa\Contracts\User\Invitation\InvitationSender
{
    private Environment $twig;

    private ConfigResolverInterface $configResolver;

    private Swift_Mailer $mailer;

    public function __construct(Environment $twig, ConfigResolverInterface $configResolver, Swift_Mailer $mailer)
    {
        $this->twig = $twig;
        $this->configResolver = $configResolver;
        $this->mailer = $mailer;
    }

    public function sendInvitation(Invitation $invitation): void
    {
        $template = $this->twig->load(
            $this->configResolver->getParameter(
                'user_invitation.templates.mail',
                null,
                $invitation->getSiteAccess()->name
            )
        );

        $senderAddress = $this->configResolver->hasParameter('sender_address', 'swiftmailer.mailer')
            ? $this->configResolver->getParameter('sender_address', 'swiftmailer.mailer')
            : '';

        $subject = $template->renderBlock('subject', []);
        $from = $template->renderBlock('from', []) ?: $senderAddress;
        $body = $template->renderBlock('body', [
            'invite_hash' => $invitation->getHash(),
            'siteaccess' => $invitation->getSiteAccess()->name,
            'invitation' => $invitation,
        ]);

        $message = (new Swift_Message())
            ->setSubject($subject)
            ->setTo($invitation->getEmail())
            ->setBody($body, 'text/html');

        if (empty($from) === false) {
            $message->setFrom($from);
        }

        $this->mailer->send($message);
    }
}
