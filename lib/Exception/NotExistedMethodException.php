<?php


namespace Tap\Exception;


class NotExistedMethodException extends \Exception
{
    protected $code = 9988;
    protected $message = 'this method not existed on tap.company yet';
}
