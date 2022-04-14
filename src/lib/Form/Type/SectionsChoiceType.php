<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\User\Form\Type;

use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Contracts\Core\Repository\SectionService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\ChoiceList;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SectionsChoiceType extends AbstractType
{
    private SectionService $sectionService;
    /** @var \Ibexa\Contracts\Core\Repository\Repository */
    private Repository $repository;

    public function __construct(
        Repository $repository,
        SectionService $sectionService
    ) {
        $this->sectionService = $sectionService;
        $this->repository = $repository;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'choice_loader' => ChoiceList::lazy(
                    $this, fn() => $this->repository->sudo(fn () => $this->sectionService->loadSections())
                ),
                'choice_label' => 'name',
                'choice_value' => 'id',
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
