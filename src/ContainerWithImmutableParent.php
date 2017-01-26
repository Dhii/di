<?php

namespace Dhii\Di;

use Interop\Container\ContainerInterface;
use Interop\Container\ServiceProvider as ServiceProviderInterface;

/**
 * This container accepts a parent instance, which cannot be changed from outside.
 */
class ContainerWithImmutableParent extends AbstractParentAwareContainer implements
    ContainerInterface,
    ParentAwareContainerInterface
{
    /**
     * @param ServiceProviderInterface    $definitions Servide definitions to add to this container.
     * @param ContainerInterface $parent      The container, which is to become this container's parent.
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
     */
    public function getParentContainer()
    {
        return $this->_getParentContainer();
    }

    /**
     * {@inheritdoc}
     */
    public function get($id)
    {
        return $this->_get($id);
    }

    /**
     * {@inheritdoc}
     */
    public function has($id)
    {
        return $this->_has($id);
    }
}
