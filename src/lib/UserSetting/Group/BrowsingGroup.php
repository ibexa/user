<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\User\UserSetting\Group;

use Symfony\Contracts\Translation\TranslatorInterface;

final class BrowsingGroup extends AbstractGroup
{
    private TranslatorInterface $translator;

    public function __construct(
        TranslatorInterface $translator,
        array $values = []
    ) {
        $this->translator = $translator;
        parent::__construct($values);
    }

    public function getName(): string
    {
        return $this->translator->trans(
            /** @Desc("Browsing") */
            'settings.group.browsing.name',
            [],
            'user_settings'
        );
    }

    public function getDescription(): string
    {
        return $this->translator->trans(
            /** @Desc("") */
            'settings.group.browsing.description',
            [],
            'user_settings'
        );
    }
}
