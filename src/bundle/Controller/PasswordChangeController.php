<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\User\Controller;

use Exception;
use Ibexa\Contracts\Core\Repository\UserService;
use Ibexa\Contracts\User\Controller\AccessCheckController;
use Ibexa\Contracts\User\Controller\AuthenticatedRememberedCheckTrait;
use Ibexa\Core\MVC\Symfony\SiteAccess;
use Ibexa\User\ExceptionHandler\ActionResultHandler;
use Ibexa\User\Form\Factory\FormFactory;
use Ibexa\User\View\ChangePassword\FormView;
use Ibexa\User\View\ChangePassword\SuccessView;
use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class PasswordChangeController extends Controller implements AccessCheckController
{
    use AuthenticatedRememberedCheckTrait;

    private ActionResultHandler $actionResultHandler;

    private UserService $userService;

    private FormFactory $formFactory;

    private TokenStorageInterface $tokenStorage;

    private array $siteAccessGroups;

    public function __construct(
        ActionResultHandler $actionResultHandler,
        UserService $userService,
        FormFactory $formFactory,
        TokenStorageInterface $tokenStorage,
        array $siteAccessGroups
    ) {
        $this->actionResultHandler = $actionResultHandler;
        $this->userService = $userService;
        $this->formFactory = $formFactory;
        $this->tokenStorage = $tokenStorage;
        $this->siteAccessGroups = $siteAccessGroups;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Ibexa\User\View\ChangePassword\FormView|\Ibexa\User\View\ChangePassword\SuccessView|\Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @throws \Ibexa\Core\Base\Exceptions\InvalidArgumentType
     */
    public function userPasswordChangeAction(Request $request): RedirectResponse|SuccessView|FormView
    {
        /** @var \Ibexa\Contracts\Core\Repository\Values\User\User $user */
        $user = $this->tokenStorage->getToken()->getUser()->getAPIUser();
        $form = $this->formFactory->changeUserPassword($user->getContentType(), null, null, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            try {
                $this->userService->updateUserPassword($user, $data->getNewPassword());

                if ($this->isInAdminGroup($request->attributes->get('siteaccess'))) {
                    $this->actionResultHandler->success(
                        /** @Desc("Your password has been successfully changed.") */
                        'ezplatform.change_password.success',
                        [],
                        'ibexa_change_password'
                    );

                    return $this->redirectToRoute('ibexa.user_settings.list', [
                        '_fragment' => 'ibexa-tab-my-account-settings',
                    ]);
                }

                return new SuccessView(null);
            } catch (Exception $e) {
                $this->actionResultHandler->error($e->getMessage());
            }
        }

        return new FormView(null, [
            'form_change_user_password' => $form->createView(),
        ]);
    }

    private function isInAdminGroup(SiteAccess $siteAccess): bool
    {
        return in_array($siteAccess->name, $this->siteAccessGroups['admin_group'], true);
    }
}
