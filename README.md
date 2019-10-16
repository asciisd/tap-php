# Tap PHP bindings

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Total Downloads][ico-downloads]][link-downloads]

The Tap PHP library provides convenient access to the Tap API from applications written in the PHP language. It includes a pre-defined set of classes for API resources that initialize themselves dynamically from API responses which makes it compatible with a wide range of versions of the Tap API.

## Requirements

PHP 7.1.0 and later.

## Install

You can install the bindings via [Composer](http://getcomposer.org/). Run the following command:

``` bash
$ composer require asciisd/tap-php
```

To use the bindings, use Composer's [autoload](https://getcomposer.org/doc/01-basic-usage.md#autoloading):

```php
require_once('vendor/autoload.php');
```

## Manual Installation

If you do not wish to use Composer, you can download the [latest release](https://github.com/asciisd/tap-php/releases). Then, to use the bindings, include the `init.php` file.

```php
require_once('/path/to/tap-php/init.php');
```

## Dependencies

The bindings require the following extensions in order to work properly:

- [`curl`](https://secure.php.net/manual/en/book.curl.php), although you can use your own non-cURL client if you prefer
- [`json`](https://secure.php.net/manual/en/book.json.php)
- [`mbstring`](https://secure.php.net/manual/en/book.mbstring.php) (Multibyte String)

If you use Composer, these dependencies should be handled automatically. If you install manually, you'll want to make sure that these extensions are available.


## Usage

Simple usage looks like:

```php
\Tap\Tap::setApiKey('sk_test_XKokBfNWv6FIYuTMg5sLPjhJ');
$charge = \Tap\Charge::create([
    'amount' => 2000, 
    'currency' => 'usd', 
    'source' => ['id' => 'tok_189fqt2eZvKYlo2CTGBeg6Uq'], 
    'customer' => ['id' => 'cus_w4MN2720192134x9XB1510264']
]);
echo $charge;
```

## Documentation

See the [PHP API docs](https://asciisd.com/tap/docs/api/php#intro).

## Custom Request Timeouts

*NOTE:* We do not recommend decreasing the timeout for non-read-only calls (e.g. charge creation), since even if you locally timeout, the request on Tap's side can still complete. If you are decreasing timeouts on these calls, make sure to use [idempotency tokens](https://tap.com/docs/api/php#idempotent_requests) to avoid executing the same transaction twice as a result of timeout retry logic.

To modify request timeouts (connect or total, in seconds) you'll need to tell the API client to use a CurlClient other than its default. You'll set the timeouts in that CurlClient.

```php
// set up your tweaked Curl client
$curl = new \Tap\HttpClient\CurlClient();
$curl->setTimeout(10); // default is \Tap\HttpClient\CurlClient::DEFAULT_TIMEOUT
$curl->setConnectTimeout(5); // default is \Tap\HttpClient\CurlClient::DEFAULT_CONNECT_TIMEOUT

echo $curl->getTimeout(); // 10
echo $curl->getConnectTimeout(); // 5

// tell Tap to use the tweaked client
\Tap\ApiRequestor::setHttpClient($curl);

// use the Tap API client as you normally would
```

### Configuring a Logger

The library does minimal logging, but it can be configured
with a [`PSR-3` compatible logger][psr3] so that messages
end up there instead of `error_log`:

```php
\Tap\Tap::setLogger($logger);
```

### Accessing response data

You can access the data from the last API response on any object via `getLastResponse()`.

```php
$charge = \Tap\Charge::create([
    'amount' => 2000, 
    'currency' => 'usd', 
    'source' => ['id' => 'tok_189fqt2eZvKYlo2CTGBeg6Uq'], 
    'customer' => ['id' => 'cus_w4MN2720192134x9XB1510264']
]);
echo $charge->getLastResponse()->headers['Request-Id'];
```

### SSL / TLS compatibility issues

Tap's API now requires that [all connections use TLS 1.2] . Some systems (most notably some older CentOS and RHEL versions) are capable of using TLS 1.2 but will use TLS 1.0 or 1.1 by default. In this case, you'd get an `invalid_request_error` with the following error message: "Tap no longer supports API requests made with TLS 1.0. Please initiate HTTPS connections with TLS 1.2 or later.".

The recommended course of action is to upgrade your cURL and OpenSSL packages so that TLS 1.2 is used by default, but if that is not possible, you might be able to solve the issue by setting the `CURLOPT_SSLVERSION` option to either `CURL_SSLVERSION_TLSv1` or `CURL_SSLVERSION_TLSv1_2`:

```php
$curl = new \Tap\HttpClient\CurlClient([CURLOPT_SSLVERSION => CURL_SSLVERSION_TLSv1]);
\Tap\ApiRequestor::setHttpClient($curl);
```

### Per-request Configuration

For apps that need to use multiple keys during the lifetime of a process, like
one that uses [Tap Connect][connect], it's also possible to set a
per-request key and/or account:

```php
\Tap\Charge::all([], [
    'api_key' => 'sk_test_...',
    'amount' => 2000, 
    'currency' => 'usd', 
    'source' => ['id' => 'tok_189fqt2eZvKYlo2CTGBeg6Uq'], 
    'customer' => ['id' => 'cus_w4MN2720192134x9XB1510264']
]);

\Tap\Charge::retrieve("ch_18atAXCdGbJFKhCuBAa4532Z", [
    'api_key' => 'sk_test_...',
    'amount' => 2000, 
    'currency' => 'usd', 
    'source' => ['id' => 'tok_189fqt2eZvKYlo2CTGBeg6Uq'], 
    'customer' => ['id' => 'cus_w4MN2720192134x9XB1510264']
]);
```

### Configuring CA Bundles

By default, the library will use its own internal bundle of known CA
certificates, but it's possible to configure your own:

```php
\Tap\Tap::setCABundlePath("path/to/ca/bundle");
```

### Configuring Automatic Retries

The library can be configured to automatically retry requests that fail due to
an intermittent network problem:

```php
\Tap\Tap::setMaxNetworkRetries(2);
```

[Idempotency keys][idempotency-keys] are added to requests to guarantee that
retries are safe.

### Request latency telemetry

By default, the library sends request latency telemetry to Tap. These
numbers help Tap improve the overall latency of its API for all users.

You can disable this behavior if you prefer:

```php
\Tap\Tap::setEnableTelemetry(false);
```

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Testing

``` bash
$ composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CONDUCT](CONDUCT.md) for details.

## Security

If you discover any security related issues, please email aemad@asciisd.com instead of using the issue tracker.

## Credits

- [Amr Emad][link-author]
- [Stripe][link-stripe] : this library is based on the stripe-php package infrastructure, so thanks Stripe.
- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/asciisd/tap-php.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/asciisd/tap-php/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/asciisd/tap-php.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/asciisd/tap-php.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/asciisd/tap-php.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/asciisd/tap-php
[link-travis]: https://travis-ci.org/asciisd/tap-php
[link-scrutinizer]: https://scrutinizer-ci.com/g/asciisd/tap-php/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/asciisd/tap-php
[link-downloads]: https://packagist.org/packages/asciisd/tap-php
[link-author]: https://github.com/aemaddin
[link-contributors]: ../../contributors
[link-stripe]: https://stripe.com
