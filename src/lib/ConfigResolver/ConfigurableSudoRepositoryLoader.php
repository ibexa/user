<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\User\ConfigResolver;

use Closure;
use Ibexa\Contracts\Core\Repository\Repository;
use OutOfBoundsException;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * ** Use with care**.
 *
 * A repository data loader that uses the sudo() method.
 *
 * It comes with parameter handling, either by passing an array of (supported) options
 * to the constructor, or using the setParam() method.
 *
 * Implementations will call load() with a repository callback as an argument.
 * The repository can be accessed using getRepository().
 */
abstract class ConfigurableSudoRepositoryLoader
{
    /**
     * @param array<string, mixed> $params
     */
    public function __construct(
        private readonly Repository $repository,
        private array $params = []
    ) {
    }

    public function setParam(string $name, mixed $value): self
    {
        $this->params[$name] = $value;

        return $this;
    }

    protected function getParam(string $name): mixed
    {
        if (!isset($this->params[$name])) {
            throw new OutOfBoundsException("There is no param '$name'");
        }

        return $this->params[$name];
    }

    protected function getRepository(): Repository
    {
        return $this->repository;
    }

    protected function sudo(Closure $callback): mixed
    {
        $resolver = new OptionsResolver();
        $this->configureOptions($resolver);
        $this->params = $resolver->resolve($this->params);

        return $this->repository->sudo($callback);
    }

    abstract protected function configureOptions(OptionsResolver $optionsResolver): void;
}
