<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\User\Strategy;

use Ibexa\Contracts\Core\Repository\Strategy\ContentThumbnail\ThumbnailStrategy;
use Ibexa\Contracts\Core\Repository\Values\Content\Thumbnail;
use Ibexa\Contracts\Core\Repository\Values\Content\VersionInfo;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final readonly class DefaultThumbnailStrategy implements ThumbnailStrategy
{
    private const string THUMBNAIL_MIME_TYPE = 'image/svg+xml';
    private const string USER_TYPE_IDENTIFIER = 'ibexa_user';

    /**
     * @param list<string> $initialsFieldDefIdentifiers
     */
    public function __construct(
        private UrlGeneratorInterface $urlGenerator,
        private array $initialsFieldDefIdentifiers
    ) {
    }

    /**
     * @param list<\Ibexa\Contracts\Core\Repository\Values\Content\Field> $fields
     */
    public function getThumbnail(
        ContentType $contentType,
        array $fields,
        ?VersionInfo $versionInfo = null
    ): ?Thumbnail {
        if (!$this->isUser($contentType)) {
            return null;
        }

        $initials = $this->getInitials($fields);

        return new Thumbnail([
            'resource' => $this->urlGenerator->generate('ibexa.user.default_profile_image.initials', [
                'initials' => $initials,
            ]) . '#profile_image',
            'mimeType' => self::THUMBNAIL_MIME_TYPE,
        ]);
    }

    /**
     * @param list<\Ibexa\Contracts\Core\Repository\Values\Content\Field> $fields
     */
    private function getInitials(array $fields): string
    {
        $initials = '';
        foreach ($this->initialsFieldDefIdentifiers as $identifier) {
            /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Field $field */
            foreach ($fields as $field) {
                if ($field->getFieldDefinitionIdentifier() === $identifier) {
                    $initials .= substr((string)$field->getValue(), 0, 1);
                }
            }
        }

        return strtoupper($initials);
    }

    private function isUser(ContentType $contentType): bool
    {
        return $contentType->hasFieldDefinitionOfType(self::USER_TYPE_IDENTIFIER);
    }
}
