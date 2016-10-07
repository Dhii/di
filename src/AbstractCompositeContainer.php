<?php

namespace XedinUnknown\Di;

use Interop\Container\ContainerInterface;

/**
 * A container that can have many containers.
 */
abstract class AbstractCompositeContainer extends AbstractParentAwareContainer
{
    const CONTAINER_ID_PREFIX = 'container-';

    /**
     * @param ContainerInterface $container The container to add.
     */
    protected function _add(ContainerInterface $container)
    {
        $this->_set($this->_createContainerId($container), function (ContainerInterface $container, $previous = null) use ($container) {
            return $container;
        });

        return $this;
    }

    /**
     * @param ContainerInterface $container
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
