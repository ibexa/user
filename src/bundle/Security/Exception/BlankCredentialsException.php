<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\User\Security\Exception;

use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Throwable;

final class BlankCredentialsException extends BadCredentialsException
{
    public const string FIELD_USERNAME = 'username';

    public const string FIELD_PASSWORD = 'password';

    /** @var list<self::FIELD_*> */
    private array $blankFields;

    /**
     * @param list<self::FIELD_*> $blankFields
     */
    public function __construct(
        array $blankFields,
        string $message = '',
        int $code = 0,
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);

        $this->blankFields = $blankFields;
    }

    /**
     * @return list<self::FIELD_*>
     */
    public function getBlankFields(): array
    {
        return $this->blankFields;
    }

    /**
     * @return array{list<self::FIELD_*>, array<mixed>}
     */
    public function __serialize(): array
    {
        return [$this->blankFields, parent::__serialize()];
    }

    /**
     * @param array{list<self::FIELD_*>, array<mixed>} $data
     */
    public function __unserialize(array $data): void
    {
        [$this->blankFields, $parentData] = $data;

        parent::__unserialize($parentData);
    }
}
