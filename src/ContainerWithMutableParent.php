<?php

namespace Dhii\Di;

use Interop\Container\ContainerInterface;

/**
 * A container that can have its parent changed after initialization.
 *
 * @since [*next-version*]
 */
class ContainerWithMutableParent extends ContainerWithImmutableParent
{
    /**
     * Sets the parent container.
     *
     * @since [*next-version*]
     *
     * @param ContainerInterface $container The container to become this instance's parent.
     *
     * @return $this This instance.
     */
    public function setParentContainer(ContainerInterface $container)
    {
        $this->_setParentContainer($container);

        return $this;
    }
}
