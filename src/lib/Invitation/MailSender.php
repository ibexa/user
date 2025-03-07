<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\User\Invitation;

use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Contracts\User\Invitation\Invitation;
use Ibexa\Contracts\User\Invitation\InvitationSender;
use Swift_Mailer;
use Swift_Message;
use Swift_Image;
use Twig\Environment;

final class MailSender implements InvitationSender
{
    private Environment $twig;

    private ConfigResolverInterface $configResolver;

    private Swift_Mailer $mailer;

    public function __construct(
        Environment $twig,
        ConfigResolverInterface $configResolver,
        Swift_Mailer $mailer
    ) {
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
                $invitation->getSiteAccessIdentifier()
            )
        );

        $senderAddress = $this->configResolver->hasParameter('sender_address', 'swiftmailer.mailer')
            ? $this->configResolver->getParameter('sender_address', 'swiftmailer.mailer')
            : '';

        $subject = $template->renderBlock('subject', []);
        $from = $template->renderBlock('from', []) ?: $senderAddress;
        
        $message = (new Swift_Message())
            ->setSubject($subject)
            ->setTo($invitation->getEmail());

        $mailImagesDir = dirname(__DIR__, 6) . '/public/bundles/ibexaadminui/img/mail/';
        $embeddedHeader = $message->embed(Swift_Image::fromPath($mailImagesDir . 'header.jpg'));
        $embeddedBtnPrimaryLeftSide = $message->embed(Swift_Image::fromPath($mailImagesDir .'btn_primary_left_side.jpg'));
        $embeddedBtnPrimaryRightSide = $message->embed(Swift_Image::fromPath($mailImagesDir .'btn_primary_right_side.jpg'));

        $body = $template->renderBlock('body', [
            'invite_hash' => $invitation->getHash(),
            'siteaccess' => $invitation->getSiteAccessIdentifier(),
            'invitation' => $invitation,
            'header_path' => $embeddedHeader,
            'btn_primary_left_side' => $embeddedBtnPrimaryLeftSide,
            'btn_primary_right_side' => $embeddedBtnPrimaryRightSide,
        ]);

        $message->setBody($body, 'text/html');

        if (empty($from) === false) {
            $message->setFrom($from);
        }

        $this->mailer->send($message);
    }
}
