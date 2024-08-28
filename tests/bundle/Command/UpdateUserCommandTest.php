<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\User\Command;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

final class UpdateUserCommandTest extends KernelTestCase
{
    private readonly CommandTester $commandTester;

    protected function setUp(): void
    {
        self::bootKernel();

        $application = new Application(self::$kernel);
        $application->setAutoExit(false);

        $command = $application->find('ibexa:user:update-user');
        $this->commandTester = new CommandTester($command);
    }

    public function testExecuteWithoutOptionsReturnsFailure(): void
    {
        $this->commandTester->execute([
            'user' => 'anonymous',
        ]);

        self::assertStringContainsString(
            'No new user data specified, exiting.',
            $this->commandTester->getDisplay()
        );

        self::assertSame(
            Command::FAILURE,
            $this->commandTester->getStatusCode()
        );
    }

    public function testExecuteWithEnableAndDisableOptionsReturnsFailure(): void
    {
        $this->commandTester->execute(
            [
                'user' => 'anonymous',
                '--enable' => true,
                '--disable' => true,
            ],
        );

        self::assertStringContainsString(
            '--enable and --disable options cannot be used simultaneously.',
            $this->commandTester->getDisplay()
        );

        self::assertSame(
            Command::FAILURE,
            $this->commandTester->getStatusCode()
        );
    }

    public function testExecuteReturnsSuccess(): void
    {
        $this->commandTester->execute(
            [
                'user' => 'anonymous',
                '--password' => true,
                '--email' => 'foo@bar.com',
                '--enable' => true,
            ],
        );

        self::assertStringContainsString(
            'User "anonymous" was successfully updated.',
            $this->commandTester->getDisplay()
        );

        $this->commandTester->assertCommandIsSuccessful();
    }
}
