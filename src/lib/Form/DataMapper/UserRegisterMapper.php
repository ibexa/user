<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace Ibexa\User\Form\DataMapper;

use Ibexa\Contracts\ContentForms\Data\Content\FieldData;
use Ibexa\Contracts\Core\Repository\Values\Content\Field;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;
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
    /** @var RegistrationContentTypeLoader */
    private $contentTypeLoader;

    /** @var RegistrationContentTypeLoader */
    private $parentGroupLoader;

    /** @var array */
    private $params;

    /**
     * @param RegistrationContentTypeLoader $contentTypeLoader
     * @param RegistrationGroupLoader $registrationGroupLoader
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
    public function setParam(
        $name,
        $value
    ) {
        $this->params[$name] = $value;
    }

    /**
     * @return UserRegisterData
     */
    public function mapToFormData()
    {
        $resolver = new OptionsResolver();
        $this->configureOptions($resolver);
        $this->params = $resolver->resolve($this->params);

        /** @var Invitation|null $invitation */
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

        /** @var FieldDefinition $fieldDef */
        foreach ($contentType->fieldDefinitions as $fieldDef) {
            $value = $fieldDef->defaultValue;
            if ($invitation && $fieldDef->fieldTypeIdentifier === 'ezuser') {
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

    private function configureOptions(OptionsResolver $optionsResolver)
    {
        $optionsResolver
            ->setRequired('language')
            ->setDefined(['invitation'])
            ->setAllowedTypes('invitation', Invitation::class);
    }
}

class_alias(UserRegisterMapper::class, 'EzSystems\EzPlatformUser\Form\DataMapper\UserRegisterMapper');
