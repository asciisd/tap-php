<?php


namespace Tap;


class RecurringTest extends TestCase
{
    const TEST_RESOURCE_ID = 'rec_tIZX1311012iarx527713';

    public function testIsListable()
    {
        $this->expectsRequest(
            'post',
            '/v2/recurring/list'
        );
        $resources = Recurring::all(['limit' => 25]);
        $this->assertTrue(is_array($resources->recurring));
        $this->assertInstanceOf(Recurring::class, $resources->recurring[0]);
    }

    public function testIsRetrievable()
    {
        $this->expectsRequest(
            'get',
            '/v2/recurring/' . self::TEST_RESOURCE_ID
        );
        $resource = Recurring::retrieve(self::TEST_RESOURCE_ID);
        $this->assertInstanceOf(Recurring::class, $resource);
    }

    public function testIsCreatable()
    {
        $this->expectsRequest(
            'post',
            '/v2/recurring'
        );
        $resource = Recurring::create([
            "auto_renew" => false,
            "term" => [
                "from" => 1606394340000,
                "to" => 1606394350000,
                "interval" => [
                    "period" => "DAY",
                    "value" => 3
                ],
                "weekday" => "SUNDAY"
            ],
            "due" => [
                "period" => "DAY",
                "value" => 2
            ],
            "expiry" => [
                "period" => "DAY",
                "value" => 3
            ],
            "grace" => [
                "period" => "DAY",
                "value" => 2
            ],
            "penalty" => [
                "amount" => 0,
                "type" => "F"
            ],
            "invoice" => [
                "charge" => [
                    "receipt" => [
                        "email" => true,
                        "sms" => true
                    ],
                    "statement_descriptor" => "string"
                ],
                "currencies" => [
                    ""
                ],
                "customer" => [
                    "email" => "testt@test.com",
                    "first_name" => "test",
                    "last_name" => "test",
                    "middle_name" => "",
                    "phone" => [
                        "country_code" => "965",
                        "number" => "000000"
                    ]
                ],
                "description" => "test",
                "invoicer" => [
                    "bcc" => "",
                    "cc" => "",
                    "to" => ""
                ],
                "metadata" => [
                    "additionalProp1" => "string",
                    "additionalProp2" => "string",
                    "additionalProp3" => "string"
                ],
                "mode" => "INVOICE",
                "note" => "test",
                "notifications" => [
                    "channels" => [
                        "EMAIL",
                        "SMS"
                    ],
                    "dispatch" => true
                ],
                "order" => [
                    "amount" => 10,
                    "currency" => "KWD",
                    "items" => [
                        [
                            "amount" => 10,
                            "currency" => "KWD",
                            "description" => "tesr",
                            "discount" => [
                                "type" => "F",
                                "value" => 1
                            ],
                            "image" => "",
                            "name" => "test",
                            "quantity" => 10
                        ]
                    ]
                ],
                "payment_methods" => [
                    ""
                ],
                "post" => [
                    "url" => ""
                ],
                "redirect" => [
                    "url" => ""
                ],
                "reference" => [
                    "documents" => [
                        [
                            "images" => [
                                ""
                            ],
                            "number" => "",
                            "type" => ""
                        ]
                    ],
                    "invoice" => "INV_0000",
                    "order" => "INV_0000"
                ]
            ]
        ]);
        $this->assertInstanceOf(Recurring::class, $resource);
    }

    public function testIsSaveable()
    {
        $resource = Recurring::retrieve(self::TEST_RESOURCE_ID);
        $resource->invoice["description"] = "test from tap-php package";
        $this->expectsRequest(
            'put',
            '/v2/recurring/' . $resource->id
        );
        $resource->save();
        $this->assertInstanceOf(Recurring::class, $resource);
        $this->assertEquals("test from tap-php package", $resource->invoice["description"]);
    }

    public function testIsUpdatable()
    {
        $this->expectsRequest(
            'put',
            '/v2/recurring/' . self::TEST_RESOURCE_ID
        );
        $resource = Recurring::update(self::TEST_RESOURCE_ID, [
            "auto_renew" => false,
            "term" => [
                "from" => 1606394340000,
                "to" => 1606394350000,
                "interval" => [
                    "period" => "DAY",
                    "value" => 3
                ],
                "weekday" => "SUNDAY"
            ],
            "due" => [
                "period" => "DAY",
                "value" => 2
            ],
            "expiry" => [
                "period" => "DAY",
                "value" => 3
            ],
            "grace" => [
                "period" => "DAY",
                "value" => 2
            ],
            "penalty" => [
                "amount" => 0,
                "type" => "F"
            ],
            "invoice" => [
                "charge" => [
                    "receipt" => [
                        "email" => true,
                        "sms" => true
                    ],
                    "statement_descriptor" => "string"
                ],
                "currencies" => [
                    ""
                ],
                "customer" => [
                    "email" => "testt@test.com",
                    "first_name" => "test",
                    "last_name" => "test",
                    "middle_name" => "",
                    "phone" => [
                        "country_code" => "965",
                        "number" => "000000"
                    ]
                ],
                "description" => "test update from tap-php package",
                "invoicer" => [
                    "bcc" => "",
                    "cc" => "",
                    "to" => ""
                ],
                "metadata" => [
                    "additionalProp1" => "string",
                    "additionalProp2" => "string",
                    "additionalProp3" => "string"
                ],
                "mode" => "INVOICE",
                "note" => "test",
                "notifications" => [
                    "channels" => [
                        "EMAIL",
                        "SMS"
                    ],
                    "dispatch" => true
                ],
                "order" => [
                    "amount" => 10,
                    "currency" => "KWD",
                    "items" => [
                        [
                            "amount" => 10,
                            "currency" => "KWD",
                            "description" => "tesr",
                            "discount" => [
                                "type" => "F",
                                "value" => 1
                            ],
                            "image" => "",
                            "name" => "test",
                            "quantity" => 10
                        ]
                    ]
                ],
                "payment_methods" => [
                    ""
                ],
                "post" => [
                    "url" => ""
                ],
                "redirect" => [
                    "url" => ""
                ],
                "reference" => [
                    "documents" => [
                        [
                            "images" => [
                                ""
                            ],
                            "number" => "",
                            "type" => ""
                        ]
                    ],
                    "invoice" => "INV_0000",
                    "order" => "INV_0000"
                ]
            ]
        ]);
        $this->assertInstanceOf(Recurring::class, $resource);
        $this->assertEquals("test update from tap-php package", $resource->invoice["description"]);
    }

    public function testIsDeletable()
    {
        $resource = Recurring::retrieve(self::TEST_RESOURCE_ID);
        $this->expectsRequest(
            'delete',
            '/v2/recurring/' . $resource->id
        );
        $resource->delete();
        $this->assertInstanceOf(Recurring::class, $resource);
        $this->assertEquals("CANCELED", $resource->status);

    }
}
