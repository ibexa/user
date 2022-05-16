<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\User\Controller;

use Ibexa\ContentForms\Form\ActionDispatcher\ActionDispatcherInterface;
use Ibexa\Contracts\User\Invitation\InvitationService;
use Ibexa\Core\MVC\Symfony\Security\Authorization\Attribute;
use Ibexa\User\Form\DataMapper\UserRegisterMapper;
use Ibexa\User\Form\Type\UserRegisterType;
use Ibexa\User\View\Register\ConfirmView;
use Ibexa\User\View\Register\FormView;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class UserRegisterController extends Controller
{
    /** @var \Ibexa\User\Form\DataMapper\UserRegisterMapper */
    private $userRegisterMapper;

    /** @var \Ibexa\ContentForms\Form\ActionDispatcher\ActionDispatcherInterface */
    private $userActionDispatcher;

    private InvitationService $invitationService;

    public function __construct(
        UserRegisterMapper $userRegisterMapper,
        ActionDispatcherInterface $userActionDispatcher,
        InvitationService $invitationService
    ) {
        $this->userRegisterMapper = $userRegisterMapper;
        $this->userActionDispatcher = $userActionDispatcher;
        $this->invitationService = $invitationService;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Ibexa\User\View\Register\FormView|\Symfony\Component\HttpFoundation\Response|null
     *
     * @throws \Ibexa\Core\Base\Exceptions\InvalidArgumentType
     */
    public function registerAction(Request $request)
    {
        if (!$this->isGranted(new Attribute('user', 'register'))) {
            throw new UnauthorizedHttpException('You are not allowed to register a new account');
        }

        $data = $this->userRegisterMapper->mapToFormData();
        $language = $data->mainLanguageCode;

        $form = $this->createForm(
            UserRegisterType::class,
            $data,
            ['languageCode' => $language, 'mainLanguageCode' => $language]
        );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && null !== $form->getClickedButton()) {
            $this->userActionDispatcher->dispatchFormAction($form, $data, $form->getClickedButton()->getName());
            if ($response = $this->userActionDispatcher->getResponse()) {
                return $response;
            }
        }

        return new FormView(null, ['form' => $form->createView()]);
    }

    /**
     * @return \Ibexa\User\View\Register\ConfirmView
     *
     * @throws \Ibexa\Core\Base\Exceptions\InvalidArgumentType
     */
    public function registerConfirmAction(): ConfirmView
    {
        return new ConfirmView();
    }

    public function registerFromInvitationAction(Request $request)
    {
        $invite = $this->invitationService->getInvitation($request->get('inviteHash'));

        if (!$this->invitationService->isValid($invite)) {
            throw new UnauthorizedHttpException('You are not allowed to register a new account');
        }

        $this->userRegisterMapper->setParam('invitation', $invite);
        $data = $this->userRegisterMapper->mapToFormData();
        $language = $data->mainLanguageCode;

        $form = $this->createForm(
            UserRegisterType::class,
            $data,
            [
                'languageCode' => $language,
                'mainLanguageCode' => $language,
                'intent' => 'invitation',
            ]
        );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && null !== $form->getClickedButton()) {
            $this->userActionDispatcher->dispatchFormAction($form, $data, $form->getClickedButton()->getName());
            if ($response = $this->userActionDispatcher->getResponse()) {
                $this->invitationService->markAsUsed($invite);

                return $response;
            }
        }

        return new FormView(null, ['form' => $form->createView()]);
    }
}

class_alias(UserRegisterController::class, 'EzSystems\EzPlatformUserBundle\Controller\UserRegisterController');
