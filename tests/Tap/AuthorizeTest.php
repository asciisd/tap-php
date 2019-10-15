<?php


namespace Tap;


class AuthorizeTest extends TestCase
{
    const TEST_RESOURCE_ID = 'auth_b4PZ1320192154w4P91410635';

    public function testIsListable()
    {
        $this->expectsRequest(
            'post',
            '/v2/authorize/list'
        );

        $resources = Authorize::all(['limit' => 50]);
        $this->assertTrue(is_array($resources->authorizes));
        $this->assertInstanceOf(Authorize::class, $resources->authorizes[0]);
    }

    public function testIsRetrievable()
    {
        $this->expectsRequest(
            'get',
            '/v2/authorize/' . self::TEST_RESOURCE_ID
        );
        $resource = Authorize::retrieve(self::TEST_RESOURCE_ID);
        $this->assertInstanceOf(Authorize::class, $resource);
    }

    public function testIsCreatable()
    {
        $token = Token::create(TestObject::$testCard);
        $this->expectsRequest(
            'post',
            '/v2/authorize'
        );
        $resource = Authorize::create([
            'amount' => 100,
            'currency' => 'USD',
            'source' => ['id' => $token->id],
            'customer' => ['id' => TestObject::$test_resource_id],
            'redirect' => ['url' => 'https://asciisd.com/tap/v2/authorize']
        ]);
        $this->assertInstanceOf(Authorize::class, $resource);
    }

    public function testIsSavable()
    {
        $resource = Authorize::retrieve(self::TEST_RESOURCE_ID);
        $resource->description = 'testIsSavable';
        $this->expectsRequest(
            'put',
            '/v2/authorize/' . $resource->id
        );
        $resource->save();
        $this->assertInstanceOf(Authorize::class, $resource);
    }

    public function testIsUpdatable()
    {
        $this->expectsRequest(
            'put',
            '/v2/authorize/' . self::TEST_RESOURCE_ID
        );
        $resource = Authorize::update(self::TEST_RESOURCE_ID, [
            'description' => 'testIsUpdatable',
        ]);
        $this->assertInstanceOf(Authorize::class, $resource);
    }

    public function testIsVoidable()
    {
        $resource = Authorize::retrieve(self::TEST_RESOURCE_ID);
        $this->expectsRequest(
            'post',
            '/v2/authorize/' . self::TEST_RESOURCE_ID . '/void'
        );
        $resource->void();
        $this->assertInstanceOf(Authorize::class, $resource);
    }
}
