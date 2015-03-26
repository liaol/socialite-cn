<?php namespace Liaol\SocialiteCn\Providers;

use Exception;

class ErrorCodeException extends Exception
{
    public function __construct($code,$msg)
    {
        parent::__construct('errcode: ' . $code .  "\n" .  'errmsg: ' . $msg);
    }

}
