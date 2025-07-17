<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\User\Form\Type\UserSettings;

use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ShortDateTimeFormatType extends AbstractType
{
    public function __construct(
        private readonly ConfigResolverInterface $configResolver
    ) {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'date_format_choices' => $this->configResolver->getParameter('user_preferences.allowed_short_date_formats'),
            'time_format_choices' => $this->configResolver->getParameter('user_preferences.allowed_short_time_formats'),
        ]);
    }

    #[\Override]
    public function getParent(): string
    {
        return DateTimeFormatType::class;
    }
}
