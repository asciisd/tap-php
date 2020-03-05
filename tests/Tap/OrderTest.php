<?php


namespace Tap;


class OrderTest extends TestCase
{
    const TEST_RESOURCE_ID = 'ord_y1Di1311012XkP5527986';

    public function testIsListable()
    {
        $this->expectsRequest(
            'post',
            '/v2/orders/list'
        );
        $resources = Order::all(['limit' => 25]);
        $this->assertTrue(is_array($resources->orders));
        $this->assertInstanceOf(Order::class, $resources->orders[0]);
    }

    public function testIsRetrievable()
    {
        $this->expectsRequest(
            'get',
            '/v2/orders/' . self::TEST_RESOURCE_ID
        );
        $resource = Order::retrieve(self::TEST_RESOURCE_ID);
        $this->assertInstanceOf(Order::class, $resource);
    }

    public function testIsCreatable()
    {
        $this->expectsRequest(
            'post',
            '/v2/orders'
        );
        $resource = Order::create([
            "amount" => 100,
            "currency" => "USD",
            "items" => [
                [
                    "amount" => 10,
                    "currency" => "KWD",
                    "description" => "test",
                    "discount" => [
                        "type" => "P",
                        "value" => 0
                    ],
                    "image" => "",
                    "name" => "test",
                    "quantity" => 1
                ]
            ],
            "shipping" => [
                "amount" => 1,
                "currency" => "KWD",
                "description" => "test",
                "provider" => "ARAMEX",
                "service" => "test"
            ],
            "tax" => [
                [
                    "description" => "test",
                    "name" => "VAT",
                    "rate" => [
                        "type" => "F",
                        "value" => 1
                    ]
                ]
            ]
        ]);
        $this->assertInstanceOf(Order::class, $resource);
    }

    public function testIsSaveable()
    {
        $resource = Order::retrieve(self::TEST_RESOURCE_ID);
        $resource->amount = 200;
        $this->expectsRequest(
            'put',
            '/v2/orders/' . $resource->id
        );
        $resource->save();
        $this->assertInstanceOf(Order::class, $resource);
        $this->assertEquals(200, $resource->amount);
    }

    public function testIsUpdatable()
    {
        $this->expectsRequest(
            'put',
            '/v2/orders/' . self::TEST_RESOURCE_ID
        );
        $resource = Order::update(self::TEST_RESOURCE_ID, [
            "amount" => 200,
            "currency" => "KWD",
            "items" => [
                [
                    "amount" => 20,
                    "currency" => "KWD",
                    "description" => "test",
                    "discount" => [
                        "type" => "P",
                        "value" => 0
                    ],
                    "image" => "",
                    "name" => "test",
                    "quantity" => 1
                ]
            ],
            "shipping" => [
                "amount" => 1,
                "currency" => "KWD",
                "description" => "test",
                "provider" => "ARAMEX",
                "service" => "test"
            ],
            "tax" => [
                [
                    "description" => "test",
                    "name" => "VAT",
                    "rate" => [
                        "type" => "F",
                        "value" => 1
                    ]
                ]
            ]
        ]);
        $this->assertInstanceOf(Order::class, $resource);
        $this->assertEquals("KWD", $resource->currency);
        $this->assertEquals(200, $resource->amount);
    }

    public function testIsDeletable() {
        $resource = Order::retrieve(self::TEST_RESOURCE_ID);
        $this->expectsRequest(
            'delete',
            '/v2/orders/' . $resource->id
        );
        $resource->delete();
        $this->assertInstanceOf(Order::class, $resource);
        $this->assertEquals("CANCELLED", $resource->status);

    }
}
