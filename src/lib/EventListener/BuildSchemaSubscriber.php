<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\User\EventListener;

use Ibexa\Contracts\DoctrineSchema\Event\SchemaBuilderEvent;
use Ibexa\Contracts\DoctrineSchema\SchemaBuilderEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final readonly class BuildSchemaSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private string $schemaFilePath
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            SchemaBuilderEvents::BUILD_SCHEMA => ['onBuildSchema', 250],
        ];
    }

    public function onBuildSchema(SchemaBuilderEvent $event): void
    {
        $event
            ->getSchemaBuilder()
            ->importSchemaFromFile($this->schemaFilePath);
    }
}
