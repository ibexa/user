<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\User\Controller;

use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\User\ExceptionHandler\ActionResultHandler;
use Ibexa\User\Form\Data\UserSettingUpdateData;
use Ibexa\User\Form\Factory\FormFactory;
use Ibexa\User\Form\SubmitHandler;
use Ibexa\User\Form\Type\UserSettingUpdateType;
use Ibexa\User\UserSetting\UserSettingService;
use Ibexa\User\UserSetting\ValueDefinitionRegistry;
use Ibexa\User\View\UserSettings\ListView;
use Ibexa\User\View\UserSettings\UpdateView;
use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Component\Form\Button;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UserSettingsController extends Controller
{
    private FormFactory $formFactory;

    private SubmitHandler $submitHandler;

    private UserSettingService $userSettingService;

    private ValueDefinitionRegistry $valueDefinitionRegistry;

    private ActionResultHandler $actionResultHandler;

    private PermissionResolver $permissionResolver;

    public function __construct(
        FormFactory $formFactory,
        SubmitHandler $submitHandler,
        UserSettingService $userSettingService,
        ValueDefinitionRegistry $valueDefinitionRegistry,
        ActionResultHandler $actionResultHandler,
        PermissionResolver $permissionResolver
    ) {
        $this->formFactory = $formFactory;
        $this->submitHandler = $submitHandler;
        $this->userSettingService = $userSettingService;
        $this->valueDefinitionRegistry = $valueDefinitionRegistry;
        $this->actionResultHandler = $actionResultHandler;
        $this->permissionResolver = $permissionResolver;
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
        $user = $this->getUser()->getAPIUser();
        $canChangePassword = $this->permissionResolver->canUser('user', 'password', $user);

        return new ListView(null, [
            'grouped_settings' => $this->userSettingService->loadGroupedUserSettings(),
            'value_definitions' => $this->valueDefinitionRegistry->getValueDefinitions(),
            'can_change_password' => $canChangePassword,
        ]);
    }

    public function updateAction(Request $request, UpdateView $view): Response|UpdateView
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
            $result = $this->submitHandler->handle($form, function (UserSettingUpdateData $data) use ($form): RedirectResponse {
                foreach ($data->getValues() as $identifier => $value) {
                    $this->userSettingService->setUserSetting($identifier, (string)$value['value']);
                }

                $this->actionResultHandler->success(
                    /** @Desc("User settings '%identifier%' updated.") */
                    'user_setting.update.success',
                    ['%identifier%' => $data->getIdentifier()],
                    'ibexa_user_settings'
                );

                if ($form->getClickedButton() instanceof Button
                    && $form->getClickedButton()->getName() === UserSettingUpdateType::BTN_UPDATE_AND_EDIT
                ) {
                    return $this->redirectToRoute('ibexa.user_settings.update', [
                        'identifier' => $data->getIdentifier(),
                    ]);
                }

                return new RedirectResponse($this->generateUrl('ibexa.user_settings.list'));
            });

            if ($result instanceof Response) {
                return $result;
            }
        }

        $view->addParameters([
            'form' => $form->createView(),
            'title' => $userSettingGroup->getName(),
        ]);

        return $view;
    }
}
