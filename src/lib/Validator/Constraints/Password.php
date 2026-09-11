<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\User\Validator\Constraints;

use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Contracts\Core\Repository\Values\User\User;
use Symfony\Component\Validator\Attribute\HasNamedArguments;
use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class Password extends Constraint
{
    public string $message = 'ez.user.password.invalid';

    public ?ContentType $contentType = null;

    public ?User $user = null;

    /**
     * @param array<string>|null $groups
     */
    #[HasNamedArguments]
    public function __construct(
        ?ContentType $contentType = null,
        ?User $user = null,
        ?string $message = null,
        ?array $groups = null,
        mixed $payload = null,
    ) {
        parent::__construct(null, $groups, $payload);

        $this->contentType = $contentType;
        $this->user = $user;
        $this->message = $message ?? $this->message;
    }

    #[\Override]
    public function getTargets(): array
    {
        return [self::CLASS_CONSTRAINT, self::PROPERTY_CONSTRAINT];
    }
}
