<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\User\Form\DataMapper;

use Ibexa\Contracts\Core\Repository\Values\Content\Field;
use Ibexa\User\ConfigResolver\RegistrationContentTypeLoader;
use Ibexa\User\ConfigResolver\RegistrationGroupLoader;
use Ibexa\Contracts\ContentForms\Data\Content\FieldData;
use Ibexa\User\Form\Data\UserRegisterData;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Form data mapper for user creation.
 */
class UserRegisterMapper
{
    /** @var \Ibexa\User\ConfigResolver\RegistrationContentTypeLoader */
    private $contentTypeLoader;

    /** @var \Ibexa\User\ConfigResolver\RegistrationContentTypeLoader */
    private $parentGroupLoader;

    /** @var array */
    private $params;

    /**
     * @param \Ibexa\User\ConfigResolver\RegistrationContentTypeLoader $contentTypeLoader
     * @param \Ibexa\User\ConfigResolver\RegistrationGroupLoader $registrationGroupLoader
     */
    public function __construct(
        RegistrationContentTypeLoader $contentTypeLoader,
        RegistrationGroupLoader $registrationGroupLoader
    ) {
        $this->contentTypeLoader = $contentTypeLoader;
        $this->parentGroupLoader = $registrationGroupLoader;
    }

    /**
     * @param $name
     * @param $value
     */
    public function setParam($name, $value)
    {
        $this->params[$name] = $value;
    }

    /**
     * @return \Ibexa\User\Form\Data\UserRegisterData
     */
    public function mapToFormData()
    {
        $resolver = new OptionsResolver();
        $this->configureOptions($resolver);
        $this->params = $resolver->resolve($this->params);

        $contentType = $this->contentTypeLoader->loadContentType();

        $data = new UserRegisterData([
            'contentType' => $contentType,
            'mainLanguageCode' => $this->params['language'],
            'enabled' => true,
        ]);
        $data->addParentGroup($this->parentGroupLoader->loadGroup());

        foreach ($contentType->fieldDefinitions as $fieldDef) {
            $data->addFieldData(new FieldData([
                'fieldDefinition' => $fieldDef,
                'field' => new Field([
                    'fieldDefIdentifier' => $fieldDef->identifier,
                    'languageCode' => $this->params['language'],
                ]),
                'value' => $fieldDef->defaultValue,
            ]));
        }

        return $data;
    }

    private function configureOptions(OptionsResolver $optionsResolver)
    {
        $optionsResolver->setRequired('language');
    }
}

class_alias(UserRegisterMapper::class, 'EzSystems\EzPlatformUser\Form\DataMapper\UserRegisterMapper');
