<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace Ibexa\User\Form\Processor;

use Ibexa\ContentForms\Event\FormActionEvent;
use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Contracts\Core\Repository\RoleService;
use Ibexa\Contracts\Core\Repository\UserService;
use Ibexa\Contracts\Core\Repository\Values\User\User;
use Ibexa\Contracts\Notifications\Service\NotificationServiceInterface;
use Ibexa\Contracts\Notifications\Value\Notification\SymfonyNotificationAdapter;
use Ibexa\Contracts\Notifications\Value\Recipent\SymfonyRecipientAdapter;
use Ibexa\Contracts\Notifications\Value\Recipent\UserRecipient;
use Ibexa\Contracts\User\Notification\UserRegister;
use Ibexa\User\Form\Data\UserRegisterData;
use Ibexa\User\Form\UserFormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Listens for and processes User register events.
 */
readonly class UserRegisterFormProcessor implements EventSubscriberInterface
{
    public function __construct(
        private Repository $repository,
        private UserService $userService,
        private UrlGeneratorInterface $urlGenerator,
        private RoleService $roleService,
        private NotificationServiceInterface $notificationService
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            UserFormEvents::USER_REGISTER => ['processRegister', 20],
        ];
    }

    /**
     * @throws \Exception
     */
    public function processRegister(FormActionEvent $event): void
    {
        /** @var \Ibexa\User\Form\Data\UserRegisterData $data */
        if (!($data = $event->getData()) instanceof UserRegisterData) {
            return;
        }
        $form = $event->getForm();

        $user = $this->createUser($data, $form->getConfig()->getOption('languageCode'));
        $this->sendNotification($user);

        $redirectUrl = $this->urlGenerator->generate('ibexa.user.register_confirmation');
        $event->setResponse(new RedirectResponse($redirectUrl));
        $event->stopPropagation();
    }

    private function createUser(UserRegisterData $data, string $languageCode): User
    {
        foreach ($data->fieldsData as $fieldDefIdentifier => $fieldData) {
            $data->setField($fieldDefIdentifier, $fieldData->value, $languageCode);
        }

        return $this->repository->sudo(
            function () use ($data): User {
                $user = $this->userService->createUser($data, $data->getParentGroups());
                if ($data->getRole() !== null) {
                    $this->roleService->assignRoleToUser($data->getRole(), $user, $data->getRoleLimitation());
                }

                return $user;
            }
        );
    }

    private function sendNotification(User $user): void
    {
        $this->notificationService->send(
            new SymfonyNotificationAdapter(
                new UserRegister($user),
            ),
            [new SymfonyRecipientAdapter(new UserRecipient($user))],
        );
    }
}
