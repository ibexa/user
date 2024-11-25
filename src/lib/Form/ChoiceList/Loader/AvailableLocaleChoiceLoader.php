<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\User\Form\ChoiceList\Loader;

use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Symfony\Component\Form\ChoiceList\ArrayChoiceList;
use Symfony\Component\Form\ChoiceList\ChoiceListInterface;
use Symfony\Component\Form\ChoiceList\Loader\ChoiceLoaderInterface;
use Symfony\Component\Intl\Locales;
use Symfony\Component\Validator\Constraints\Locale;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AvailableLocaleChoiceLoader implements ChoiceLoaderInterface
{
    /**
     * @var string[]
     *
     * Acholi dialect is used by In-Context translation and should not be present on the list of available translations.
     */
    private const array EXCLUDED_TRANSLATIONS = ['ach'];

    private ValidatorInterface $validator;

    private ConfigResolverInterface $configResolver;

    /** @var string[] */
    private array $availableTranslations;

    /**
     * @param string[] $availableTranslations
     */
    public function __construct(
        ValidatorInterface $validator,
        ConfigResolverInterface $configResolver,
        array $availableTranslations
    ) {
        $this->validator = $validator;
        $this->availableTranslations = $availableTranslations;
        $this->configResolver = $configResolver;
    }

    /**
     * @return array<string, string>
     */
    public function getChoiceList(): array
    {
        $choices = [];

        $additionalTranslations = $this->configResolver->getParameter('user_preferences.additional_translations');
        $availableLocales = array_unique(array_merge($this->availableTranslations, $additionalTranslations));
        $locales = array_diff($availableLocales, self::EXCLUDED_TRANSLATIONS);

        foreach ($locales as $locale) {
            if (0 === $this->validator->validate($locale, new Locale())->count()) {
                $choices[Locales::getName($locale)] = $locale;
            }
        }

        return $choices;
    }

    public function loadChoiceList(?callable $value = null): ChoiceListInterface
    {
        return new ArrayChoiceList($this->getChoiceList(), $value);
    }

    public function loadChoicesForValues(array $values, ?callable $value = null): array
    {
        // Optimize
        $values = array_filter($values);
        if (empty($values)) {
            return [];
        }

        return $this->loadChoiceList($value)->getChoicesForValues($values);
    }

    public function loadValuesForChoices(array $choices, ?callable $value = null): array
    {
        // Optimize
        $choices = array_filter($choices);
        if (empty($choices)) {
            return [];
        }

        // If no callable is set, choices are the same as values
        if (null === $value) {
            return $choices;
        }

        return $this->loadChoiceList($value)->getValuesForChoices($choices);
    }
}
