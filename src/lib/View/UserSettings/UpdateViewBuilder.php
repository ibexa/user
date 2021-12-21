<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\User\View\UserSettings;

use Ibexa\Core\MVC\Symfony\View\Builder\ViewBuilder;
use Ibexa\Core\MVC\Symfony\View\Configurator;
use Ibexa\Core\MVC\Symfony\View\ParametersInjector;
use Ibexa\User\UserSetting\UserSettingService;

class UpdateViewBuilder implements ViewBuilder
{
    /** @var \Ibexa\User\UserSetting\UserSettingService */
    private $userSettingService;

    /** @var \Ibexa\Core\MVC\Symfony\View\Configurator */
    private $viewConfigurator;

    /** @var \Ibexa\Core\MVC\Symfony\View\ParametersInjector */
    private $viewParametersInjector;

    /**
     * @param \Ibexa\User\UserSetting\UserSettingService $userSettingService
     * @param \Ibexa\Core\MVC\Symfony\View\Configurator $viewConfigurator
     * @param \Ibexa\Core\MVC\Symfony\View\ParametersInjector $viewParametersInjector
     */
    public function __construct(
        UserSettingService $userSettingService,
        Configurator $viewConfigurator,
        ParametersInjector $viewParametersInjector
    ) {
        $this->userSettingService = $userSettingService;
        $this->viewConfigurator = $viewConfigurator;
        $this->viewParametersInjector = $viewParametersInjector;
    }

    /**
     * {@inheritdoc}
     */
    public function matches($argument): bool
    {
        return 'Ibexa\Bundle\User\Controller\UserSettingsController::updateGroupAction' === $argument ||
                    'Ibexa\Bundle\User\Controller\UserSettingsController::updateAction' === $argument;

    }

    /**
     * {@inheritdoc}
     */
    public function buildView(array $parameters): UpdateView
    {
        $view = new UpdateView();

//        $view->setUserSetting($this->userSettingService->getUserSetting($parameters['identifier']));
        $view->setUserSettingGroup($this->userSettingService->getUserSettingGroup($parameters['identifier']));
        $this->viewParametersInjector->injectViewParameters($view, $parameters);
        $this->viewConfigurator->configure($view);

        return $view;
    }
}

class_alias(UpdateViewBuilder::class, 'EzSystems\EzPlatformUser\View\UserSettings\UpdateViewBuilder');
