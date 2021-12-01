<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\User\Controller;

use Ibexa\Core\MVC\Symfony\Security\Authorization\Attribute;
use Ibexa\User\Form\DataMapper\UserRegisterMapper;
use Ibexa\User\View\Register\ConfirmView;
use Ibexa\User\View\Register\FormView;
use Ibexa\ContentForms\Form\ActionDispatcher\ActionDispatcherInterface;
use Ibexa\User\Form\Type\UserRegisterType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class UserRegisterController extends Controller
{
    /** @var \Ibexa\User\Form\DataMapper\UserRegisterMapper */
    private $userRegisterMapper;

    /** @var \Ibexa\ContentForms\Form\ActionDispatcher\ActionDispatcherInterface */
    private $userActionDispatcher;

    /**
     * @param \Ibexa\User\Form\DataMapper\UserRegisterMapper $userRegisterMapper
     * @param \Ibexa\ContentForms\Form\ActionDispatcher\ActionDispatcherInterface $userActionDispatcher
     */
    public function __construct(
        UserRegisterMapper $userRegisterMapper,
        ActionDispatcherInterface $userActionDispatcher
    ) {
        $this->userRegisterMapper = $userRegisterMapper;
        $this->userActionDispatcher = $userActionDispatcher;
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

        /** @var \Symfony\Component\Form\Form $form */
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
}

class_alias(UserRegisterController::class, 'EzSystems\EzPlatformUserBundle\Controller\UserRegisterController');
