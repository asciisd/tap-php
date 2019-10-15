<?php


namespace Tap;


class TokenTest extends TestCase
{
    const TEST_RESOURCE_ID = 'tok_C4rIs1311012PX9L527526';

    public function testIsRetrievable()
    {
        $this->expectsRequest(
            'get',
            '/v2/tokens/' . self::TEST_RESOURCE_ID
        );
        $resource = Token::retrieve(self::TEST_RESOURCE_ID);
        $this->assertInstanceOf(Token::class, $resource);
    }

    public function testIsCreatable()
    {
        $this->expectsRequest(
            'post',
            '/v2/tokens'
        );

        $resource = Token::create(TestObject::$testCard);
        $this->assertInstanceOf(Token::class, $resource);
    }

    public function testCreateFromSavedCard()
    {
        $customer = Customer::create(TestObject::$testCustomer);
        $token = Token::create(TestObject::$testCard);
        $card = Card::createFromSource($customer->id, $token->id);

        $this->expectsRequest(
            'post',
            '/v2/tokens'
        );

        $resource = Token::create([
            'saved_card' => [
                'card_id' => $card->id,
                'customer_id' => $customer->id
            ],
            'client_ip' => '127.0.0.1'
        ]);
        $this->assertInstanceOf(Token::class, $resource);
    }
}
