<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\User\Controller;

use Ibexa\Core\MVC\ConfigResolverInterface;
use Ibexa\User\ExceptionHandler\ActionResultHandler;
use Ibexa\User\Form\Data\UserSettingUpdateData;
use Ibexa\User\Form\Factory\FormFactory;
use Ibexa\User\Form\SubmitHandler;
use Ibexa\User\Pagination\Pagerfanta\UserSettingsAdapter;
use Ibexa\User\UserSetting\UserSettingService;
use Ibexa\User\UserSetting\ValueDefinitionRegistry;
use Ibexa\User\View\UserSettings\ListView;
use Ibexa\User\View\UserSettings\UpdateView;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UserSettingsController extends Controller
{
    /** @var \Ibexa\User\Form\Factory\FormFactory */
    private $formFactory;

    /** @var \Ibexa\User\Form\SubmitHandler */
    private $submitHandler;

    /** @var \Ibexa\User\UserSetting\UserSettingService */
    private $userSettingService;

    /** @var \Ibexa\User\UserSetting\ValueDefinitionRegistry */
    private $valueDefinitionRegistry;

    /** @var \Ibexa\User\ExceptionHandler\ActionResultHandler */
    private $actionResultHandler;

    /** @var \Ibexa\Core\MVC\ConfigResolverInterface */
    private $configResolver;

    public function __construct(
        FormFactory $formFactory,
        SubmitHandler $submitHandler,
        UserSettingService $userSettingService,
        ValueDefinitionRegistry $valueDefinitionRegistry,
        ActionResultHandler $actionResultHandler,
        ConfigResolverInterface $configResolver
    ) {
        $this->formFactory = $formFactory;
        $this->submitHandler = $submitHandler;
        $this->userSettingService = $userSettingService;
        $this->valueDefinitionRegistry = $valueDefinitionRegistry;
        $this->actionResultHandler = $actionResultHandler;
        $this->configResolver = $configResolver;
    }

    /**
     * @param int $page
     *
     * @return \Ibexa\User\View\UserSettings\ListView
     *
     * @throws \Ibexa\Core\Base\Exceptions\InvalidArgumentType
     */
    public function listAction(int $page = 1): ListView
    {
        $pagerfanta = new Pagerfanta(
            new UserSettingsAdapter($this->userSettingService)
        );

        $pagerfanta->setMaxPerPage($this->configResolver->getParameter('pagination_user.user_settings_limit'));
        $pagerfanta->setCurrentPage(min($page, $pagerfanta->getNbPages()));

        return new ListView(null, [
            'pager' => $pagerfanta,
            'value_definitions' => $this->valueDefinitionRegistry->getValueDefinitions(),
        ]);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Ibexa\User\View\UserSettings\UpdateView $view
     *
     * @return \Ibexa\User\View\UserSettings\UpdateView|\Symfony\Component\HttpFoundation\Response
     */
    public function updateAction(Request $request, UpdateView $view)
    {
        $userSetting = $view->getUserSetting();

        $data = new UserSettingUpdateData($userSetting->identifier, $userSetting->value);

        $form = $this->formFactory->updateUserSetting($userSetting->identifier, $data);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $result = $this->submitHandler->handle($form, function (UserSettingUpdateData $data) {
                $this->userSettingService->setUserSetting($data->getIdentifier(), $data->getValue());

                $this->actionResultHandler->success(
                    /** @Desc("User setting '%identifier%' updated.") */
                    'user_setting.update.success',
                    ['%identifier%' => $data->getIdentifier()],
                    'user_settings'
                );

                return new RedirectResponse($this->generateUrl('ezplatform.user_settings.list'));
            });

            if ($result instanceof Response) {
                return $result;
            }
        }

        $view->addParameters([
            'form' => $form->createView(),
        ]);

        return $view;
    }
}

class_alias(UserSettingsController::class, 'EzSystems\EzPlatformUserBundle\Controller\UserSettingsController');
