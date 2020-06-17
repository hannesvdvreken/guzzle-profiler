<?php
namespace GuzzleHttp\Profiling\Unit;

use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\TransferException;
use GuzzleHttp\Profiling\Middleware;
use GuzzleHttp\Profiling\Profiler;
use GuzzleHttp\Promise\Promise;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;

class MiddlewareTest extends TestCase
{
    public function testMiddlewareReturnsCallable()
    {
        // Arrange
        $profiler = $this->createMock(Profiler::class);
        $middleware = new Middleware($profiler);

        $called = false;

        $handler = function() use (&$called) {
            if (!$called) {
                $called = true;
            }
        };

        // Act
        $function = $middleware($handler);

        // Assert
        $this->assertTrue(is_callable($function));
        $this->assertFalse($called);
    }

    public function testNextMiddlewareIsCalled()
    {
        // Arrange
        $profiler = $this->createMock(Profiler::class);
        $middleware = new Middleware($profiler);
        $options = [
            'random' => 'data',
        ];

        $promise = $this->getMockBuilder(Promise::class)->getMock();
        $promise
            ->expects($this->once())
            ->method('then')
            ->with(
                $this->callback(function ($callback) {
                    return true;
                }),
                $this->callback(function ($callback) {
                    return true;
                })
            )->willReturn($promise);

        // Next handler that will be passed when creating the inner middleware function.
        $called = false;

        $handler = function(RequestInterface $request, array $passedOptions) use (&$called, $promise, $options) {
            if (!$called) {
                $called = true;
            }

            $this->assertEquals($options, $passedOptions);

            return $promise;
        };

        // Get inner callable.
        $middleware = $middleware($handler);

        $request = new Request('GET', 'https://httpbin.org/status/200');

        // Act
        $returnedPromise = $middleware($request, $options);

        // Assert
        $this->assertTrue($called);
        $this->assertEquals($promise, $returnedPromise);
    }

    function testFulfilledCallback() {
        // Arrange
        $profiler = $this->getMockBuilder(Profiler::class)->getMock();
        $middleware = new Middleware($profiler);

        $request = new Request('GET', 'https://httpbin.org/status/200');
        $response = new Response();

        $profiler
            ->expects($this->once())
            ->method('add')
            ->with(
                $this->greaterThan(microtime(true)),
                $this->greaterThan(microtime(true)),
                $request,
                $response
            );

        $promise = $this->getMockBuilder(Promise::class)->getMock();
        $promise
            ->expects($this->once())
            ->method('then')
            ->with(
                $this->callback(function ($callback) use ($response) {
                    $callback($response);
                    return true;
                }),
                $this->callback(function ($callback) {
                    return true;
                })
            )->willReturn($promise);

        // Next handler that will be passed when creating the inner middleware function.
        $called = false;

        $handler = function(RequestInterface $request, array $options) use (&$called, $promise) {
            if (!$called) {
                $called = true;
            }

            return $promise;
        };

        // Get inner callable.
        $middleware = $middleware($handler);

        // Act
        $returnedPromise = $middleware($request, []);

        // Assert
        $this->assertEquals($promise, $returnedPromise);
    }

    function testRejectionCallbackNonRequestException() {
        // Arrange
        $profiler = $this->getMockBuilder(Profiler::class)->getMock();
        $middleware = new Middleware($profiler);

        $request = new Request('GET', 'https://httpbin.org/status/200');

        $profiler
            ->expects($this->once())
            ->method('add')
            ->with(
                $this->greaterThan(microtime(true)),
                $this->greaterThan(microtime(true)),
                $request,
                null
            );

        $exception = new TransferException();

        $promise = $this->getMockBuilder(Promise::class)->getMock();
        $promise
            ->expects($this->once())
            ->method('then')
            ->with(
                $this->callback(function ($callback) {
                    return true;
                }),
                $this->callback(function ($callback) use ($exception) {
                    try {
                        $callback($exception);
                    } catch (\Exception $thrownException) {
                        $this->assertEquals($exception, $thrownException);
                    }

                    return true;
                })
            )->willReturn($promise);

        // Next handler that will be passed when creating the inner middleware function.
        $called = false;

        $handler = function(RequestInterface $request, array $options) use (&$called, $promise) {
            if (!$called) {
                $called = true;
            }

            return $promise;
        };

        // Get inner callable.
        $middleware = $middleware($handler);

        // Act
        $returnedPromise = $middleware($request, []);

        // Assert
        $this->assertEquals($promise, $returnedPromise);
    }

    function testRejectionCallbackRequestException() {
        // Arrange
        $profiler = $this->getMockBuilder(Profiler::class)->getMock();
        $middleware = new Middleware($profiler);

        $request = new Request('GET', 'https://httpbin.org/status/200');
        $response = new Response();

        $profiler
            ->expects($this->once())
            ->method('add')
            ->with(
                $this->greaterThan(microtime(true)),
                $this->greaterThan(microtime(true)),
                $request,
                $response
            );

        $exception = RequestException::create($request, $response);

        $promise = $this->getMockBuilder(Promise::class)->getMock();
        $promise
            ->expects($this->once())
            ->method('then')
            ->with(
                $this->callback(function ($callback) {
                    return true;
                }),
                $this->callback(function ($callback) use ($exception) {
                    try {
                        $callback($exception);
                    } catch (\Exception $thrownException) {
                        $this->assertEquals($exception, $thrownException);
                    }

                    return true;
                })
            )->willReturn($promise);

        // Next handler that will be passed when creating the inner middleware function.
        $called = false;

        $handler = function(RequestInterface $request, array $options) use (&$called, $promise) {
            if (!$called) {
                $called = true;
            }

            return $promise;
        };

        // Get inner callable.
        $middleware = $middleware($handler);

        // Act
        $returnedPromise = $middleware($request, []);

        // Assert
        $this->assertEquals($promise, $returnedPromise);
    }
}
