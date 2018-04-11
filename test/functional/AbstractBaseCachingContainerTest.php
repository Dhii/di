<?php

namespace Dhii\Di\FuncTest;

use Dhii\Di\AbstractBaseContainer as TestSubject;
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
class AbstractBaseCachingContainerTest extends TestCase
{
    /**
     * The class name of the test subject.
     *
     * @since [*next-version*]
     */
    const TEST_SUBJECT_CLASSNAME = 'Dhii\Di\AbstractBaseCachingContainer';

    /**
     * Creates a new instance of the test subject.
     *
     * @since [*next-version*]
     *
     * @param array $methods The methods to mock.
     *
     * @return MockObject|TestSubject The new instance.
     */
    public function createInstance($methods = [])
    {
        is_array($methods) && $methods = $this->mergeValues($methods, [
            '__',
        ]);

        $mock = $this->getMockBuilder(static::TEST_SUBJECT_CLASSNAME)
            ->setMethods($methods)
            ->getMockForAbstractClass();

        $mock->method('__')
                ->will($this->returnArgument(0));

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
        $subject = $this->createInstance(['_getArgsForDefinition']);

        $this->assertInstanceOf(
            static::TEST_SUBJECT_CLASSNAME,
            $subject,
            'A valid instance of the test subject could not be created.'
        );
    }

    /**
     * Tests that `has()` works correctly.
     *
     * If must report `true` if the container has a service definition with the requested key. It must report `false`
     * for any other key.
     *
     * @since [*next-version*]
     */
    public function testHas()
    {
        $key1 = uniqid('key');
        $service1 = new stdClass();
        $def1 = $this->createCallable();
        $key2 = uniqid('key');
        $service2 = new stdClass();
        $def2 = $service2;
        $key3 = uniqid('key');
        $container1 = $this->createContainer();
        $store = (object) [
            $key1 => $def1,
            $key2 => $def2,
        ];
        $subject = $this->createInstance(['_getDataStore']);
        $_subject = $this->reflect($subject);

        $subject->expects($this->any())
            ->method('_getDataStore')
            ->will($this->returnValue($store));

        $this->assertTrue($subject->has($key1));
        $this->assertTrue($subject->has($key2));
        $this->assertFalse($subject->has($key3));
    }

    /**
     * Tests that `_createReflectionFunction()` works correctly.
     *
     * @since [*next-version*]
     */
    public function testCreateReflectionFunction()
    {
        $functionName = 'sprintf';
        $subject = $this->createInstance();
        $_subject = $this->reflect($subject);

        $result = $_subject->_createReflectionFunction($functionName);
        $this->assertInstanceOf('ReflectionFunction', $result, 'Reflection has wrong type');
        $this->assertEquals($functionName, $result->getName(), 'Reflection has wrong function name');
    }

    /**
     * Tests that `_createReflectionMethod()` works correctly.
     *
     * @since [*next-version*]
     */
    public function testCreateReflectionMethod()
    {
        $methodName = 'count';
        $className = 'ArrayObject';
        $subject = $this->createInstance();
        $_subject = $this->reflect($subject);

        $result = $_subject->_createReflectionMethod($className, $methodName);
        $this->assertInstanceOf('ReflectionMethod', $result, 'Reflection has wrong type');
        $this->assertEquals($result->getDeclaringClass()->getName(), $className, 'Reflection has wrong class name');
        $this->assertEquals($methodName, $result->getName(), 'Reflection has wrong method name');
    }
}
