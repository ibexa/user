<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\User\Controller;

use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\User\ExceptionHandler\ActionResultHandler;
use Ibexa\User\Form\Data\UserSettingUpdateData;
use Ibexa\User\Form\Factory\FormFactory;
use Ibexa\User\Form\SubmitHandler;
use Ibexa\User\UserSetting\UserSettingService;
use Ibexa\User\UserSetting\ValueDefinitionRegistry;
use Ibexa\User\View\UserSettings\ListView;
use Ibexa\User\View\UserSettings\UpdateView;
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

    /** @var \Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface */
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
        return new ListView(null, [
            'grouped_settings' => $this->userSettingService->loadGroupedUserSettings(),
            'value_definitions' => $this->valueDefinitionRegistry->getValueDefinitions(),
        ]);
    }

    public function updateAction(Request $request, UpdateView $view)
    {
        $userSettingGroup = $view->getUserSettingGroup();

        $values = [];
        foreach ($userSettingGroup->getSettings() as $setting) {
            $values[$setting->identifier] = ['value' => $setting->value];
        }

        $data = new UserSettingUpdateData($userSettingGroup->getIdentifier(), $values);
        $form = $this->formFactory->updateUserSetting($userSettingGroup->getIdentifier(), $data);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $result = $this->submitHandler->handle($form, function (UserSettingUpdateData $data) {
                foreach ($data->getValues() as $identifier => $value) {
                    $this->userSettingService->setUserSetting($identifier, (string)$value['value']);
                }

                $this->actionResultHandler->success(
                    /** @Desc("User settings '%identifier%' updated.") */
                    'user_setting.update.success',
                    ['%identifier%' => $data->getIdentifier()],
                    'user_settings'
                );

                return new RedirectResponse($this->generateUrl('ibexa.user_settings.list'));
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
