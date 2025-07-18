<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\User\EventListener;

use Ibexa\ContentForms\User\View\UserUpdateView;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Core\MVC\Symfony\Event\PreContentViewEvent;
use Ibexa\Core\MVC\Symfony\MVCEvents;
use Ibexa\User\View;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ViewTemplatesListener implements EventSubscriberInterface
{
    public function __construct(
        private readonly ConfigResolverInterface $configResolver
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [MVCEvents::PRE_CONTENT_VIEW => [
            ['setViewTemplates', 0],
            ['setUserEditViewTemplateParameters', 0],
        ]];
    }

    public function setUserEditViewTemplateParameters(PreContentViewEvent $event): void
    {
        $contentView = $event->getContentView();

        if (!$contentView instanceof UserUpdateView) {
            return;
        }

        $user = $contentView->getParameter('user');
        $isPublished = null !== $user->contentInfo->mainLocationId && $user->contentInfo->published;

        $contentView->addParameters([
            'is_published' => $isPublished,
        ]);
    }

    /**
     * If the event's view has a defined template, sets the view's template identifier,
     * and the 'page_layout' parameter.
     */
    public function setViewTemplates(PreContentViewEvent $event): void
    {
        $view = $event->getContentView();
        $pageLayout = $this->configResolver->getParameter('page_layout');

        foreach ($this->getTemplatesMap() as $viewClass => $template) {
            if ($view instanceof $viewClass) {
                $view->setTemplateIdentifier($template);
                $view->addParameters(['page_layout' => $pageLayout]);
            }
        }
    }

    /**
     * @return string[]
     */
    private function getTemplatesMap(): array
    {
        return [
            View\ChangePassword\FormView::class => $this->configResolver->getParameter('user_change_password.templates.form'),
            View\ChangePassword\SuccessView::class => $this->configResolver->getParameter('user_change_password.templates.success'),
            View\ForgotPassword\FormView::class => $this->configResolver->getParameter('user_forgot_password.templates.form'),
            View\ForgotPassword\SuccessView::class => $this->configResolver->getParameter('user_forgot_password_success.templates.form'),
            View\ForgotPassword\LoginView::class => $this->configResolver->getParameter('user_forgot_password_login.templates.form'),
            View\ResetPassword\FormView::class => $this->configResolver->getParameter('user_reset_password.templates.form'),
            View\ResetPassword\InvalidLinkView::class => $this->configResolver->getParameter('user_reset_password.templates.invalid_link'),
            View\ResetPassword\SuccessView::class => $this->configResolver->getParameter('user_reset_password.templates.success'),
            View\UserSettings\ListView::class => $this->configResolver->getParameter('user_settings.templates.list'),
            View\Register\FormView::class => $this->configResolver->getParameter('user_registration.templates.form'),
            View\Register\ConfirmView::class => $this->configResolver->getParameter('user_registration.templates.confirmation'),
            View\Invitation\FormView::class => $this->configResolver->getParameter('user_invitation.templates.form'),
        ];
    }
}
