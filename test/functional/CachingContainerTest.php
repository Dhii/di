<?php

namespace Dhii\Di\FuncTest;

use Dhii\Di\CachingContainer as TestSubject;
use Dhii\Cache\ContainerInterface as CacheContainerInterface;
use Psr\Container\ContainerInterface as BaseContainerInterface;
use stdClass;
use Xpmock\TestCase;
use Exception as RootException;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_MockObject_MockBuilder as MockBuilder;

/**
 * Tests {@see TestSubject}.
 *
 * @since [*next-version*]
 */
class CachingContainerTest extends TestCase
{
    /**
     * The class name of the test subject.
     *
     * @since [*next-version*]
     */
    const TEST_SUBJECT_CLASSNAME = 'Dhii\Di\CachingContainer';

    /**
     * Creates a new instance of the test subject.
     *
     * @since [*next-version*]
     *
     * @param array $methods         The methods to mock.
     * @param array $constructorArgs Arguments for the constructor.
     *
     * @return MockObject|TestSubject The new instance.
     */
    public function createInstance($methods = [], $constructorArgs = [])
    {
        is_array($methods) && $methods = $this->mergeValues($methods, [
            '__',
        ]);

        $mock = $this->getMockBuilder(static::TEST_SUBJECT_CLASSNAME)
            ->setMethods($methods)
            ->setConstructorArgs($constructorArgs)
            ->getMock();

        return $mock;
    }

    /**
     * Merges the values of two arrays.
     *
     * The resulting product will be a numeric array where the values of both inputs are present, without duplicates.
     *
     * @since [*next-version*]
     *
     * @param array $destination The base array.
     * @param array $source      The array with more keys.
     *
     * @return array The array which contains unique values
     */
    public function mergeValues($destination, $source)
    {
        return array_keys(array_merge(array_flip($destination), array_flip($source)));
    }

    /**
     * Creates a mock that both extends a class and implements interfaces.
     *
     * This is particularly useful for cases where the mock is based on an
     * internal class, such as in the case with exceptions. Helps to avoid
     * writing hard-coded stubs.
     *
     * @since [*next-version*]
     *
     * @param string   $className      Name of the class for the mock to extend.
     * @param string[] $interfaceNames Names of the interfaces for the mock to implement.
     *
     * @return MockBuilder The builder for a mock of an object that extends and implements
     *                     the specified class and interfaces.
     */
    public function mockClassAndInterfaces($className, $interfaceNames = [])
    {
        $paddingClassName = uniqid($className);
        $definition = vsprintf('abstract class %1$s extends %2$s implements %3$s {}', [
            $paddingClassName,
            $className,
            implode(', ', $interfaceNames),
        ]);
        eval($definition);

        return $this->getMockBuilder($paddingClassName);
    }

    /**
     * Creates a mock that uses traits.
     *
     * This is particularly useful for testing integration between multiple traits.
     *
     * @since [*next-version*]
     *
     * @param string[] $traitNames Names of the traits for the mock to use.
     *
     * @return MockBuilder The builder for a mock of an object that uses the traits.
     */
    public function mockTraits($traitNames = [])
    {
        $paddingClassName = uniqid('Traits');
        $definition = vsprintf('abstract class %1$s {%2$s}', [
            $paddingClassName,
            implode(
                ' ',
                array_map(
                    function ($v) {
                        return vsprintf('use %1$s;', [$v]);
                    },
                    $traitNames)),
        ]);
        var_dump($definition);
        eval($definition);

        return $this->getMockBuilder($paddingClassName);
    }

    /**
     * Creates a new exception.
     *
     * @since [*next-version*]
     *
     * @param string $message The exception message.
     *
     * @return RootException|MockObject The new exception.
     */
    public function createException($message = '')
    {
        $mock = $this->getMockBuilder('Exception')
            ->setConstructorArgs([$message])
            ->getMock();

        return $mock;
    }

    /**
     * Creates a new invocable object.
     *
     * @since [*next-version*]
     *
     * @return MockObject An object that has an `__invoke()` method.
     */
    public function createCallable()
    {
        $mock = $this->getMockBuilder('MyCallable')
            ->setMethods(['__invoke'])
            ->getMock();

        return $mock;
    }

    /**
     * Creates a new cache container.
     *
     * @since [*next-version*]
     *
     * @return MockObject|CacheContainerInterface The new cache container.
     */
    public function createCacheContainer()
    {
        $mock = $this->getMockBuilder('Dhii\Cache\MemoryMemoizer')
            ->setMethods(null)
            ->getMock();

        return $mock;
    }

    /**
     * Creates a new container.
     *
     * @since [*next-version*]
     *
     * @return MockObject|BaseContainerInterface The new container.
     */
    public function createContainer()
    {
        $mock = $this->getMockBuilder('Psr\Container\ContainerInterface')
            ->getMock();

        return $mock;
    }

    /**
     * Tests whether a valid instance of the test subject can be created.
     *
     * @since [*next-version*]
     */
    public function testCanBeCreated()
    {
        $subject = $this->createInstance([], [[], $this->createCacheContainer()]);

        $this->assertInstanceOf(
            static::TEST_SUBJECT_CLASSNAME,
            $subject,
            'A valid instance of the test subject could not be created.'
        );
    }

    /**
     * Tests that `get()` works correctly.
     *
     * It must correctly retrieve the services from definitions, invoking callable definitions only once,
     * and retrieving simple services as is. The services must be cached, and subsequent requests should return the
     * cached versions.
     *
     * @since [*next-version*]
     */
    public function testGet()
    {
        $key1 = uniqid('key');
        $service1 = new stdClass();
        $def1 = $this->createCallable();
        $key2 = uniqid('key');
        $service2 = new stdClass();
        $def2 = $service2;
        $services = [
            $key1 => $def1,
            $key2 => $def2,
        ];
        $cache = $this->createCacheContainer();
        $subject = $this->createInstance([], [$services, $cache]);
        $_subject = $this->reflect($subject);

        $def1->expects($this->exactly(1))
            ->method('__invoke')
            ->with($subject)
            ->will($this->returnValue($service1));

        $result1A = $subject->get($key1);
        $this->assertSame($service1, $result1A, 'Wrong new service retrieved');
        $result1B = $subject->get($key1);
        $this->assertSame($service1, $result1B, 'Wrong cached service retrieved');

        $result2 = $subject->get($key2);
        $this->assertSame($service2, $result2, 'Wrong simple service returned');
    }
}
