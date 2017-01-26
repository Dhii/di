<?php

namespace Dhii\Di;

use Interop\Container\ContainerInterface;
use Interop\Container\ServiceProvider as ServiceProviderInterface;

/**
 * This container accepts a parent instance, which cannot be changed from external objects.
 *
 * @since [*next-version*]
 */
class ContainerWithImmutableParent extends AbstractParentAwareContainer implements
    ContainerInterface,
    ParentAwareContainerInterface
{
    /**
     * Constructor.
     *
     * @since [*next-version*]
     *
     * @param ServiceProviderInterface $definitions Service definitions to add to this container.
     * @param ContainerInterface       $parent      The container, which is to become this container's parent.
     */
    public function __construct(ServiceProviderInterface $definitions = null, ContainerInterface $parent = null)
    {
        if (!is_null($definitions)) {
            $this->_set($definitions);
        }

        if (!is_null($parent)) {
            $this->_setParentContainer($parent);
        }
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
    public function get($id)
    {
        return $this->_get($id);
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function has($id)
    {
        return $this->_has($id);
    }
}
