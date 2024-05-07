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
    public function testGetValueDefinitions()
    {
        $definitions = [
            'foo' => $this->createMock(ValueDefinitionInterface::class),
            'bar' => $this->createMock(ValueDefinitionInterface::class),
            'baz' => $this->createMock(ValueDefinitionInterface::class),
        ];

        $registry = new ValueDefinitionRegistry($definitions);

        self::assertEquals($definitions, $registry->getValueDefinitions());
    }

    public function testAddValueDefinition()
    {
        $foo = $this->createMock(ValueDefinitionInterface::class);

        $registry = new ValueDefinitionRegistry([]);
        $registry->addValueDefinition('foo', $foo);

        self::assertEquals(['foo' => $foo], $registry->getValueDefinitions());
    }

    public function testHasValueDefinition()
    {
        $registry = new ValueDefinitionRegistry([
            'foo' => $this->createMock(ValueDefinitionInterface::class),
        ]);

        self::assertTrue($registry->hasValueDefinition('foo'));
        self::assertFalse($registry->hasValueDefinition('bar'));
    }

    public function testGetValueDefinition()
    {
        $foo = $this->createMock(ValueDefinitionInterface::class);

        $registry = new ValueDefinitionRegistry([
            'foo' => $foo,
        ]);

        self::assertEquals($foo, $registry->getValueDefinition('foo'));
    }

    public function testCountValueDefinitions()
    {
        $definitions = [
            'foo' => $this->createMock(ValueDefinitionInterface::class),
            'bar' => $this->createMock(ValueDefinitionInterface::class),
        ];

        $registry = new ValueDefinitionRegistry($definitions);

        self::assertEquals(2, $registry->countValueDefinitions());
    }

    public function testCountValueDefinitionsWithEmptyRegistry()
    {
        $registry = new ValueDefinitionRegistry([]);

        self::assertEquals(0, $registry->countValueDefinitions());
    }
}

class_alias(ValueDefinitionRegistryTest::class, 'EzSystems\EzPlatformUser\Tests\UserSetting\ValueDefinitionRegistryTest');
