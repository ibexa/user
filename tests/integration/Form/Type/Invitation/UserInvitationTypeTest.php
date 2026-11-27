<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\User\Form\Type\Invitation;

use Ibexa\Contracts\Core\Repository\Values\User\Limitation\RoleLimitation;
use Ibexa\Contracts\Core\Repository\Values\User\Limitation\SectionLimitation;
use Ibexa\Contracts\Core\Repository\Values\User\Limitation\SubtreeLimitation;
use Ibexa\Tests\Integration\User\IbexaKernelTestCase;
use Ibexa\User\Form\Data\UserInvitationData;
use Ibexa\User\Form\Type\Invitation\UserInvitationType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

/**
 * @covers \Ibexa\User\Form\Type\Invitation\UserInvitationType
 * @covers \Ibexa\User\Form\Data\UserInvitationData
 */
final class UserInvitationTypeTest extends IbexaKernelTestCase
{
    private const INVITEE_EMAIL = 'invitee@example.com';
    private const SITEACCESS = 'eng';
    private const EDITORS_GROUP_REMOTE_ID = '3c160cca19fb135f83bd02d911f04db2';
    private const STANDARD_SECTION_ID = 1;
    private const SUBTREE_PATH = '/1/2/';

    protected function setUp(): void
    {
        self::setAdministratorUser();
    }

    /**
     * @dataProvider provideForSubmit
     *
     * @param array<string, mixed> $overrides
     */
    public function testSubmit(
        array $overrides,
        ?RoleLimitation $expectedLimitation
    ): void {
        $form = $this->submitForm($overrides);

        $limitation = $this->getValidData($form)->getRoleLimitation();

        // assertSame on values: assertEquals on the objects would accept '1' for 1
        self::assertSame(
            $expectedLimitation === null ? null : get_class($expectedLimitation),
            $limitation === null ? null : get_class($limitation)
        );
        self::assertSame(
            $expectedLimitation === null ? null : $expectedLimitation->limitationValues,
            $limitation === null ? null : $limitation->limitationValues
        );
    }

    /**
     * @return iterable<string, array{array<string, mixed>, RoleLimitation|null}>
     */
    public static function provideForSubmit(): iterable
    {
        yield 'no limitation' => [
            ['limitation_type' => '0'],
            null,
        ];

        yield 'section limitation' => [
            ['limitation_type' => '1', 'sections' => [(string)self::STANDARD_SECTION_ID]],
            new SectionLimitation(['limitationValues' => [self::STANDARD_SECTION_ID]]),
        ];

        yield 'subtree limitation' => [
            ['limitation_type' => '2', 'location_path' => self::SUBTREE_PATH],
            new SubtreeLimitation(['limitationValues' => [self::SUBTREE_PATH]]),
        ];
    }

    /**
     * @dataProvider provideForMissingRequiredField
     */
    public function testSubmitWithMissingRequiredFieldShowsValidationError(
        string $field,
        ?string $value
    ): void {
        $form = $this->submitForm([$field => $value, 'limitation_type' => '0']);

        self::assertTrue($form->isSubmitted());
        self::assertFalse($form->isValid());
        self::assertCount(1, $form->get($field)->getErrors());
    }

    /**
     * @return iterable<string, array{string, string|null}>
     */
    public static function provideForMissingRequiredField(): iterable
    {
        yield 'empty email' => ['email', ''];
        yield 'no site access' => ['siteaccess', null];
    }

    /**
     * @param array<string, mixed> $overrides
     *
     * @return FormInterface<UserInvitationData|null>
     */
    private function submitForm(array $overrides): FormInterface
    {
        $role = self::getRoleService()->loadRoleByIdentifier('Editor');
        $userGroup = self::getUserService()->loadUserGroupByRemoteId(self::EDITORS_GROUP_REMOTE_ID);

        $formFactory = self::getServiceByClassName(FormFactoryInterface::class);
        $form = $formFactory->create(UserInvitationType::class, null, ['csrf_protection' => false]);
        $form->submit($overrides + [
            'email' => self::INVITEE_EMAIL,
            'siteaccess' => self::SITEACCESS,
            'role' => (string)$role->id,
            'user_group' => (string)$userGroup->id,
        ]);

        return $form;
    }

    /**
     * @param FormInterface<UserInvitationData|null> $form
     */
    private function getValidData(FormInterface $form): UserInvitationData
    {
        self::assertTrue($form->isSubmitted());
        self::assertTrue($form->isValid(), (string)$form->getErrors(true));

        $data = $form->getData();
        self::assertInstanceOf(UserInvitationData::class, $data);
        self::assertSame(self::INVITEE_EMAIL, $data->getEmail());
        self::assertNotNull($data->getSiteaccess());
        self::assertSame(self::SITEACCESS, $data->getSiteaccess()->name);
        self::assertNotNull($data->getRole());
        self::assertSame('Editor', $data->getRole()->identifier);
        self::assertNotNull($data->getUserGroup());
        self::assertSame(self::EDITORS_GROUP_REMOTE_ID, $data->getUserGroup()->contentInfo->remoteId);

        return $data;
    }
}
