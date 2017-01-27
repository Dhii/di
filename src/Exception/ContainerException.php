<?php

namespace Dhii\Di\Exception;

use Interop\Container\Exception\ContainerException as ContainerExceptionInterface;
use Dhii\Di\ExceptionInterface as DiExceptionInterface;

/**
 * An exception related to DI containers.
 *
 * @since [*next-version*]
 */
class ContainerException extends \Exception implements
    DiExceptionInterface,
    ContainerExceptionInterface
{
}
