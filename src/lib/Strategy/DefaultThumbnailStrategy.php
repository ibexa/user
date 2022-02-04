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

final class DefaultThumbnailStrategy implements ThumbnailStrategy
{
    private const THUMBNAIL_MIME_TYPE = 'image/svg+xml';

    private UrlGeneratorInterface $urlGenerator;

    public function __construct(
        UrlGeneratorInterface $urlGenerator
    ) {
        $this->urlGenerator = $urlGenerator;
    }

    public function getThumbnail(
        ContentType $contentType,
        array $fields,
        ?VersionInfo $versionInfo = null
    ): ?Thumbnail {
        if ($contentType->identifier !== 'user') {
            return null;
        }

        $initials = $this->getInitials($fields);

        return new Thumbnail([
            'resource' => $this->urlGenerator->generate('ibexa.user.default_profile_image.initials', [
                'initials' => $initials,
            ]),
            'mimeType' => self::THUMBNAIL_MIME_TYPE,
        ]);
    }

    private function getInitials(array $fields): string
    {
        $initials = '';
        $identifiers = ['first_name', 'last_name'];
        foreach ($identifiers as $identifier) {
            /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Field $field */
            foreach ($fields as $field) {
                if ($field->fieldDefIdentifier === $identifier) {
                    $initials .= substr((string)$field->value, 0, 1);
                }
            }
        }

        return strtoupper($initials);
    }
}
