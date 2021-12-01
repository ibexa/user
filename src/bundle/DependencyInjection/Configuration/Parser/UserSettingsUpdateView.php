<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\User\DependencyInjection\Configuration\Parser;

use Ibexa\Bundle\Core\DependencyInjection\Configuration\Parser\View;

class UserSettingsUpdateView extends View
{
    public const NODE_KEY = 'user_settings_update_view';
    public const INFO = 'Template selection settings when displaying a user setting update form';
}

class_alias(UserSettingsUpdateView::class, 'EzSystems\EzPlatformUserBundle\DependencyInjection\Configuration\Parser\UserSettingsUpdateView');
