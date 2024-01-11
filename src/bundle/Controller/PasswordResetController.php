<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\User\Controller;

use DateInterval;
use DateTime;
use Ibexa\Bundle\User\Type\UserForgotPasswordReason;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\Contracts\Core\Repository\UserService;
use Ibexa\Contracts\Core\Repository\Values\User\User;
use Ibexa\Contracts\Core\Repository\Values\User\UserTokenUpdateStruct;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Contracts\Notifications\Service\NotificationServiceInterface;
use Ibexa\Contracts\Notifications\Value\Notification\SymfonyNotificationAdapter;
use Ibexa\Contracts\Notifications\Value\Recipent\SymfonyRecipientAdapter;
use Ibexa\Contracts\Notifications\Value\Recipent\UserRecipient;
use Ibexa\Contracts\User\Notification\UserPasswordReset;
use Ibexa\User\ExceptionHandler\ActionResultHandler;
use Ibexa\User\Form\Data\UserPasswordResetData;
use Ibexa\User\Form\Factory\FormFactory;
use Ibexa\User\View\ForgotPassword\FormView;
use Ibexa\User\View\ForgotPassword\LoginView;
use Ibexa\User\View\ForgotPassword\SuccessView;
use Ibexa\User\View\ResetPassword\FormView as UserResetPasswordFormView;
use Ibexa\User\View\ResetPassword\InvalidLinkView;
use Ibexa\User\View\ResetPassword\SuccessView as UserResetPasswordSuccessView;
use Swift_Mailer;
use Swift_Message;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class PasswordResetController extends Controller
{
    private FormFactory $formFactory;

    private UserService $userService;

    private Swift_Mailer $mailer;

    private Environment $twig;

    private ActionResultHandler $actionResultHandler;

    private PermissionResolver $permissionResolver;

    private ConfigResolverInterface $configResolver;

    private NotificationServiceInterface $notificationService;

    public function __construct(
        FormFactory $formFactory,
        UserService $userService,
        Swift_Mailer $mailer,
        Environment $twig,
        ActionResultHandler $actionResultHandler,
        PermissionResolver $permissionResolver,
        ConfigResolverInterface $configResolver,
        NotificationServiceInterface $notificationService
    ) {
        $this->formFactory = $formFactory;
        $this->userService = $userService;
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->actionResultHandler = $actionResultHandler;
        $this->permissionResolver = $permissionResolver;
        $this->configResolver = $configResolver;
        $this->notificationService = $notificationService;
    }

    /**
     * @return \Ibexa\User\View\ForgotPassword\FormView|\Ibexa\User\View\ForgotPassword\SuccessView|\Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @throws \Ibexa\Core\Base\Exceptions\InvalidArgumentType
     */
    public function userForgotPasswordAction(Request $request, ?string $reason = null)
    {
        $form = $this->formFactory->forgotUserPassword();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $users = $this->userService->loadUsersByEmail($data->getEmail());

            /** Because is is possible to have multiple user accounts with same email address we must gain a user login. */
            if (\count($users) > 1) {
                return $this->redirectToRoute('ibexa.user.forgot_password.login');
            }

            if (!empty($users)) {
                $user = reset($users);
                $token = $this->updateUserToken($user);

                $this->sendResetPasswordMessage($user, $token);
            }

            return new SuccessView(null);
        }

        return new FormView(null, [
            'form_forgot_user_password' => $form->createView(),
            'reason' => $reason,
            'userForgotPasswordReasonMigration' => UserForgotPasswordReason::MIGRATION,
        ]);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Ibexa\User\View\ForgotPassword\LoginView|\Ibexa\User\View\ForgotPassword\SuccessView
     *
     * @throws \Ibexa\Core\Base\Exceptions\InvalidArgumentType
     */
    public function userForgotPasswordLoginAction(Request $request)
    {
        $form = $this->formFactory->forgotUserPasswordWithLogin();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            try {
                $user = $this->userService->loadUserByLogin($data->getLogin());
            } catch (NotFoundException $e) {
                $user = null;
            }

            if (!$user || \count($this->userService->loadUsersByEmail($user->email)) < 2) {
                return new SuccessView(null);
            }

            $token = $this->updateUserToken($user);
            $this->sendResetPasswordMessage($user, $token);

            return new SuccessView(null);
        }

        return new LoginView(null, [
            'form_forgot_user_password_with_login' => $form->createView(),
        ]);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param string $hashKey
     *
     * @return \Ibexa\User\View\ResetPassword\FormView|\Ibexa\User\View\ResetPassword\InvalidLinkView|\Ibexa\User\View\ResetPassword\SuccessView
     *
     * @throws \Ibexa\Core\Base\Exceptions\InvalidArgumentType
     */
    public function userResetPasswordAction(Request $request, string $hashKey)
    {
        $response = new Response();
        $response->headers->set('X-Robots-Tag', 'noindex');

        try {
            $user = $this->userService->loadUserByToken($hashKey);
        } catch (NotFoundException $e) {
            $view = new InvalidLinkView(null);
            $view->setResponse($response);

            return $view;
        }
        $userPasswordResetData = new UserPasswordResetData();
        $form = $this->formFactory->resetUserPassword(
            $userPasswordResetData,
            null,
            $user->getContentType(),
            $user
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $currentUser = $this->permissionResolver->getCurrentUserReference();
                $this->permissionResolver->setCurrentUserReference($user);
            } catch (NotFoundException $e) {
                $view = new InvalidLinkView(null);
                $view->setResponse($response);

                return $view;
            }

            $data = $form->getData();

            try {
                $this->userService->updateUserPassword($user, $data->getNewPassword());
                $this->userService->expireUserToken($hashKey);
                $this->permissionResolver->setCurrentUserReference($currentUser);

                $view = new UserResetPasswordSuccessView(null);
                $view->setResponse($response);

                return $view;
            } catch (\Exception $e) {
                $this->actionResultHandler->error($e->getMessage());
            }
        }

        $view = new UserResetPasswordFormView(null, [
            'form_reset_user_password' => $form->createView(),
        ]);
        $view->setResponse($response);

        return $view;
    }

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\User\User $user
     *
     * @return string
     *
     * @throws \Exception
     */
    private function updateUserToken(User $user): string
    {
        $struct = new UserTokenUpdateStruct();
        $struct->hashKey = bin2hex(random_bytes(16));
        $date = new DateTime();
        $date->add(new DateInterval($this->configResolver->getParameter('security.token_interval_spec')));
        $struct->time = $date;
        $this->userService->updateUserToken($user, $struct);

        return $struct->hashKey;
    }

    private function sendResetPasswordMessage(User $user, string $hashKey): void
    {
        if ($this->isNotifierConfigured()) {
            $this->sendNotification($user, $hashKey);

            return;
        }

        // Swiftmailer delivery has to be kept to maintain backwards compatibility
        $template = $this->twig->load($this->configResolver->getParameter('user_forgot_password.templates.mail'));

        $senderAddress = $this->configResolver->hasParameter('sender_address', 'swiftmailer.mailer')
            ? $this->configResolver->getParameter('sender_address', 'swiftmailer.mailer')
            : '';

        $subject = $template->renderBlock('subject', []);
        $from = $template->renderBlock('from', []) ?: $senderAddress;
        $body = $template->renderBlock('body', ['hash_key' => $hashKey]);

        $message = (new Swift_Message())
            ->setSubject($subject)
            ->setTo($user->email)
            ->setBody($body, 'text/html');

        if (empty($from) === false) {
            $message->setFrom($from);
        }

        $this->mailer->send($message);
    }

    private function sendNotification($user, string $token): void
    {
        $this->notificationService->send(
            new SymfonyNotificationAdapter(
                new UserPasswordReset($user, $token),
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

class_alias(PasswordResetController::class, 'EzSystems\EzPlatformUserBundle\Controller\PasswordResetController');
