<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace Ibexa\User\Form\DataMapper;

use Ibexa\Contracts\ContentForms\Data\Content\FieldData;
use Ibexa\Contracts\Core\Repository\Values\Content\Field;
use Ibexa\Contracts\User\Invitation\Invitation;
use Ibexa\User\ConfigResolver\RegistrationContentTypeLoader;
use Ibexa\User\ConfigResolver\RegistrationGroupLoader;
use Ibexa\User\Form\Data\UserRegisterData;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Form data mapper for user creation.
 */
class UserRegisterMapper
{
    private RegistrationContentTypeLoader $contentTypeLoader;

    /** @var \Ibexa\User\ConfigResolver\RegistrationContentTypeLoader */
    private RegistrationGroupLoader $parentGroupLoader;

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
    public function setParam($name, $value): void
    {
        $this->params[$name] = $value;
    }

    /**
     * @return \Ibexa\User\Form\Data\UserRegisterData
     */
    public function mapToFormData(): UserRegisterData
    {
        $resolver = new OptionsResolver();
        $this->configureOptions($resolver);
        $this->params = $resolver->resolve($this->params);

        /** @var \Ibexa\Contracts\User\Invitation\Invitation|null $invitation */
        $invitation = $this->params['invitation'] ?? null;
        $contentType = $this->contentTypeLoader->loadContentType(
            $invitation ? $invitation->getSiteAccessIdentifier() : null
        );

        $data = new UserRegisterData([
            'contentType' => $contentType,
            'mainLanguageCode' => $this->params['language'],
            'enabled' => true,
        ]);

        if ($invitation && $invitation->getUserGroup()) {
            $targetGroup = $invitation->getUserGroup();
        } else {
            $targetGroup = $this->parentGroupLoader->loadGroup();
        }

        $data->addParentGroup($targetGroup);

        if ($invitation) {
            $data->setRole($invitation->getRole());
            $data->setRoleLimitation($invitation->getLimitation());
            $data->email = $invitation->getEmail();
        }

        /** @var \Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition $fieldDef */
        foreach ($contentType->fieldDefinitions as $fieldDef) {
            $value = $fieldDef->defaultValue;
            if ($invitation && $fieldDef->fieldTypeIdentifier === 'ibexa_user') {
                $value->email = $invitation->getEmail();
            }
            $data->addFieldData(new FieldData([
                'fieldDefinition' => $fieldDef,
                'field' => new Field([
                    'fieldDefIdentifier' => $fieldDef->identifier,
                    'languageCode' => $this->params['language'],
                ]),
                'value' => $value,
            ]));
        }

        return $data;
    }

    private function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver
            ->setRequired('language')
            ->setDefined(['invitation'])
            ->setAllowedTypes('invitation', Invitation::class);
    }
}
