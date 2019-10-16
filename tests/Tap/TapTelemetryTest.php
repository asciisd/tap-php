<?php

namespace Tap;

class TapTelemetryTest extends TestCase
{
    const TEST_RESOURCE_ID = 'acct_123';
    const TEST_EXTERNALACCOUNT_ID = 'ba_123';
    const TEST_PERSON_ID = 'person_123';

    const FAKE_VALID_RESPONSE = '{
      "data": [],
      "has_more": false,
      "object": "list",
      "url": "/v2/customers"
    }';

    protected function setUp(): void
    {
        parent::setUp();

        // clear static telemetry data
        ApiRequestor::resetTelemetry();
    }

    public function testNoTelemetrySentIfNotEnabled()
    {
        $requestHeaders = null;

        $stub = $this
            ->getMockBuilder("Tap\HttpClient\ClientInterface")
            ->setMethods(['request'])
            ->getMock();

        $stub->expects($this->any())
            ->method("request")
            ->with(
                $this->anything(),
                $this->anything(),
                $this->callback(function ($headers) use (&$requestHeaders) {
                    foreach ($headers as $index => $header) {
                        // capture the requested headers and format back to into an assoc array
                        $components = explode(": ", $header, 2);
                        $requestHeaders[$components[0]] = $components[1];
                    }

                    return true;
                }),
                $this->anything(),
                $this->anything()
            )->willReturn(array(self::FAKE_VALID_RESPONSE, 200, ["request-id" => "123"]));

        ApiRequestor::setHttpClient($stub);

        // make one request to capture its result
        Customer::all();
        $this->assertArrayNotHasKey('X-Tap-Client-Telemetry', $requestHeaders);

        // make another request and verify telemetry isn't sent
        Customer::all();

        $this->assertArrayNotHasKey('X-Tap-Client-Telemetry', $requestHeaders);

        ApiRequestor::setHttpClient(null);
    }

    public function testTelemetrySetIfEnabled()
    {
        Tap::setEnableTelemetry(true);

        $requestHeaders = null;

        $stub = $this
            ->getMockBuilder("Tap\HttpClient\ClientInterface")
            ->setMethods(['request'])
            ->getMock();

        $stub->expects($this->any())
            ->method("request")
            ->with(
                $this->anything(),
                $this->anything(),
                $this->callback(function ($headers) use (&$requestHeaders) {
                    // capture the requested headers and format back to into an assoc array
                    foreach ($headers as $index => $header) {
                        $components = explode(": ", $header, 2);
                        $requestHeaders[$components[0]] = $components[1];
                    }

                    return true;
                }),
                $this->anything(),
                $this->anything()
            )->willReturn(array(self::FAKE_VALID_RESPONSE, 200, ["request-id" => "123"]));

        ApiRequestor::setHttpClient($stub);

        // make one request to capture its result
        Charge::all();
        $this->assertArrayNotHasKey('X-Tap-Client-Telemetry', $requestHeaders);

        // make another request to send the previous
        Charge::all();
        $this->assertArrayHasKey('X-Tap-Client-Telemetry', $requestHeaders);

        $data = json_decode($requestHeaders['X-Tap-Client-Telemetry'], true);
        $this->assertEquals('123', $data['last_request_metrics']['request_id']);
        $this->assertNotNull($data['last_request_metrics']['request_duration_ms']);

        ApiRequestor::setHttpClient(null);
    }
}
