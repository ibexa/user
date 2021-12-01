<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\User\Controller;

use Ibexa\Contracts\Core\Repository\UserService;
use Ibexa\Core\MVC\Symfony\SiteAccess;
use Ibexa\User\ExceptionHandler\ActionResultHandler;
use Ibexa\User\Form\Factory\FormFactory;
use Ibexa\User\View\ChangePassword\FormView;
use Ibexa\User\View\ChangePassword\SuccessView;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Exception;

class PasswordChangeController extends Controller
{
    /** @var \Ibexa\User\ExceptionHandler\ActionResultHandler */
    private $actionResultHandler;

    /** @var \Ibexa\Contracts\Core\Repository\UserService */
    private $userService;

    /** @var \Ibexa\User\Form\Factory\FormFactory */
    private $formFactory;

    /** @var \Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface */
    private $tokenStorage;

    /** @var array */
    private $siteAccessGroups;

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
    public function userPasswordChangeAction(Request $request)
    {
        /** @var \Ibexa\Contracts\Core\Repository\Values\User\User $user */
        $user = $this->tokenStorage->getToken()->getUser()->getAPIUser();
        $form = $this->formFactory->changeUserPassword($user->getContentType());
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
                        'change_password'
                    );

                    return new RedirectResponse($this->generateUrl('ezplatform.dashboard'));
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

class_alias(PasswordChangeController::class, 'EzSystems\EzPlatformUserBundle\Controller\PasswordChangeController');
