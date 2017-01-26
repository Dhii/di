<?php

namespace Dhii\Di;

use Interop\Container\ContainerInterface;

/**
 * A container that can have many containers.
 *
 * @since [*next-version*]
 */
abstract class AbstractCompositeContainer extends AbstractParentAwareContainer
{
    /**
     * The prefix for container IDs.
     *
     * @since [*next-version*]
     */
    const CONTAINER_ID_PREFIX = 'container-';

    /**
     * Adds a container.
     *
     * @since [*next-version*]
     *
     * @param ContainerInterface $container The container to add.
     *
     * @return $this This instance.
     */
    protected function _add(ContainerInterface $container)
    {
        $this->_set($this->_createContainerId($container), function (ContainerInterface $c, $previous = null) use ($container) {
            return $container;
        });

        return $this;
    }

    /**
     * Generates a container ID.
     *
     * @todo To check if the $container instance should play a part in ID generation.
     *
     * @since [*next-version*]
     *
     * @param ContainerInterface $container The container for which an ID will be generated.
     *
     * @return string A new container ID, guaranteed to be unique in the scope of this container.
     */
    protected function _createContainerId(ContainerInterface $container)
    {
        do {
            $id = uniqid(static::CONTAINER_ID_PREFIX);
        } while ($this->_hasDefinition($id));

        return $id;
    }

    /**
     * Retrieves a service from the first child container that has its definition.
     *
     * @since [*next-version*]
     *
     * @param string $id The ID of the service to retrieve.
     *
     * @return mixed|null The service, if found; otherwise, null.
     */
    protected function _getDelegated($id)
    {
        if ($having = $this->_hasDelegated($id)) {
            return $having->get($id);
        }

        return;
    }

    /**
     * Determines which of the child containers has a service with the specified ID.
     *
     * @since [*next-version*]
     *
     * @param string $id The ID of the service to check for.
     *
     * @return ContainerInterface|bool The container, which has the definition with the specified ID, if found;
     *                                 otherwise, false.
     */
    protected function _hasDelegated($id)
    {
        foreach ($this->_getContainers() as $_container) {
            if ($_container->has($id)) {
                return $_container;
            }
        }

        return false;
    }

    /**
     * Gets the child containers.
     *
     * @since [*next-version*]
     * @see CompositeContainerInterface::getContainers()
     *
     * @return ContainerInterface[]|\Traversable A list of containers.
     */
    protected function _getContainers()
    {
        $containers = array();
        foreach ($this->serviceDefinitions as $_key => $_value) {
            $containers[$_key] = $this->_get($_key);
        }

        return $containers;
    }
}
