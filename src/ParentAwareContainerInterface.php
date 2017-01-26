<?php

namespace Dhii\Di;

/**
 * Formalized contract of containers that can delegate lookup.
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
