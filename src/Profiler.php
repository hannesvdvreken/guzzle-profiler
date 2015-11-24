<?php
namespace GuzzleHttp\Profiling;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

interface Profiler
{
    /**
     * @param float $start
     * @param float $end
     * @param \Psr\Http\Message\RequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     */
    public function add($start, $end, RequestInterface $request, ResponseInterface $response = null);
}
