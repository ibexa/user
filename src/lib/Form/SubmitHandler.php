<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\User\Form;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;

interface SubmitHandler
{
    public function handle(FormInterface $form, callable $handler): ?Response;
}
