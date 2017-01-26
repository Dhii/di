<?php

namespace Dhii\Di;

/**
 * Something that can act as a composite container.
 *
 * A composite container is a container that can contain other containers,
 * and perform queries on them.
 *
 * @since [*next-version*]
 */
interface CompositeContainerInterface
{
    /**
     * Return the inner containers that belong to this container.
     *
     * @since [*next-version*]
     *
     * @return array|\Traversable A list of containers that this container contains.
     */
    public function getContainers();
}
