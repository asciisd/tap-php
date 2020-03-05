<?php


namespace Tap;


class ProductTest extends TestCase
{
    const TEST_RESOURCE_ID = 'prd_IDto1311012xckQ527446';

    public function testIsListable()
    {
        $this->expectsRequest(
            'post',
            '/v2/products/list'
        );
        $resources = Product::all(['limit' => 25]);
        $this->assertTrue(is_array($resources->products));
        $this->assertInstanceOf(Product::class, $resources->products[0]);
    }

    public function testIsRetrievable()
    {
        $this->expectsRequest(
            'get',
            '/v2/products/' . self::TEST_RESOURCE_ID
        );
        $resource = Product::retrieve(self::TEST_RESOURCE_ID);
        $this->assertInstanceOf(Product::class, $resource);
    }

    public function testIsCreatable()
    {
        $this->expectsRequest(
            'post',
            '/v2/products'
        );
        $resource = Product::create([
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
        ]);
        $this->assertInstanceOf(Product::class, $resource);
    }

    public function testIsSaveable()
    {
        $resource = Product::retrieve(self::TEST_RESOURCE_ID);
        $resource->description = "test from tap-php package";
        $this->expectsRequest(
            'put',
            '/v2/products/' . $resource->id
        );
        $resource->save();
        $this->assertInstanceOf(Product::class, $resource);
        $this->assertEquals("test from tap-php package", $resource->description);
    }

    public function testIsUpdatable()
    {
        $this->expectsRequest(
            'put',
            '/v2/products/' . self::TEST_RESOURCE_ID
        );
        $resource = Product::update(self::TEST_RESOURCE_ID, [
            "amount" => 100,
            "currency" => "KWD",
            "description" => "test",
            "discount" => [
                "type" => "P",
                "value" => 0
            ],
            "image" => "",
            "name" => "test",
            "quantity" => 1
        ]);
        $this->assertInstanceOf(Product::class, $resource);
        $this->assertEquals(100, $resource->amount);
    }

    public function testIsDeletable()
    {
        $resource = Product::retrieve(self::TEST_RESOURCE_ID);
        $this->expectsRequest(
            'delete',
            '/v2/products/' . $resource->id
        );
        $resource->delete();
        $this->assertInstanceOf(Product::class, $resource);
        $this->assertEquals("CANCELLED", $resource->status);

    }
}
