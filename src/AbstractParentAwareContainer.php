<?php

namespace Dhii\Di;

use Interop\Container\ContainerInterface;

/**
 * Functionality that facilitates parent awareness of a container.
 *
 * @since [*next-version*]
 */
abstract class AbstractParentAwareContainer extends AbstractContainer
{
    /**
     * The parent container instance.
     *
     * @since [*next-version*]
     *
     * @var ContainerInterface
     */
    protected $parentContainer;

    /**
     * Retrieves the parent container instance.
     *
     * @since [*next-version*]
     *
     * @return ContainerInterface|null The parent container or null if this container has no parent.
     */
    protected function _getParentContainer()
    {
        return ($this->parentContainer instanceof ContainerInterface)
                ? $this->parentContainer
                : null;
    }

    /**
     * Checks if this container has a parent container.
     *
     * @since [*next-version*]
     *
     * @return bool True if this container has a parent; false otherwise.
     */
    protected function _hasParentContainer()
    {
        return $this->parentContainer instanceof ContainerInterface;
    }

    /**
     * Sets the parent container instance.
     *
     * @since [*next-version*]
     *
     * @param ContainerInterface|null $container The parent container or null to remove the parent. Default: null
     *
     * @return $this This instance.
     */
    protected function _setParentContainer(ContainerInterface $container = null)
    {
        $this->parentContainer = $container;

        return $this;
    }

    /**
     * Retrieves the container at the root of the hierarchy.
     *
     * @since [*next-version*]
     *
     * @return ContainerInterface|null The top-most container in the chain, if exists;
     *                                 null otherwise.
     */
    protected function _getRootContainer()
    {
        $parent = $this;
        do {
            $root = $parent;

            $parent = ($parent instanceof ParentAwareContainerInterface)
                ? $parent->getParentContainer()
                : null;
        } while ($parent);

        return $root;
    }

    /**
     * {@inheritdoc}
     *
     * This is what does the magic.
     *
     * @since [*next-version*]
     * @see AbstractContainer::_resolveDefinition()
     */
    protected function _resolveDefinition($definition, $config)
    {
        $root      = $this->_getRootContainer();
        $container = $root ? $root : $this;

        return call_user_func_array($definition, array($container, null, $config));
    }
}
