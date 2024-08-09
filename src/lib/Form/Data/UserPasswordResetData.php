<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\User\Form\Data;

use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Symfony\Component\Validator\Constraints as Assert;

class UserPasswordResetData
{
    /**
     * @var string
     */
    #[Assert\NotBlank]
    private $newPassword;

    /**
     * @deprecated ContentType should be passed as option to FormType.
     *
     * @var \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType
     */
    private $contentType;

    /**
     * @param string|null $newPassword
     */
    public function __construct(?string $newPassword = null, ?ContentType $contentType = null)
    {
        $this->newPassword = $newPassword;
        $this->contentType = $contentType;
    }

    /**
     * @param string|null $newPassword
     */
    public function setNewPassword(?string $newPassword): void
    {
        $this->newPassword = $newPassword;
    }

    /**
     * @return string|null
     */
    public function getNewPassword(): ?string
    {
        return $this->newPassword;
    }

    /**
     * @return \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType
     */
    public function getContentType(): ?ContentType
    {
        return $this->contentType;
    }

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType $contentType
     */
    public function setContentType(ContentType $contentType): void
    {
        $this->contentType = $contentType;
    }
}
