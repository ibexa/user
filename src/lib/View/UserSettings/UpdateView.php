<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\User\View\UserSettings;

use Ibexa\Core\MVC\Symfony\View\BaseView;
use Ibexa\User\UserSetting\UserSetting;

class UpdateView extends BaseView
{
    /** @var \Ibexa\User\UserSetting\UserSetting|null */
    private $userSetting;

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

    /**
     * {@inheritdoc}
     */
    protected function getInternalParameters(): array
    {
        return [
            'user_setting' => $this->getUserSetting(),
        ];
    }
}

class_alias(UpdateView::class, 'EzSystems\EzPlatformUser\View\UserSettings\UpdateView');
