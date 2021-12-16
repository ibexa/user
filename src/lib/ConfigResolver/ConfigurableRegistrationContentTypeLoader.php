<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\User\ConfigResolver;

use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Loads the registration content type from a configured, injected content type identifier.
 */
class ConfigurableRegistrationContentTypeLoader extends ConfigurableSudoRepositoryLoader implements RegistrationContentTypeLoader
{
    public function loadContentType()
    {
        return $this->sudo(
            function () {
                return
                    $this->getRepository()
                        ->getContentTypeService()
                        ->loadContentTypeByIdentifier(
                            $this->getParam('contentTypeIdentifier')
                        );
            }
        );
    }

    protected function configureOptions(OptionsResolver $optionsResolver)
    {
        $optionsResolver->setRequired('contentTypeIdentifier');
    }
}

class_alias(ConfigurableRegistrationContentTypeLoader::class, 'EzSystems\EzPlatformUser\ConfigResolver\ConfigurableRegistrationContentTypeLoader');
