<?php


namespace Tap;


class ChargeTest extends TestCase
{
    const TEST_RESOURCE_ID = 'chg_i4P93920192134Dy5g1410243';

    public function testIsListable()
    {
        $this->expectsRequest(
            'post',
            '/v2/charges/list'
        );
        $resources = Charge::all();
        $this->assertTrue(is_array($resources->charges));
        $this->assertInstanceOf(Charge::class, $resources->charges[0]);
    }

    public function testIsRetrievable()
    {
        $this->expectsRequest(
            'get',
            '/v2/charges/' . self::TEST_RESOURCE_ID
        );
        $resource = Charge::retrieve(self::TEST_RESOURCE_ID);
        $this->assertInstanceOf(Charge::class, $resource);
    }

    public function testIsCreatable()
    {
        $this->expectsRequest(
            'post',
            '/v2/charges'
        );
        $resource = Charge::create([
            "amount" => 100,
            "currency" => "USD",
            "source" => ['id' => 'src_card'],
            "customer" => ['id' => TestObject::$test_resource_id],
            "redirect" => [
                "url" => "http://payment.test/tap/handle"
            ]
        ]);
        $this->assertInstanceOf(Charge::class, $resource);
    }

    public function testIsSaveable()
    {
        $resource = Charge::retrieve(self::TEST_RESOURCE_ID);
        $resource->metadata["key"] = "value";
        $this->expectsRequest(
            'put',
            '/v2/charges/' . $resource->id
        );
        $resource->save();
        $this->assertInstanceOf(Charge::class, $resource);
    }

    public function testIsUpdatable()
    {
        $this->expectsRequest(
            'put',
            '/v2/charges/' . self::TEST_RESOURCE_ID
        );
        $resource = Charge::update(self::TEST_RESOURCE_ID, [
            "metadata" => ["udf2" => "test"],
        ]);
        $this->assertInstanceOf(Charge::class, $resource);
    }

//    public function testCanCapture()
//    {
//        $charge = Charge::retrieve(self::TEST_RESOURCE_ID);
//        $this->expectsRequest(
//            'post',
//            '/v2/charges/' . $charge->id . '/capture'
//        );
//        $resource = $charge->capture();
//        $this->assertInstanceOf(\Tap\Charge::class, $resource);
//        $this->assertSame($resource, $charge);
//    }
}
