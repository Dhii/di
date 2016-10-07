<?php

namespace XedinUnknown\Di;

use Interop\Container\ContainerInterface;

/**
 * Functionality that facilitates parent awareness of a container.
 */
abstract class AbstractParentAwareContainer extends AbstractContainer
{
    /** @var ContainerInterface */
    protected $parentContainer;

    /**
     * @return ContainerInterface
     */
    protected function _getParentContainer()
    {
        return ($this->parentContainer instanceof ContainerInterface)
                ? $this->parentContainer
                : null;
    }

    /**
     * @param ContainerInterface $container
     */
    protected function _setParentContainer(ContainerInterface $container)
    {
        $this->parentContainer = $container;

        return $this;
    }

    /**
     * @return ContainerInterface|null The top-most container in the chain, if exists;
     *                                 false otherwise.
     */
    protected function _getRootContainer()
    {
        $parent = $this->_getParentContainer();
        $root   = $parent;

        while ($parent) {
            if (!($parent instanceof ParentAwareContainerInterface)) {
                break;
            }

            if ($parent = $parent->getParentContainer()) {
                $root = $parent;
            }
        }

        return $root;
    }

    /**
     * This is what does the magic.
     *
     * @see AbstractContainer::_resolveDefinition()
     */
    protected function _resolveDefinition($definition, $config)
    {
        $root      = $this->_getRootContainer();
        $container = $root ? $root : $this;

        return call_user_func_array($definition, array($container, null, $config));
    }
}
