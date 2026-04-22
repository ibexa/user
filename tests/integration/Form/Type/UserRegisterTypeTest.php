<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\User\Form\Type;

use Ibexa\Contracts\ContentForms\Data\Content\FieldData;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Core\Repository\Values\ContentType\FieldDefinition;
use Ibexa\Tests\Integration\User\IbexaKernelTestCase;
use Ibexa\User\Form\Data\UserRegisterData;
use Ibexa\User\Form\Type\UserRegisterType;
use Symfony\Component\Form\FormFactoryInterface;

final class UserRegisterTypeTest extends IbexaKernelTestCase
{
    public function testAllowedFieldDefinitionsIdentifiers(): void
    {
        // Arrange
        $expectedIdentifiers = ['last_name'];
        $data = self::prepareUserRegisterData();
        $language = 'eng-GB';

        $configResolver = $this->createMock(ConfigResolverInterface::class);
        $configResolver
            ->method('getParameter')
            ->with('user_registration.form.allowed_field_definitions_identifiers')
            ->willReturn($expectedIdentifiers);
        self::getContainer()->set(UserRegisterType::class, new UserRegisterType($configResolver));
        $formFactory = self::getServiceByClassName(FormFactoryInterface::class);

        // Act
        $form = $formFactory->create(
            UserRegisterType::class,
            $data,
            [
                'languageCode' => $language,
                'mainLanguageCode' => $language,
                'struct' => $data,
            ]
        );

        // Assert
        $fieldsData = $form->get('fieldsData');
        self::assertCount(count($expectedIdentifiers), $fieldsData);
        foreach ($expectedIdentifiers as $identifier) {
            self::assertTrue(
                $fieldsData->has($identifier),
                sprintf('fieldsData is missing expected child "%s"', $identifier)
            );
        }
    }

    private static function prepareUserRegisterData(): UserRegisterData
    {
        return new UserRegisterData(['fieldsData' => [
            'first_name' => new FieldData([
                'fieldDefinition' => new FieldDefinition([
                    'fieldTypeIdentifier' => '',
                ]),
            ]),
            'last_name' => new FieldData([
                'fieldDefinition' => new FieldDefinition([
                    'fieldTypeIdentifier' => '',
                ]),
            ]),
            'user_account' => new FieldData([
                'fieldDefinition' => new FieldDefinition([
                    'fieldTypeIdentifier' => '',
                ]),
            ]),
        ]]);
    }
}
