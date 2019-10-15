<?php


namespace Tap;

use Tap\Exception\NotExistedMethodException;

class CustomerTest extends TestCase
{
    public function testIsListable()
    {
        $this->expectsRequest(
            'post',
            '/v2/customers/list'
        );

        $resources = Customer::all();

        $this->assertTrue(is_array($resources->customers));
        $this->assertInstanceOf(Customer::class, $resources->customers[0]);
    }

    public function testIsRetrievable()
    {
        $this->expectsRequest(
            'get',
            '/v2/customers/' . TestObject::$test_resource_id
        );
        $resource = Customer::retrieve(TestObject::$test_resource_id);
        $this->assertInstanceOf(Customer::class, $resource);
    }

    public function testIsCreatable()
    {
        $this->expectsRequest(
            'post',
            '/v2/customers'
        );

        $resource = Customer::create(TestObject::$testCustomer);
        $this->assertInstanceOf(Customer::class, $resource);
    }

    public function testIsSavable()
    {
        $resource = Customer::retrieve(TestObject::$test_resource_id);
        $resource->metadata["test env"] = "ta-php";
        $resource->currency = 'USD';
        $this->expectsRequest(
            'put',
            '/v2/customers/' . $resource->id
        );

        $resource->save();
        $this->assertInstanceOf(Customer::class, $resource);
    }

    public function testIsUpdatable()
    {
        $this->expectsRequest(
            'put',
            '/v2/customers/' . TestObject::$test_resource_id
        );
        $resource = Customer::update(TestObject::$test_resource_id, [
            'first_name' => 'Amr',
            'email' => 'test@test.com',
            'metadata' => [
                'check_for' => 'testIsUpdatable'
            ]
        ]);

        $this->assertInstanceOf(Customer::class, $resource);
    }

    public function testIsDeletable()
    {
        $resource = Customer::create(TestObject::$testDeleteCustomer);
        $this->expectsRequest(
            'delete',
            '/v2/customers/' . $resource->id
        );
        $resource->delete();
        $this->assertInstanceOf(Customer::class, $resource);
    }

    public function testCanDeleteDiscount()
    {
        $customer = Customer::retrieve(TestObject::$test_resource_id);
        $customer->deleteDiscount();
        $this->assertSame($customer->discount, null);
    }

    public function testCanCreateSource()
    {
        $customer = Customer::create(TestObject::$testDeleteCustomer);
        $token = Token::create(TestObject::$testCard);

        $this->expectsRequest(
            'post',
            '/v2/card/' . $customer->id
        );

        $resource = Customer::createSource($customer->id, $token->id);
        $this->assertInstanceOf(Card::class, $resource);
    }

    public function testCanRetrieveSource()
    {
        $customer = Customer::create(TestObject::$testDeleteCustomer);
        $token = Token::create(TestObject::$testCard);
        $card = Customer::createSource($customer->id, $token->id);
        $this->expectsRequest(
            'get',
            '/v2/card/' . $customer->id . '/' . $card->id
        );

        $resource = Customer::retrieveSource($customer->id, $card->id);
        $this->assertInstanceOf(Card::class, $resource);
    }

    public function testCanUpdateSource()
    {
        $this->expectException(NotExistedMethodException::class);

        Customer::updateSource(TestObject::$test_resource_id, 'no_source_id', ["name" => "New Card Holder"]);
    }

    public function testCanDeleteSource()
    {
        $token = Token::create(TestObject::$testCard);
        $card = Customer::createSource(TestObject::$test_resource_id, $token->id);

        $this->expectsRequest(
            'delete',
            '/v2/card/' . TestObject::$test_resource_id . '/' . $card->id
        );

        Customer::deleteSource(TestObject::$test_resource_id, $card->id);
    }

    public function testCanListSources()
    {
        $this->expectsRequest(
            'get',
            '/v2/card/' . TestObject::$test_resource_id
        );
        $resources = Customer::allSources(TestObject::$test_resource_id);
        $this->assertTrue(is_array($resources->data));
    }

    public function testSerializeSourceString()
    {
        $obj = Util\Util::convertToTapObject([
            'object' => 'customer',
        ], null);
        $obj->source = 'tok_visa';

        $expected = [
            'source' => 'tok_visa',
        ];
        $this->assertSame($expected, $obj->serializeParameters());
    }

    public function testSerializeSourceMap()
    {
        $obj = Util\Util::convertToTapObject([
            'object' => 'customer',
        ], null);

        $obj->phone = [
            'country_code' => '00965',
            'number' => '60012191'
        ];

        $expected = [
            'phone' => [
                'country_code' => '00965',
                'number' => '60012191'
            ],
        ];
        $this->assertSame($expected, $obj->serializeParameters());
    }
}
