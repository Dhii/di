<?php

namespace Dhii\Di;

use Interop\Container\ServiceProvider as BaseServiceProviderInterface;

/**
 * A simple, parent-agnostic container implementation.
 *
 * @since [*next-version*]
 */
class Container extends AbstractContainerBase implements ContainerInterface, WritableContainerInterface
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
     */
    public function set($id, $definition = null)
    {
        $this->_set($id, $definition);

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function register(BaseServiceProviderInterface $serviceProvider)
    {
        $this->_register($serviceProvider);

        return $this;
    }
}
