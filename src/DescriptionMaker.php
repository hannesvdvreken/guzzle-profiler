<?php
namespace GuzzleHttp\Profiling;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

trait DescriptionMaker
{
    /**
     * @param \Psr\Http\Message\RequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     *
     * @return string
     */
    protected function describe(RequestInterface $request, ResponseInterface $response = null)
    {
        if (!$response) {
            return sprintf('%s %s failed', $request->getMethod(), $request->getUri());
        }

        return sprintf(
            '%s %s returned %s %s',
            $request->getMethod(),
            $request->getUri(),
            $response->getStatusCode(),
            $response->getReasonPhrase()
        );
    }
}
