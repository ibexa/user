<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\User\Controller;

use Ibexa\Contracts\User\Invitation\Exception\InvitationExist;
use Ibexa\Contracts\User\Invitation\Exception\UserExist;
use Ibexa\Contracts\User\Invitation\InvitationService;
use Ibexa\Contracts\User\Invitation\MailSender;
use Ibexa\User\ExceptionHandler\ActionResultHandler;
use Ibexa\User\Form\Type\UserInvitationType;
use Ibexa\User\View\Invitation\FormView;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;

class UserInvitationController extends Controller
{
    private InvitationService $invitationService;

    private MailSender $mailSender;

    private FormFactoryInterface $formFactory;

    private ActionResultHandler $actionResultHandler;

    public function __construct(
        InvitationService $invitationService,
        MailSender $mailSender,
        FormFactoryInterface $formFactory,
        ActionResultHandler $actionResultHandler
    ) {
        $this->invitationService = $invitationService;
        $this->mailSender = $mailSender;
        $this->formFactory = $formFactory;
        $this->actionResultHandler = $actionResultHandler;
    }

    public function inviteUser(Request $request)
    {
        $form = $this->formFactory->create(UserInvitationType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var \Ibexa\User\Form\Data\UserInvitationData $data */
            $data = $form->getData();
            try {
                $invitation = $this->invitationService->createInvitation(
                    $data->getEmail(),
                    $data->getSiteaccess(),
                    $data->getUserGroup(),
                    $data->getRole(),
                    $data->getRoleLimitation(),
                );

                $this->mailSender->sendInvitation($invitation);

                $this->actionResultHandler->success(
                /** @Desc("Invitation send to '%email%' updated.") */
                    'user_invitation.send.success',
                    ['%email%' => $data->getEmail()],
                    'user_invitation'
                );

            } catch (InvitationExist $e) {
                $this->actionResultHandler->error(
                /** @Desc("Invitation for '%email%' already exist.") */
                    'user_invitation.send.invitation_exist',
                    ['%email%' => $data->getEmail()],
                    'user_invitation'
                );
            } catch (UserExist $e) {
                $this->actionResultHandler->error(
                /** @Desc("User with '%email%' already exist.") */
                    'user_invitation.send.user_exist',
                    ['%email%' => $data->getEmail()],
                    'user_invitation'
                );
            }
        }

        return new FormView(null, ['form' => $form->createView()]);
    }
}
