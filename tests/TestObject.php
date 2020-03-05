<?php


namespace Tap;


use phpseclib\Crypt\RSA;

class TestObject
{
    public static $test_resource_id = 'cus_Mi241920201317h9PX1902566';
    public static $test_token_id = 'tok_C4rIs1311012PX9L527526';
    public static $test_card_id = 'card_CE9eU1311012Bnk9527526';
    public static $auth_id = 'auth_b4PZ1320192154w4P91410635';

    public static $testCard = [
        'card' => [
            'number' => '5123450000000008',
            'exp_month' => '12',
            'exp_year' => '21',
            'cvc' => 124,
            'name' => 'Amr Emad',
            'address' => [
                'country' => 'Kuwait',
                'line1' => 'Salmiya, 21',
                'city' => 'Kuwait city',
                'street' => 'Salim',
                'avenue' => 'Gulf',
            ]
        ],
        'client_ip' => '192.168.10.20'
    ];

    public static $testCustomer = [
        'first_name' => 'Test',
        'last_name' => 'Test',
        'email' => 'test@customer.com',
        'phone' => [
            'country_code' => '00065',
            'number' => '11111111'
        ]
    ];

    public static $testDeleteCustomer = [
        'first_name' => 'delete',
        'middle_name' => 'customer',
        'last_name' => 'test',
        'email' => 'test@delete.com',
        'phone' => [
            'country_code' => '002',
            'number' => '01011441444'
        ]
    ];

    // @var string for testing source token
    public static $sourceId = null;

    /**
     * @return null
     */
    public static function getSourceId()
    {
        if (self::$sourceId == null) {
            self::$sourceId = Token::create(self::$testCard);
        }

        return self::$sourceId;
    }

    /**
     * @param null $sourceId
     */
    public static function setSourceId($sourceId): void
    {
        self::$sourceId = $sourceId;
    }

    public static function getEncryptedCard($token)
    {
        //TODO: not yet finished
        $rsa = new RSA();
        $rsa->loadKey(file_get_contents(Tap::getCABundlePath()));
        return $rsa->encrypt($token);
    }
}
