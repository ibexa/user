<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\User\UserSetting\Group;

use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * The translation for the given group was changed to 'Locale' from 'Location'. However, for backwards compatibility
 * we decided to keep the old configuration with corresponding classes and services as 'location'.
 */
final class LocationGroup extends AbstractGroup
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
            /** @Desc("Locale") */
            'settings.group.locale.name',
            [],
            'ibexa_user_settings'
        );
    }

    public function getDescription(): string
    {
        return $this->translator->trans(
            /** @Desc("") */
            'settings.group.locale.description',
            [],
            'ibexa_user_settings'
        );
    }
}
