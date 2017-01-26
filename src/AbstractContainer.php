<?php

namespace Dhii\Di;

use Interop\Container\ServiceProvider;

/**
 * Basic functionality of a DI container.
 */
abstract class AbstractContainer
{
    /** @var mixed[] */
    protected $serviceCache = array();
    /** @var callable[] */
    protected $serviceDefinitions = array();

    /**
     * @param string ID The ID of the service to get.
     *
     * @return mixed The service identified by ID.
     */
    protected function _get($id)
    {
        if ($this->_isCached($id)) {
            return $this->_getCached($id);
        }

        $service = $this->_make($id);
        $this->_cacheService($id, $service);

        return $this->_getCached($id);
    }

    /**
     * Creates a new instance of a service.
     *
     * In the future, this could be exposed by a public method to implement FactoryInterface.
     *
     * @param string $id     The ID of the service to create.
     * @param mixed  $config Some kind of configuration.
     *
     * @return object|null
     */
    protected function _make($id, $config = array())
    {
        if (!($definition = $this->_getDefinition($id))) {
            throw new NotFoundException(sprintf('Could not create service for ID "%1$s": no service defined', $id));
        }

        return $this->_resolveDefinition($definition, $config);
    }

    /**
     * @return mixed The service, to which the definition resolves.
     */
    protected function _resolveDefinition($definition, $config)
    {
        if (!is_callable($definition)) {
            throw new ContainerException(sprintf('Could not create service for ID "%1$s": service definition must be callable', $id));
        }

        return call_user_func_array($definition, array($this, null, $config));
    }

    /**
     * @return callable[] All definitions registered with this instance, by ID.
     */
    protected function _getDefinitions()
    {
        return $this->serviceDefinitions;
    }

    /**
     * @param string $id The ID of the service to get the definition for.
     *
     * @return callable|null The service definition, if registered; otherwise false.
     */
    protected function _getDefinition($id)
    {
        return isset($this->serviceDefinitions[$id])
                ? $this->serviceDefinitions[$id]
                : null;
    }

    /**
     * @return bool True if a definition with the specified ID exists in this container;
     *              false otherwise.
     */
    protected function _has($id)
    {
        return $this->_hasDefinition($id);
    }

    /**
     * @param string $id The ID of the service definition to check for.
     *
     * @return bool True if a definition with the specified ID is registered;
     *              otherwise false.
     */
    protected function _hasDefinition($id)
    {
        return isset($this->serviceDefinitions[$id]);
    }

    /**
     * @param string $id The service ID to check.
     *
     * @return bool True if a service with this ID exists in cache; false otherwise.
     */
    protected function _isCached($id)
    {
        return isset($this->serviceCache[$id]);
    }

    /**
     * @param string $id      The ID of the service to cache.
     * @param mixed  $service The service.
     */
    protected function _cacheService($id, $service)
    {
        $this->serviceCache[$id] = $service;

        return $this;
    }

    /**
     * @param string $id The ID of the service to retrieve.
     *
     * @return mixed|null The cached service if found; otherwise null.
     */
    protected function _getCached($id)
    {
        return isset($this->serviceCache[$id])
                ? $this->serviceCache[$id]
                : null;
    }

    /**
     * @param string|ServiceProvider $id         The service ID, or a service provider
     * @param callable|null          $definition The service definition.
     */
    protected function _set($id, $definition = null)
    {
        if ($id instanceof ServiceProvider) {
            foreach ($id->getServices() as $_id => $_definition) {
                $this->_setDefinition($_id, $_definition);
            }

            return $this;
        }

        $this->_setDefinition($id, $definition);

        return $this;
    }

    /**
     * @param string   $id         The service ID.
     * @param callable $definition The service definition.
     */
    protected function _setDefinition($id, $definition)
    {
        $this->serviceDefinitions[$id] = $definition;

        return $this;
    }
}
