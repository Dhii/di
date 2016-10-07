<?php

namespace XedinUnknown\Di;

use Interop\Container\ContainerInterface;

/**
 * A container that can have its parent changed after initialization.
 */
class ContainerWithMutableParent extends ContainerWithImmutableParent
{
    /**
     * @param ContainerInterface $container The container to become this instance's parent.
     */
    public function setParentContainer(ContainerInterface $container)
    {
        $this->_setParentContainer($container);

        return $this;
    }
}
