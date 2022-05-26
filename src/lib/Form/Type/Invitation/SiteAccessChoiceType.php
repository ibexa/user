<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\User\Form\Type\Invitation;

use Ibexa\Core\MVC\Symfony\SiteAccess\SiteAccessServiceInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\ChoiceList;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class SiteAccessChoiceType extends AbstractType
{
    private SiteAccessServiceInterface $siteAccessService;

    public function __construct(SiteAccessServiceInterface $siteAccessService)
    {
        $this->siteAccessService = $siteAccessService;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'choice_loader' => ChoiceList::lazy(
                    $this,
                    fn () => $this->siteAccessService->getAll(),
                ),
                'choice_label' => 'name',
                'choice_name' => 'name',
                'choice_value' => 'name',
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent(): ?string
    {
        return ChoiceType::class;
    }
}
