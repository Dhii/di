<?php

namespace Dhii\Di;

use ArrayAccess;
use Dhii\Cache\ContainerInterface as CacheContainerInterface;
use Dhii\I18n\StringTranslatingTrait;
use stdClass;
use Psr\Container\ContainerInterface as BaseContainerInterface;

/**
 * A basic DI container.
 *
 * Will resolve callable definitions, and cache the result.
 *
 * @since [*next-version*]
 */
class CachingContainer extends AbstractBaseContainer
{
    /*
     * Basic ability to i18n strings.
     *
     * @since [*next-version*]
     */
    use StringTranslatingTrait;

    /**
     * @since [*next-version*]
     *
     * @param array|ArrayAccess|stdClass|BaseContainerInterface $services     The services container.
     * @param CacheContainerInterface                           $serviceCache The cache for resolved services.
     */
    public function __construct($services, CacheContainerInterface $serviceCache)
    {
        if (is_array($services)) {
            $services = (object) $services;
        }

        $this->_setDataStore($services);
        $this->_setServiceCache($serviceCache);
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    protected function _getArgsForDefinition($definition)
    {
        return [$this];
    }
}
