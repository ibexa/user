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
    /**
     * @param array<string, \Ibexa\Contracts\User\UserSetting\ValueDefinitionInterface> $values
     */
    public function __construct(
        private readonly TranslatorInterface $translator,
        array $values = []
    ) {
        parent::__construct($values);
    }

    public function getName(): string
    {
        return $this->translator->trans(
            /** @Desc("Browsing") */
            'settings.group.browsing.name',
            [],
            'ibexa_user_settings'
        );
    }

    public function getDescription(): string
    {
        return $this->translator->trans(
            /** @Desc("") */
            'settings.group.browsing.description',
            [],
            'ibexa_user_settings'
        );
    }
}
