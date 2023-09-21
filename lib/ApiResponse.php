<?php

namespace Tap;

use Tap\Util\CaseInsensitiveArray;

/**
 * Class ApiResponse
 *
 * @package Tap
 */
class ApiResponse
{
    public $headers;
    public $body;
    public $json;
    public $code;

    /**
     * @param string $body
     * @param integer $code
     * @param array|CaseInsensitiveArray $headers
     * @param ?array $json
     */
    public function __construct($body, $code, $headers, $json)
    {
        $this->body = $body;
        $this->code = $code;
        $this->headers = $headers;
        $this->json = $json;
    }
}
