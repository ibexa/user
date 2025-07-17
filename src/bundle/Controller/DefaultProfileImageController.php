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

final class DefaultProfileImageController extends Controller
{
    public function __construct(
        private readonly ConfigResolverInterface $configResolver
    ) {
    }

    public function initialsAction(Request $request): Response
    {
        $initials = substr($request->query->get('initials', ''), 0, 2);
        $colors = $this->getInitialsColors($initials);

        $response = new Response();
        $response->headers->set('Content-Type', 'image/svg+xml');

        return $this->render('@IbexaUser/profile_image/initials.svg.twig', [
            'initials' => $initials,
            'text_color' => $colors['text'],
            'border_color' => $colors['text'],
            'background_color' => $colors['background'],
        ], $response);
    }

    /**
     * @return array{text: string, background: string}
     */
    private function getInitialsColors(string $initials): array
    {
        $colors = $this->configResolver->getParameter('user.default_profile_image.colors');

        $index = array_sum(
            array_map('ord', str_split($initials))
        ) % count($colors);

        return $colors[$index];
    }
}
