<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\User\Form\Processor;

use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Contracts\Core\Repository\UserService;
use Ibexa\User\Form\Data\UserRegisterData;
use Ibexa\User\Form\UserFormEvents;
use Ibexa\ContentForms\Event\FormActionEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;

/**
 * Listens for and processes User register events.
 */
class UserRegisterFormProcessor implements EventSubscriberInterface
{
    /** @var \Ibexa\Contracts\Core\Repository\UserService */
    private $userService;

    /** @var \Symfony\Component\Routing\Generator\UrlGeneratorInterface */
    private $urlGenerator;

    /** @var \Ibexa\Core\Repository\Repository */
    private $repository;

    public function __construct(Repository $repository, UserService $userService, RouterInterface $router)
    {
        $this->userService = $userService;
        $this->urlGenerator = $router;
        $this->repository = $repository;
    }

    public static function getSubscribedEvents()
    {
        return [
            UserFormEvents::USER_REGISTER => ['processRegister', 20],
        ];
    }

    /**
     * @param \Ibexa\ContentForms\Event\FormActionEvent $event
     *
     * @throws \Exception
     */
    public function processRegister(FormActionEvent $event)
    {
        /** @var \Ibexa\User\Form\Data\UserRegisterData $data */
        if (!($data = $event->getData()) instanceof UserRegisterData) {
            return;
        }
        $form = $event->getForm();

        $this->createUser($data, $form->getConfig()->getOption('languageCode'));

        $redirectUrl = $this->urlGenerator->generate('ezplatform.user.register_confirmation');
        $event->setResponse(new RedirectResponse($redirectUrl));
        $event->stopPropagation();
    }

    /**
     * @param \Ibexa\User\Form\Data\UserRegisterData $data
     * @param $languageCode
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\User\User
     *
     * @throws \Exception
     */
    private function createUser(UserRegisterData $data, $languageCode)
    {
        foreach ($data->fieldsData as $fieldDefIdentifier => $fieldData) {
            $data->setField($fieldDefIdentifier, $fieldData->value, $languageCode);
        }

        return $this->repository->sudo(
            function () use ($data) {
                return $this->userService->createUser($data, $data->getParentGroups());
            }
        );
    }
}

class_alias(UserRegisterFormProcessor::class, 'EzSystems\EzPlatformUser\Form\Processor\UserRegisterFormProcessor');
