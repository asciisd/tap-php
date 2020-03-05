<?php


namespace Tap;


class InvoiceTest extends TestCase
{
    const TEST_RESOURCE_ID = 'inv_CfFH1311012kCQK527570';

    public function testIsListable()
    {
        $this->expectsRequest(
            'post',
            '/v2/invoices/list'
        );
        $resources = Invoice::all(['limit' => 25]);
        $this->assertTrue(is_array($resources->invoices));
        $this->assertInstanceOf(Invoice::class, $resources->invoices[0]);
    }

    public function testIsRetrievable()
    {
        $this->expectsRequest(
            'get',
            '/v2/invoices/' . self::TEST_RESOURCE_ID
        );
        $resource = Invoice::retrieve(self::TEST_RESOURCE_ID);
        $this->assertInstanceOf(Invoice::class, $resource);
    }

    public function testIsCreatable()
    {
        $this->expectsRequest(
            'post',
            '/v2/invoices'
        );
        $resource = Invoice::create([
            "draft" => false,
            "due" => 1604728943000,
            "expiry" => 1604728943000,
            "description" => "test invoice",
            "mode" => "INVOICE",
            "note" => "test note",
            "notifications" => [
                "channels" => [
                    "SMS",
                    "EMAIL"
                ],
                "dispatch" => true
            ],
            "currencies" => [
                "KWD"
            ],
            "metadata" => [
                "udf1" => "1",
                "udf2" => "2",
                "udf3" => "3"
            ],
            "charge" => [
                "receipt" => [
                    "email" => true,
                    "sms" => true
                ],
                "statement_descriptor" => "test"
            ],
            "customer" => [
                "email" => "test@test.com",
                "first_name" => "test",
                "last_name" => "test",
                "middle_name" => "test",
                "phone" => [
                    "country_code" => "965",
                    "number" => "51234567"
                ]
            ],
            "order" => [
                "amount" => 12,
                "currency" => "KWD",
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
            ],
            "payment_methods" => [
                ""
            ],
            "post" => [
                "url" => "http://your_website.com/post_url"
            ],
            "redirect" => [
                "url" => "http://your_website.com/redirect_url"
            ],
            "reference" => [
                "invoice" => "INV_00001",
                "order" => "ORD_00001"
            ]
        ]);
        $this->assertInstanceOf(Invoice::class, $resource);
    }

    public function testIsSaveable()
    {
        $resource = Invoice::retrieve(self::TEST_RESOURCE_ID);
        $resource->metadata["key"] = "value";
        $this->expectsRequest(
            'put',
            '/v2/invoices/' . $resource->id
        );
        $resource->save();
        $this->assertInstanceOf(Invoice::class, $resource);
        $this->assertEquals("value", $resource->metadata["key"]);
    }

    public function testIsUpdatable()
    {
        $this->expectsRequest(
            'put',
            '/v2/invoices/' . self::TEST_RESOURCE_ID
        );
        $resource = Invoice::update(self::TEST_RESOURCE_ID, [
            "draft" => false,
            "due" => 1604728943000,
            "expiry" => 1604728943000,
            "description" => "test invoice",
            "mode" => "INVOICE",
            "note" => "test note",
            "notifications" => [
                "channels" => [
                    "SMS",
                    "EMAIL"
                ],
                "dispatch" => true
            ],
            "currencies" => [
                "KWD"
            ],
            "metadata" => [
                "udf1" => "1",
                "udf2" => "2",
                "udf3" => "test@tap-php.com"
            ],
            "charge" => [
                "receipt" => [
                    "email" => true,
                    "sms" => true
                ],
                "statement_descriptor" => "test"
            ],
            "customer" => [
                "email" => "test@test.com",
                "first_name" => "test",
                "last_name" => "test",
                "middle_name" => "test",
                "phone" => [
                    "country_code" => "965",
                    "number" => "51234567"
                ]
            ],
            "order" => [
                "amount" => 12,
                "currency" => "KWD",
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
            ],
            "payment_methods" => [
                ""
            ],
            "post" => [
                "url" => "http://your_website.com/post_url"
            ],
            "redirect" => [
                "url" => "http://your_website.com/redirect_url"
            ],
            "reference" => [
                "invoice" => "INV_00001",
                "order" => "ORD_00001"
            ]
        ]);
        $this->assertInstanceOf(Invoice::class, $resource);
        $this->assertEquals("test@tap-php.com", $resource->metadata["udf3"]);
    }

    public function testIsDeletable()
    {
        $resource = Invoice::retrieve(self::TEST_RESOURCE_ID);
        $this->expectsRequest(
            'delete',
            '/v2/invoices/' . $resource->id
        );
        $resource->delete();
        $this->assertInstanceOf(Invoice::class, $resource);
        $this->assertEquals("CANCELLED", $resource->status);

    }
}
