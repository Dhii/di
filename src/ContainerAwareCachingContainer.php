<?php

namespace Dhii\Di;

use ArrayAccess;
use Dhii\Data\Container\ContainerAwareInterface;
use Dhii\Data\Container\ContainerAwareTrait;
use Dhii\Data\Container\ResolveContainerCapableTrait;
use Dhii\I18n\StringTranslatingTrait;
use stdClass;
use Psr\Container\ContainerInterface as BaseContainerInterface;
use Dhii\Cache\ContainerInterface as CacheContainerInterface;

/**
 * A DI container that is aware of a parent container.
 *
 * Will resolve callable definitions, and cache the result.
 * While resolving, will retrieve the inner-most container from the
 * ancestor chain, and pass it to the definition.
 *
 * @since [*next-version*]
 */
class ContainerAwareCachingContainer extends AbstractBaseContainer implements ContainerAwareInterface
{
    /*
     * Basic ability to i18n strings.
     *
     * @since [*next-version*]
     */
    use StringTranslatingTrait;

    /* Awareness of an outer container.
     *
     * @since [*next-version*]
     */
    use ContainerAwareTrait;

    /* Ability to resolve inner-most container.
     *
     * @since [*next-version*]
     */
    use ResolveContainerCapableTrait;

    /**
     * @since [*next-version*]
     *
     * @param array|ArrayAccess|stdClass|BaseContainerInterface $services The services container.
     */
    public function __construct($services, CacheContainerInterface $serviceCache, $parentContainer = null)
    {
        if (is_array($services)) {
            $services = (object) $services;
        }

        $this->_setDataStore($services);
        $this->_setServiceCache($serviceCache);
        !is_null($parentContainer) && $this->_setContainer($parentContainer);
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function getContainer()
    {
        return $this->_getContainer();
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    protected function _getArgsForDefinition($definition)
    {
        $container = $this->_resolveContainer($this);

        return [$container];
    }
}
