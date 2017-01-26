<?php

namespace Dhii\Di;

use Interop\Container\ContainerInterface;

/**
 * Concrete implementation of a container that cna have child containers.
 *
 * @since [*next-version*]
 */
class CompositeContainer extends AbstractCompositeContainer implements
    ContainerInterface,
    ParentAwareContainerInterface,
    CompositeContainerInterface
{
    /**
     * Constructor.
     *
     * @since [*next-version*]
     *
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
     *
     * @since [*next-version*]
     */
    public function get($id)
    {
        return $this->_getDelegated($id);
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function has($id)
    {
        return $this->_hasDelegated($id);
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function getParentContainer()
    {
        return $this->_getParentContainer();
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function getContainers()
    {
        return $this->_getContainers();
    }

    /**
     * Adds a child container.
     *
     * @since [*next-version*]
     *
     * @param ContainerInterface $container The container to add.
     *
     * @return $this This instance.
     */
    public function add(ContainerInterface $container)
    {
        $this->_add($container);

        return $this;
    }
}
