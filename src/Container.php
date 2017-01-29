<?php

namespace Dhii\Di;

use Interop\Container\ServiceProvider as BaseServiceProviderInterface;

/**
 * A simple, parent-agnostic container implementation.
 *
 * @since [*next-version*]
 */
class Container extends AbstractContainerBase implements ContainerInterface
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
     * Registers a service or multiple services to this container.
     *
     * @since [*next-version*]
     *
     * @param string|ServiceProviderInterface $id         The service ID, or a service provider
     * @param callable                        $definition The service definition.
     *
     * @return $this This instance.
     */
    public function set($id, $definition)
    {
        $this->_set($id, $definition);

        return $this;
    }
}
