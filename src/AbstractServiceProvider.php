<?php

namespace XedinUnknown\Di;

/**
 * Description of AbstractServiceProvider.
 *
 * @author Xedin Unknown
 */
abstract class AbstractServiceProvider
{
    protected $serviceDefinitions = array();

    /**
     * @see \Interop\Container\ServiceProvider::getServices()
     *
     * @return callable[]|\Traversable A list of service definitions.
     */
    protected function _getServices()
    {
        return $this->serviceDefinitions;
    }

    /**
     * Adds a service definition to this provider.
     *
     * @param string   $id         The ID of the service definition.
     * @param callable $definition The service definition.
     *
     * @throws ContainerException
     */
    protected function _add($id, $definition)
    {
        // Checking only format, because the definition may become available later
        if (!is_callable($definition, true)) {
            throw new ContainerException(sprintf('Could not add service definition with ID "%1$s": The definition must be a callable'));
        }

        $this->serviceDefinitions[$id] = $definition;

        return $this;
    }
}
