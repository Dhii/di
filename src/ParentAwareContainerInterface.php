<?php

namespace Dhii\Di;

use Interop\Container\ContainerInterface;

/**
 * A container that can have a parent container.
 *
 * This interface is often used to delegate lookup.
 *
 * @since [*next-version*]
 */
interface ParentAwareContainerInterface
{
    /**
     * Retrieve the container that is the parent of this container.
     *
     * @return ContainerInterface
     */
    public function getParentContainer();
}
