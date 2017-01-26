<?php

namespace Dhii\Di;

use Dhii\Di\Exception\ContainerException;
use Dhii\Di\Exception\NotFoundException;
use Interop\Container\ServiceProvider;

/**
 * Basic functionality of a DI container.
 *
 * @since [*next-version*]
 */
abstract class AbstractContainer
{
    /**
     * Cache for created service instances.
     *
     * @since [*next-version*]
     *
     * @var array
     */
    protected $serviceCache = array();

    /**
     * The service definitions.
     *
     * @since [*next-version*]
     *
     * @var callable[]
     */
    protected $serviceDefinitions = array();

    /**
     * Retrieves a service by its ID.
     *
     * @since [*next-version*]
     *
     * @param string ID The ID of the service to retrieve.
     *
     * @throws NotFoundException If no service is registered with the given ID.
     *
     * @return mixed The service identified by the given ID.
     */
    protected function _get($id)
    {
        if ($this->_isCached($id)) {
            return $this->_getCached($id);
        }

        $this->_cacheService($id, $this->_make($id));

        return $this->_getCached($id);
    }

    /**
     * Creates a new instance of a service.
     *
     * This can be exposed by a public method to implement FactoryInterface.
     *
     * @todo Check why return doc includes a `null` possibility.
     *
     * @param string $id     The ID of the service to create.
     * @param mixed  $config Some kind of configuration.
     *
     * @throws NotFoundException If no service is registered with the given ID.
     *
     * @return object|null The created service instance.
     */
    protected function _make($id, $config = array())
    {
        if (!($definition = $this->_getDefinition($id))) {
            throw new NotFoundException(sprintf('Could not create service for ID "%1$s": no service defined', $id));
        }

        return $this->_resolveDefinition($definition, $config);
    }

    /**
     * Resolves a service definition into a service instance.
     *
     * @since [*next-version*]
     *
     * @param callable $definition The service definition.
     * @param array    $config     An array of configuration arguments to pass to the definition.
     *
     * @throws ContainerException If the service definition is not a valid callable.
     *
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
     * Retrieves the service definitions.
     *
     * @since [*next-version*]
     *
     * @return callable[] An associative array of all the registered definitions, mapped by their ID.
     */
    protected function _getDefinitions()
    {
        return $this->serviceDefinitions;
    }

    /**
     * Retrieves a service definition by ID.
     *
     * @since [*next-version*]
     *
     * @param string $id The ID of the service to get the definition for.
     *
     * @return callable|null The service definition mapped to the given ID, if the ID is registered;
     *                       otherwise null.
     */
    protected function _getDefinition($id)
    {
        return isset($this->serviceDefinitions[$id])
                ? $this->serviceDefinitions[$id]
                : null;
    }

    /**
     * Checks if a service ID exists in this container.
     *
     * @since [*next-version*]
     *
     * @return bool True if a definition with the specified ID exists in this container;
     *              false otherwise.
     */
    protected function _has($id)
    {
        return $this->_hasDefinition($id);
    }

    /**
     * Checks if a service definition is registered to a given ID.
     *
     * @since [*next-version*]
     *
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
     * Checks if a service instance is cached.
     *
     * @since [*next-version*]
     *
     * @param string $id The service ID to check.
     *
     * @return bool True if a service with this ID exists in cache; false otherwise.
     */
    protected function _isCached($id)
    {
        return isset($this->serviceCache[$id]);
    }

    /**
     * Caches a service instance.
     *
     * @since [*next-version*]
     *
     * @param string $id      The ID of the service to cache.
     * @param mixed  $service The service.
     *
     * @return $this This instance.
     */
    protected function _cacheService($id, $service)
    {
        $this->serviceCache[$id] = $service;

        return $this;
    }

    /**
     * Retrieves the cached instance of a service.
     *
     * @since [*next-version*]
     *
     * @param string $id The ID of the service to retrieve.
     *
     * @return mixed|null The cached service instance if found; otherwise null.
     */
    protected function _getCached($id)
    {
        return isset($this->serviceCache[$id])
                ? $this->serviceCache[$id]
                : null;
    }

    /**
     * Registers a service or multiple services to this container.
     *
     * @since [*next-version*]
     *
     * @param string|ServiceProvider $id         The service ID, or a service provider
     * @param callable|null          $definition The service definition.
     *
     * @return $this This instance.
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
     * Registers a service definition.
     *
     * @since [*next-version*]
     *
     * @param string   $id         The service ID.
     * @param callable $definition The service definition.
     */
    protected function _setDefinition($id, $definition)
    {
        $this->serviceDefinitions[$id] = $definition;

        return $this;
    }
}
