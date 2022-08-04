<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\User\Form\Type\Invitation;

use Ibexa\Contracts\User\Invitation\InvitationService;
use Ibexa\User\Form\DataTransformer\InvitationTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

final class InvitationType extends AbstractType
{
    private InvitationService $invitationService;

    public function __construct(InvitationService $invitationService)
    {
        $this->invitationService = $invitationService;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addViewTransformer(new InvitationTransformer($this->invitationService));
    }

    public function getParent(): ?string
    {
        return TextType::class;
    }
}
