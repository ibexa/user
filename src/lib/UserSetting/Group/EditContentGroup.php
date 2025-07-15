<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\User\UserSetting\Group;

use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Contracts\Translation\TranslatorInterface;

final class EditContentGroup extends AbstractGroup
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
            /** @Desc("Content authoring") */
            'settings.group.content_authoring.name',
            [],
            'ibexa_user_settings'
        );
    }

    public function getDescription(): string
    {
        return $this->translator->trans(
            /** @Desc("") */
            'settings.group.edit_content.description',
            [],
            'ibexa_user_settings'
        );
    }
}
