<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\User\Command;

use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Contracts\Core\Repository\UserService;
use Ibexa\Contracts\Core\Repository\Values\User\User;
use Ibexa\Contracts\Core\Repository\Values\User\UserUpdateStruct;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'ibexa:user:update-user', description: 'Updates basic user data.')]
final class UpdateUserCommand extends Command
{
    public function __construct(
        private readonly UserService $userService,
        private readonly Repository $repository,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument(
            'user',
            InputArgument::REQUIRED,
            'User login',
        );
        $this->addOption(
            'password',
            null,
            InputOption::VALUE_OPTIONAL,
            'New plaintext password (input will be in a "hidden" mode)',
            false
        );
        $this->addOption(
            'email',
            null,
            InputOption::VALUE_REQUIRED,
            'New e-mail address',
        );
        $this->addOption(
            'enable',
            null,
            InputOption::VALUE_NONE,
            'Flag enabling the user being updated',
        );
        $this->addOption(
            'disable',
            null,
            InputOption::VALUE_NONE,
            'Flag disabling the user being updated',
        );
    }

    protected function interact(InputInterface $input, OutputInterface $output): void
    {
        $io = new SymfonyStyle($input, $output);
        $password = $input->getOption('password');

        if ($password !== null) {
            return;
        }

        $password = $io->askHidden('Password (your input will be hidden)');
        $input->setOption('password', $password);
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $userReference = $input->getArgument('user');
        $password = $input->getOption('password');
        $enable = $input->getOption('enable');
        $disable = $input->getOption('disable');
        $email = $input->getOption('email');

        if (!$password && !$enable && !$disable && $email === null) {
            $io->error('No new user data specified, exiting.');

            return Command::FAILURE;
        }

        $user = $this->userService->loadUserByLogin($userReference);

        if ($enable && $disable) {
            $io->error('--enable and --disable options cannot be used simultaneously.');

            return Command::FAILURE;
        }

        $userUpdateStruct = new UserUpdateStruct();
        $userUpdateStruct->password = $password;
        $userUpdateStruct->email = $email;
        $userUpdateStruct->enabled = $this->resolveEnabledFlag($enable, $disable);

        $this->repository->sudo(
            function () use ($user, $userUpdateStruct): User {
                return $this->userService->updateUser($user, $userUpdateStruct);
            }
        );

        $io->success(sprintf(
            'User "%s" was successfully updated.',
            $user->getLogin(),
        ));

        return Command::SUCCESS;
    }

    private function resolveEnabledFlag(bool $enable, bool $disable): ?bool
    {
        if (!$enable && !$disable) {
            return null;
        }

        return $enable === true;
    }
}
