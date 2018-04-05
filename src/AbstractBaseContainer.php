<?php

namespace Dhii\Di;

use Dhii\Data\Container\CreateContainerExceptionCapableTrait;
use Dhii\Data\Container\NormalizeContainerCapableTrait;
use Dhii\Exception\CreateInternalExceptionCapableTrait;
use Dhii\Exception\CreateInvalidArgumentExceptionCapableTrait;
use Dhii\Exception\CreateOutOfRangeExceptionCapableTrait;
use Dhii\Exception\CreateRuntimeExceptionCapableTrait;
use Dhii\Invocation\CreateInvocationExceptionCapableTrait;
use Dhii\Invocation\CreateReflectionForCallableCapableTrait;
use Dhii\Iterator\CountIterableCapableTrait;
use Dhii\Iterator\ResolveIteratorCapableTrait;
use Dhii\Util\Normalization\NormalizeArrayCapableTrait;
use Dhii\Util\Normalization\NormalizeIntCapableTrait;
use Dhii\Util\Normalization\NormalizeIterableCapableTrait;
use Dhii\Util\Normalization\NormalizeStringCapableTrait;
use Dhii\Validation\CreateValidationFailedExceptionCapableTrait;
use Psr\Container\ContainerExceptionInterface;
use Dhii\Util\String\StringableInterface as Stringable;
use Exception as RootException;
use Psr\Container\ContainerInterface as BaseContainerInterface;
use Dhii\Data\Container\AbstractBaseContainer as BaseAbstractBaseContainer;
use ReflectionFunction;
use ReflectionMethod;

/**
 * Common functionality for regular DI containers.
 *
 * @since [*next-version*]
 */
abstract class AbstractBaseContainer extends BaseAbstractBaseContainer
{
    /* Ability to invoke callables;
     *
     * @since [*next-version*]
     */
    use InvokingTrait;

    /* Data object methods.
     *
     * @since [*next-version*]
     */
    use DataObjectTrait;

    /* Ability to retrieve resolved cached service.
     *
     * @since [*next-version*]
     */
    use GetServiceCapableCachingTrait;

    /* Awareness of a service cache.
     *
     * @since [*next-version*]
     */
    use ServiceCacheAwareTrait;

    /* Ability to resolve a service definition.
     *
     * @since [*next-version*]
     */
    use ResolveDefinitionCapableTrait;

    /* Ability to resolve an iterator from a Traversable chain.
     *
     * @since [*next-version*]
     */
    use ResolveIteratorCapableTrait;

    /* Ability to count iterables.
     *
     * @since [*next-version*]
     */
    use CountIterableCapableTrait;

    /* Ability to normalize into an array.
     *
     * @since [*next-version*]
     */
    use NormalizeArrayCapableTrait;

    /* Ability to normalize into a validator.
     *
     * @since [*next-version*]
     */
    use NormalizeIterableCapableTrait;

    /* Ability to normalize into a container.
     *
     * @since [*next-version*]
     */
    use NormalizeContainerCapableTrait;

    /* Ability to normalize into an integer.
     *
     * @since [*next-version*]
     */
    use NormalizeIntCapableTrait;

    /* Ability to normalize into a string.
     *
     * @since [*next-version*]
     */
    use NormalizeStringCapableTrait;

    /* Factory of Runtime exception.
     *
     * @since [*next-version*]
     */
    use CreateRuntimeExceptionCapableTrait;

    /* Factory of Invalid Argument exception.
     *
     * @since [*next-version*]
     */
    use CreateInvalidArgumentExceptionCapableTrait;

    /* Factory of Out of Range exception.
     *
     * @since [*next-version*]
     */
    use CreateOutOfRangeExceptionCapableTrait;

    /* Factory of CachingContainer exception.
     *
     * @since [*next-version*]
     */
    use CreateContainerExceptionCapableTrait;

    /* Factory of Invocation exception.
     *
     * @since [*next-version*]
     */
    use CreateInvocationExceptionCapableTrait;

    /* Ability to create a reflection for a callable.
     *
     * @since [*next-version*]
     */
    use CreateReflectionForCallableCapableTrait;

    /* Factory of Validation Failed exception.
     *
     * @since [*next-version*]
     */
    use CreateValidationFailedExceptionCapableTrait;

    /* Factory of Internal exception.
     *
     * @since [*next-version*]
     */
    use CreateInternalExceptionCapableTrait;

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function get($key)
    {
        return $this->_getService($key);
    }

    /**
     * Throws a container exception.
     *
     * @param string|Stringable|null      $message   The exception message, if any.
     * @param int|string|Stringable|null  $code      The numeric exception code, if any.
     * @param RootException|null          $previous  The inner exception, if any.
     * @param BaseContainerInterface|null $container The associated container, if any. Pass `true` to use available container.
     *
     * @throws ContainerExceptionInterface
     */
    protected function _throwContainerException($message = null, $code = null, $previous = null, $container = null)
    {
        $container = $container === true
            ? $this
            : $container;

        throw $this->_createContainerException($message, $code, $previous, $container);
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    protected function _createReflectionFunction($functionName)
    {
        return new ReflectionFunction($functionName);
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    protected function _createReflectionMethod($className, $methodName)
    {
        return new ReflectionMethod($className, $methodName);
    }
}
