<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace Ibexa\User\Form\Type\Invitation;

use Ibexa\Contracts\User\Invitation\InvitationService;
use Ibexa\User\Form\DataTransformer\InvitationTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;

final class InvitationType extends AbstractType
{
    public function __construct(
        private readonly InvitationService $invitationService
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addViewTransformer(new InvitationTransformer($this->invitationService));
    }

    #[\Override]
    public function getParent(): string
    {
        return HiddenType::class;
    }
}
