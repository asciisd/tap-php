<?php

namespace Tap;

use PHPUnit\Framework\MockObject\Builder\InvocationMocker;
use PHPUnit\Framework\TestCase as FWTestCase;

/**
 * Base class for Tap test cases.
 */
class TestCase extends FWTestCase
{
    /** @var string original API base URL */
    protected $origApiBase;

    /** @var string original API key */
    protected $origApiKey;

    /** @var string original API version */
    protected $origApiVersion;

    /** @var object HTTP client mocker */
    protected $clientMock;

    protected function setUp(): void
    {
        parent::setUp();

        // Save original values so that we can restore them after running tests
        $this->origApiBase = Tap::$apiBase;
        $this->origApiKey = Tap::getApiKey();
        $this->origApiVersion = Tap::getApiVersion();

        // Set up the HTTP client mocker
        $this->clientMock = $this->createMock('\Tap\HttpClient\ClientInterface');

        // Set up host and credentials for tap-mock
        Tap::$apiBase = 'https://api.tap.company';
        Tap::setApiKey("sk_test_XKokBfNWv6FIYuTMg5sLPjhJ");
        Tap::setApiVersion('v2');
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        // Restore original values
        Tap::$apiBase = $this->origApiBase;
        Tap::setApiKey($this->origApiKey);
        Tap::setApiVersion($this->origApiVersion);
    }

    /**
     * Sets up a request expectation with the provided parameters. The request
     * will actually go through and be emitted.
     *
     * @param string $method HTTP method (e.g. 'post', 'get', etc.)
     * @param string $path relative path (e.g. '/v1/charges')
     * @param ?array $params array of parameters. If null, parameters will
     *   not be checked.
     * @param ?string[] $headers array of headers. Does not need to be
     *   exhaustive. If null, headers are not checked.
     * @param bool $hasFile Whether the request parameters contains a file.
     *   Defaults to false.
     * @param ?string $base base URL (e.g. 'https://api.tap.com')
     */
    protected function expectsRequest(
        $method,
        $path,
        $params = null,
        $headers = null,
        $hasFile = false,
        $base = null
    )
    {
        $this->prepareRequestMock($method, $path, $params, $headers, $hasFile, $base)
            ->will($this->returnCallback(
                function ($method, $absUrl, $headers, $params, $hasFile) {
                    $curlClient = HttpClient\CurlClient::instance();
                    ApiRequestor::setHttpClient($curlClient);
                    return $curlClient->request($method, $absUrl, $headers, $params, $hasFile);
                }
            ));
    }

    /**
     * Sets up a request expectation with the provided parameters. The request
     * will not actually be emitted, instead the provided response parameters
     * will be returned.
     *
     * @param string $method HTTP method (e.g. 'post', 'get', etc.)
     * @param string $path relative path (e.g. '/v1/charges')
     * @param ?array $params array of parameters. If null, parameters will
     *   not be checked.
     * @param ?string[] $headers array of headers. Does not need to be
     *   exhaustive. If null, headers are not checked.
     * @param bool $hasFile Whether the request parameters contains a file.
     *   Defaults to false.
     * @param array $response
     * @param integer $rcode
     * @param ?string $base
     *
     * @return array
     */
    protected function stubRequest(
        $method,
        $path,
        $params = null,
        $headers = null,
        $hasFile = false,
        $response = [],
        $rcode = 200,
        $base = null
    )
    {
        $this->prepareRequestMock($method, $path, $params, $headers, $hasFile, $base)
            ->willReturn([json_encode($response), $rcode, []]);
    }

    /**
     * Prepares the client mocker for an invocation of the `request` method.
     * This helper method is used by both `expectsRequest` and `stubRequest` to
     * prepare the client mocker to expect an invocation of the `request` method
     * with the provided arguments.
     *
     * @param string $method HTTP method (e.g. 'post', 'get', etc.)
     * @param string $path relative path (e.g. '/v1/charges')
     * @param ?array $params array of parameters. If null, parameters will
     *   not be checked.
     * @param ?string[] $headers array of headers. Does not need to be
     *   exhaustive. If null, headers are not checked.
     * @param bool $hasFile Whether the request parameters contains a file.
     *   Defaults to false.
     * @param ?string $base base URL (e.g. 'https://api.tap.com')
     *
     * @return InvocationMocker
     */
    private function prepareRequestMock(
        $method,
        $path,
        $params = null,
        $headers = null,
        $hasFile = false,
        $base = null
    )
    {
        ApiRequestor::setHttpClient($this->clientMock);

        if ($base === null) {
            $base = Tap::$apiBase;
        }
        $absUrl = $base . $path;

        return $this->clientMock
            ->expects($this->once())
            ->method('request')
            ->with(
                $this->identicalTo(strtolower($method)),
                $this->identicalTo($absUrl),
                // for headers, we only check that all of the headers provided in $headers are
                // present in the list of headers of the actual request
                $headers === null ? $this->anything() : $this->callback(function ($array) use ($headers) {
                    foreach ($headers as $header) {
                        if (!in_array($header, $array)) {
                            return false;
                        }
                    }
                    return true;
                }),
                $params === null ? $this->anything() : $this->identicalTo($params),
                $this->identicalTo($hasFile)
            );
    }
}
