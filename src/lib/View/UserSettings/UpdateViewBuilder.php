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
    public function __construct(
        private readonly UserSettingService $userSettingService,
        private readonly Configurator $viewConfigurator,
        private readonly ParametersInjector $viewParametersInjector
    ) {
    }

    public function matches(mixed $argument): bool
    {
        return 'Ibexa\Bundle\User\Controller\UserSettingsController::updateAction' === $argument;
    }

    public function buildView(array $parameters): UpdateView
    {
        $view = new UpdateView();

        $view->setUserSettingGroup($this->userSettingService->getUserSettingGroup($parameters['identifier']));
        $this->viewParametersInjector->injectViewParameters($view, $parameters);
        $this->viewConfigurator->configure($view);

        return $view;
    }
}
