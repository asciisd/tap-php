<?php

namespace Tap;

use Tap\Exception\NotExistedMethodException;

class CardTest extends TestCase
{
    protected $customer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->customer = Customer::create(TestObject::$testCustomer);
    }

    public function testIsListable()
    {
        $this->expectsRequest(
            'get',
            '/v2/card/' . $this->customer->id
        );

        $resources = Card::all($this->customer->id);
        $this->assertTrue(is_array($resources->data));
    }

    public function testIsCreatable()
    {
        $token = Token::create(TestObject::$testCard);

        $this->expectsRequest(
            'post',
            '/v2/card/' . $this->customer->id
        );

        $resource = Card::createFromSource($this->customer->id, $token->id);
        $this->assertInstanceOf(Card::class, $resource);
    }

    public function testIsRetrievable()
    {
        $token = Token::create(TestObject::$testCard);
        $card = Card::createFromSource($this->customer->id, $token->id);

        $this->expectsRequest(
            'get',
            '/v2/card/' . $this->customer->id . '/' . $card->id
        );

        $resource = Card::retrieve($card->id, $this->customer->id);
        $this->assertInstanceOf(Card::class, $resource);
    }

    public function testIsUpdatable()
    {
        $this->expectException(NotExistedMethodException::class);

        Card::update(TestObject::$test_resource_id, 'no_source_id', ["name" => "New Card Holder"]);
    }

    public function testIsDeletable()
    {
        $token = Token::create(TestObject::$testCard);
        $card = Customer::createSource($this->customer->id, $token->id);

        $this->expectsRequest(
            'delete',
            '/v2/card/' . $this->customer->id . '/' . $card->id
        );

        $resource = Card::delete($card->id, $this->customer->id);
        $this->assertInstanceOf(TapObject::class, $resource);
    }

    public function testItCanBeVerified()
    {
        $token = Token::create(TestObject::$testCard);

        $this->expectsRequest(
            'post',
            '/v2/card/verify'
        );

        $resource = Card::verify([
            'source' => ['id' => $token->id],
            'customer' => ['id' => $this->customer->id],
            'currency' => 'USD',
            'redirect' => ['url' => 'https://payment.test/tap/handle']
        ]);

        $this->assertInstanceOf(TapObject::class, $resource);
    }
}
