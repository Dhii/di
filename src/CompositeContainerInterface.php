<?php

namespace XedinUnknown\Di;

/**
 * Something that can act as a composite container.
 *
 * A composite container is a container that can contain other containers,
 * and perform queries on them.
 */
interface CompositeContainerInterface
{
    /**
     * Return the inner containers that belong to this container.
     *
     * @return array|\Traversable A list of containers that this container contains.
     */
    public function getContainers();
}
