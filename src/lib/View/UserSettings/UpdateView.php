<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\User\View\UserSettings;

use Ibexa\Core\MVC\Symfony\View\BaseView;
use Ibexa\User\UserSetting\UserSetting;
use Ibexa\User\UserSetting\UserSettingGroup;

class UpdateView extends BaseView
{
    private ?UserSetting $userSetting;

    private ?UserSettingGroup $userSettingGroup;

    public function __construct(
        $templateIdentifier = null,
        array $parameters = [],
        string $viewType = 'full'
    ) {
        $this->userSetting = null;
        $this->userSettingGroup = null;
        parent::__construct($templateIdentifier, $parameters, $viewType);
    }

    /**
     * @return \Ibexa\User\UserSetting\UserSetting|null
     */
    public function getUserSetting(): ?UserSetting
    {
        return $this->userSetting;
    }

    /**
     * @param \Ibexa\User\UserSetting\UserSetting|null $userSetting
     */
    public function setUserSetting(?UserSetting $userSetting): void
    {
        $this->userSetting = $userSetting;
    }

    public function getUserSettingGroup(): ?UserSettingGroup
    {
        return $this->userSettingGroup;
    }

    public function setUserSettingGroup(?UserSettingGroup $userSettingGroup): void
    {
        $this->userSettingGroup = $userSettingGroup;
    }

    /**
     * {@inheritdoc}
     */
    protected function getInternalParameters(): array
    {
        return [
            'user_setting' => $this->getUserSetting(),
            'user_setting_group' => $this->getUserSettingGroup(),
        ];
    }
}
