<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\User\UserSetting\Setting;

use Ibexa\Contracts\User\UserSetting\ValueDefinitionGroupInterface;
use Ibexa\Contracts\User\UserSetting\ValueDefinitionInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class EditContentGroup implements ValueDefinitionGroupInterface
{
    private TranslatorInterface $translator;
    private array $values;

    public function __construct(
        TranslatorInterface $translator,
        array $values = []
    ) {
        $this->translator = $translator;
        $this->values = $values;
    }

    public function getName(): string
    {
        return $this->translator->trans(
        /** @Desc("Edit Content") */
            'settings.group.edit_content.name',
            [],
            'user_settings'
        );
    }

    public function getDescription(): string
    {
        return $this->translator->trans(
        /** @Desc("") */
            'settings.group.edit_content.description',
            [],
            'user_settings'
        );
    }

    public function addToGroup(string $identifier, ValueDefinitionInterface $valueDefinition): void
    {
        $this->values[$identifier] = $valueDefinition;
    }

    public function getValues(): array
    {
        return $this->values;
    }
}
