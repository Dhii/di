<?php

namespace Dhii\Di;

use Exception;
use Dhii\Di\Exception\ContainerException;
use Dhii\Di\Exception\NotFoundException;
use Interop\Container\ServiceProvider as BaseServiceProviderInterface;

/**
 * A simple, parent-agnostic container implementation.
 *
 * @since [*next-version*]
 */
class Container extends AbstractContainer implements ContainerInterface
{
    /**
     * Constructor.
     *
     * @since [*next-version*]
     *
     * @param BaseServiceProviderInterface $definitions Service definitions to add to this container.
     */
    public function __construct(BaseServiceProviderInterface $definitions = null)
    {
        if (!is_null($definitions)) {
            $this->_set($definitions);
        }
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     *
     * @return ContainerException The new exception instance.
     */
    protected function _createContainerException($message, $code = 0, Exception $innerException = null)
    {
        return new ContainerException($message, $code, $innerException);
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     *
     * @return NotFoundException The new exception instance.
     */
    protected function _createNotFoundException($message, $code = 0, Exception $innerException = null)
    {
        return new NotFoundException($message, $code, $innerException);
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function get($id)
    {
        return $this->_get($id);
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function has($id)
    {
        return $this->_has($id);
    }

    /**
     * Registers a service or multiple services to this container.
     *
     * @since [*next-version*]
     *
     * @param string|ServiceProviderInterface $id         The service ID, or a service provider
     * @param callable|null                   $definition The service definition.
     *
     * @return $this This instance.
     */
    public function set($id, $definition)
    {
        $this->_set($id, $definition);

        return $this;
    }
}
