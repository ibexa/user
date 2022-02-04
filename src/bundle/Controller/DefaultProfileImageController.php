<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\User\Controller;

use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultProfileImageController extends Controller
{
    private ConfigResolverInterface $configResolver;

    public function __construct(
        ConfigResolverInterface $configResolver
    ) {
        $this->configResolver = $configResolver;
    }

    public function initialsAction(Request $request): Response
    {
        $initials = substr(
            $request->query->get('initials', ''),
            0,
            2
        );

        $colors = $this->getInitialsColors($initials);

        return $this->render('@IbexaUser/profile_image/initials.svg.twig', [
            'initials' => $initials,
            'text' => $colors['text'],
            'background' => $colors['background'],
        ]);
    }

    private function getInitialsColors(string $initials): array
    {
        $colors = $this->configResolver->getParameter('user.default_profile_image.colors');

        $index = array_sum(
            array_map('ord', str_split($initials))
        ) % count($colors);

        return $colors[$index];
    }
}
