<?php

namespace XedinUnknown\Di;

use Interop\Container\ContainerInterface;

/**
 * Description of CompositeContainer.
 *
 * @author Xedin Unknown
 */
class CompositeContainer extends AbstractCompositeContainer implements
    ContainerInterface,
    ParentAwareContainerInterface,
    CompositeContainerInterface
{
    /**
     * @param ContainerInterface $parent The parent container of this instance.
     */
    public function __construct(ContainerInterface $parent = null)
    {
        if (!is_null($parent)) {
            $this->_setParentContainer($parent);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function get($id)
    {
        return $this->_getDelegated($id);
    }

    /**
     * {@inheritdoc}
     */
    public function has($id)
    {
        return $this->_hasDelegated($id);
    }

    /**
     * {@inheritdoc}
     */
    public function getParentContainer()
    {
        return $this->_getParentContainer();
    }

    /**
     * {@inheritdoc}
     */
    public function getContainers()
    {
        return $this->_getContainers();
    }

    /**
     * @param ContainerInterface $container The container to add.
     */
    public function add($container)
    {
        $this->_add($container);

        return $this;
    }
}
