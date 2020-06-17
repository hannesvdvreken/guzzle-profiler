<?php

namespace GuzzleHttp\Profiling;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class Middleware
{
    /**
     * @var Profiler
     */
    private $profiler;

    /**
     * Public constructor.
     *
     * @param Profiler $profiler
     */
    public function __construct(Profiler $profiler)
    {
        $this->profiler = $profiler;
    }

    /**
     * @param callable $handler
     *
     * @return callable
     */
    public function __invoke(callable $handler)
    {
        return function (RequestInterface $request, array $options) use ($handler) {
            // Set starting time.
            $start = microtime(true);

            return $handler($request, $options)
                ->then(function (ResponseInterface $response) use ($start, $request) {
                    // After
                    $this->profiler->add($start, microtime(true), $request, $response);

                    return $response;
                }, function (GuzzleException $exception) use ($start, $request) {
                    $response = $exception instanceof RequestException ? $exception->getResponse() : null;
                    $this->profiler->add($start, microtime(true), $request, $response);

                    throw $exception;
                });
        };
    }
}
