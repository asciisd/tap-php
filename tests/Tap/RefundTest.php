<?php


namespace Tap;


class RefundTest extends TestCase
{
    const TEST_RESOURCE_ID = 're_y4HM5620190916c4J40910845';

    public function testIsListable()
    {
        $this->expectsRequest(
            'post',
            '/v2/refunds/list'
        );
        $resources = Refund::all();

        $this->assertTrue(is_array($resources->refunds));
        $this->assertInstanceOf(\Tap\Refund::class, $resources->refunds[0]);
    }
    public function testIsRetrievable()
    {
        $this->expectsRequest(
            'get',
            '/v2/refunds/' . self::TEST_RESOURCE_ID
        );
        $resource = Refund::retrieve(self::TEST_RESOURCE_ID);
        $this->assertInstanceOf(\Tap\Refund::class, $resource);
    }
    public function testIsCreatable()
    {
        $this->expectsRequest(
            'post',
            '/v2/refunds'
        );
        $resource = Refund::create([
            'charge_id' => 'chg_De2b2620191945Bo592010668',
            'amount' => 2,
            'currency' => 'KWD',
            'reason' => 'requested_by_customer'
        ]);
        $this->assertInstanceOf(\Tap\Refund::class, $resource);
    }
    public function testIsSaveable()
    {
        $resource = Refund::retrieve(self::TEST_RESOURCE_ID);
        $resource->metadata["test_case"] = "retrieve";
        $this->expectsRequest(
            'put',
            '/v2/refunds/' . $resource->id
        );
        $resource->save();
        $this->assertInstanceOf(\Tap\Refund::class, $resource);
    }
    public function testIsUpdatable()
    {
        $this->expectsRequest(
            'put',
            '/v2/refunds/' . self::TEST_RESOURCE_ID
        );
        $resource = Refund::update(self::TEST_RESOURCE_ID, [
            "metadata" => ["test_case" => "testIsUpdatable"],
        ]);
        $this->assertInstanceOf(\Tap\Refund::class, $resource);
    }
}
