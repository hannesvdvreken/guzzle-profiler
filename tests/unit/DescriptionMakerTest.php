<?php
namespace GuzzleHttp\Profiling\Unit;

use GuzzleHttp\Profiling\Unit\Stubs\DescriptionMaker;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class DescriptionMakerTest extends TestCase
{
    public function testWithResponse()
    {
        // Arrange
        $response = new Response();
        $request = new Request('GET', 'https://httpbin.org/status/200');

        $maker = new DescriptionMaker();

        // Act
        $description = $maker->describe($request, $response);

        // Assert
        $this->assertEquals('GET https://httpbin.org/status/200 returned 200 OK', $description);
    }

    public function testWithoutResponse()
    {
        // Arrange
        $request = new Request('GET', 'http://invalid-url');

        $maker = new DescriptionMaker();

        // Act
        $description = $maker->describe($request, null);

        // Assert
        $this->assertEquals('GET http://invalid-url failed', $description);
    }
}
