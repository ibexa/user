<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\User\UserSetting;

use Ibexa\Contracts\User\UserSetting\FormMapperInterface;
use Ibexa\Core\Base\Exceptions\InvalidArgumentException;

/**
 * @internal
 */
class FormMapperRegistry
{
    /**
     * @param array<string, \Ibexa\Contracts\User\UserSetting\FormMapperInterface> $formMappers
     */
    public function __construct(
        protected array $formMappers = []
    ) {
    }

    public function addFormMapper(
        string $identifier,
        FormMapperInterface $formMapper
    ): void {
        $this->formMappers[$identifier] = $formMapper;
    }

    /**
     * @throws \Ibexa\Core\Base\Exceptions\InvalidArgumentException
     */
    public function getFormMapper(string $identifier): FormMapperInterface
    {
        if (!isset($this->formMappers[$identifier])) {
            throw new InvalidArgumentException(
                '$identifier',
                sprintf('There is no Form Mapper registered for \'%s\' identifier', $identifier)
            );
        }

        return $this->formMappers[$identifier];
    }

    /**
     * @return array<string, \Ibexa\Contracts\User\UserSetting\FormMapperInterface>
     */
    public function getFormMappers(): array
    {
        return $this->formMappers;
    }
}
