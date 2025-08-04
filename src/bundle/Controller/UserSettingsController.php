<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\User\Controller;

use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\Contracts\User\Controller\AuthenticatedRememberedCheckTrait;
use Ibexa\Contracts\User\Controller\RestrictedControllerInterface;
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

final class UserSettingsController extends Controller implements RestrictedControllerInterface
{
    use AuthenticatedRememberedCheckTrait;

    public function __construct(
        private readonly FormFactory $formFactory,
        private readonly SubmitHandler $submitHandler,
        private readonly UserSettingService $userSettingService,
        private readonly ValueDefinitionRegistry $valueDefinitionRegistry,
        private readonly ActionResultHandler $actionResultHandler,
        private readonly PermissionResolver $permissionResolver
    ) {
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\BadStateException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
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
