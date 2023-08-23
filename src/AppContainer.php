<?php

namespace Bokoch\CommissionCalculator;

use Bokoch\CommissionCalculator\Exceptions\ClassNotFoundContainerException;
use Closure;

final class AppContainer implements Container
{
    /**
     * @var array<class-string, Closure(Container $container): void>
     */
    private array $registeredClasses = [];

    /**
     * @var array<class-string, Closure(Container $container): void>
     */
    private array $registeredSingletons = [];

    /**
     * @template TClass
     * @var array<class-string<TClass>, TClass>
     */
    private array $singletonInstances = [];


    public function register(string $class, Closure $closure): void
    {
        $this->registeredClasses[$class] = $closure;
    }

    public function registerSingleton(string $class, Closure $closure): void
    {
        $this->registeredSingletons[$class] = $closure;
    }

    public function make(string $class): object
    {
        if (isset($this->registeredClasses[$class])) {
            return call_user_func($this->registeredClasses[$class], $this);
        }

        if (isset($this->registeredSingletons[$class])) {
            if (isset($this->singletonInstances[$class])) {
                return $this->singletonInstances[$class];
            }

            return call_user_func($this->registeredSingletons[$class], $this);
        }

        throw new ClassNotFoundContainerException(
            sprintf('Class "%s" is not registered in container', $class)
        );
    }
}
