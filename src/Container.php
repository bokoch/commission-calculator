<?php

declare(strict_types=1);

namespace Bokoch\CommissionCalculator;

use Bokoch\CommissionCalculator\Exceptions\ClassNotFoundContainerException;
use Closure;

interface Container
{
    /**
     * @param class-string $class
     * @param Closure(Container $container): void $closure
     * @return void
     */
    public function register(string $class, Closure $closure): void;


    /**
     * @param class-string $class
     * @param Closure(Container $container): void $closure
     * @return void
     */
    public function registerSingleton(string $class, Closure $closure): void;

    /**
     * @template TClass
     * @param class-string<TClass> $class
     * @return TClass
     * @throws ClassNotFoundContainerException
     */
    public function make(string $class): object;
}
