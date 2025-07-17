<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\User\Form\Type\Invitation;

use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Contracts\Core\Repository\SectionService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\ChoiceList;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class SectionsChoiceType extends AbstractType
{
    public function __construct(
        private readonly Repository $repository,
        private readonly SectionService $sectionService
    ) {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'choice_loader' => ChoiceList::lazy(
                    $this,
                    fn () => $this->repository->sudo(fn (): iterable => $this->sectionService->loadSections())
                ),
                'choice_label' => 'name',
                'choice_value' => 'id',
            ]);
    }

    #[\Override]
    public function getParent(): string
    {
        return ChoiceType::class;
    }
}
