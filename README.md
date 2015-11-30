# [Guzzle client](http://docs.guzzlephp.org/en/latest/) middleware to profile HTTP requests.

[![Build Status](http://img.shields.io/travis/hannesvdvreken/guzzle-profiler.svg?style=flat-square)](https://travis-ci.org/hannesvdvreken/guzzle-profiler)
[![Latest Stable Version](http://img.shields.io/packagist/v/hannesvdvreken/guzzle-profiler.svg?style=flat-square)](https://packagist.org/packages/hannesvdvreken/guzzle-profiler)
[![Code Quality](https://img.shields.io/scrutinizer/g/hannesvdvreken/guzzle-profiler.svg?style=flat-square)](https://scrutinizer-ci.com/g/hannesvdvreken/guzzle-profiler/)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/hannesvdvreken/guzzle-profiler.svg?style=flat-square)](https://scrutinizer-ci.com/g/hannesvdvreken/guzzle-profiler/)
[![Total Downloads](http://img.shields.io/packagist/dt/hannesvdvreken/guzzle-profiler.svg?style=flat-square)](https://packagist.org/packages/hannesvdvreken/guzzle-profiler)
[![License](http://img.shields.io/packagist/l/hannesvdvreken/guzzle-profiler.svg?style=flat-square)](#license)

Guzzle Middleware to log made HTTP requests to a timeline for debugging.

## Adapter

A couple of adapters are available:

- [`hannesvdvreken/guzzle-debugbar`](https://github.com/hannesvdvreken/guzzle-debugbar)
- [`hannesvdvreken/guzzle-clockwork`](https://github.com/hannesvdvreken/guzzle-clockwork)

## Usage

```php
// First you need a HandlerStack
$stack = GuzzleHttp\HandlerStack::create();

// Create a middleware by wrapping a profiler (eg: DebugBar's profiler):
/** @var DebugBar\DebugBar $debugBar */
$timeline = $debugBar->getCollector('time');
$profiler = new GuzzleHttp\Profiling\DebugBar\Profiler($timeline);
$middleware = new GuzzleHttp\Profiling\Middleware($profiler);

// Add the Middleware to the stack of middlewares.
$stack->unshift($middleware);

// Then you need to add it to the Guzzle HandlerStack
$stack = GuzzleHttp\HandlerStack::create();

$stack->unshift($middleware);

// Create a Guzzle Client with the new HandlerStack:
$client = new GuzzleHttp\Client(['handler' => $stack]);
```

And you are done! All requests will now be logged to whatever profiler you wrapped.

## Contributing

Feel free to make a pull request. Please try to be as
[PSR-2](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md)
compliant as possible. Fix Code Style quickly by running `vendor/bin/php-cs-fixer fix`. Give a good description of what is supposed to be added/changed/removed/fixed.

### Testing

To test your code before pushing, run the unit test suite.

```bash
vendor/bin/phpunit
```

## License

[MIT](LICENSE)
