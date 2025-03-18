<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\User\UserSetting;

use Ibexa\Contracts\User\UserSetting\ValueDefinitionInterface;
use Ibexa\User\UserSetting\ValueDefinitionRegistry;
use PHPUnit\Framework\TestCase;

class ValueDefinitionRegistryTest extends TestCase
{
    public function testGetValueDefinitions(): void
    {
        $definitions = [
            'foo' => $this->createMock(ValueDefinitionInterface::class),
            'bar' => $this->createMock(ValueDefinitionInterface::class),
            'baz' => $this->createMock(ValueDefinitionInterface::class),
        ];

        $registry = new ValueDefinitionRegistry($definitions);

        self::assertEquals($definitions, $registry->getValueDefinitions());
    }

    public function testAddValueDefinition(): void
    {
        $foo = $this->createMock(ValueDefinitionInterface::class);

        $registry = new ValueDefinitionRegistry([]);
        $registry->addValueDefinition('foo', $foo);

        self::assertEquals(['foo' => $foo], $registry->getValueDefinitions());
    }

    public function testHasValueDefinition(): void
    {
        $registry = new ValueDefinitionRegistry([
            'foo' => $this->createMock(ValueDefinitionInterface::class),
        ]);

        self::assertTrue($registry->hasValueDefinition('foo'));
        self::assertFalse($registry->hasValueDefinition('bar'));
    }

    public function testGetValueDefinition(): void
    {
        $foo = $this->createMock(ValueDefinitionInterface::class);

        $registry = new ValueDefinitionRegistry([
            'foo' => $foo,
        ]);

        self::assertEquals($foo, $registry->getValueDefinition('foo'));
    }

    public function testCountValueDefinitions(): void
    {
        $definitions = [
            'foo' => $this->createMock(ValueDefinitionInterface::class),
            'bar' => $this->createMock(ValueDefinitionInterface::class),
        ];

        $registry = new ValueDefinitionRegistry($definitions);

        self::assertEquals(2, $registry->countValueDefinitions());
    }

    public function testCountValueDefinitionsWithEmptyRegistry(): void
    {
        $registry = new ValueDefinitionRegistry([]);

        self::assertEquals(0, $registry->countValueDefinitions());
    }
}
