<?php

namespace AlgoDocs;

use Exception;
use GuzzleHttp\Exception\ClientException;

class AlgoDocsErrorHandler
{
    public static function throw_exception($e)
    {
        throw $e;
    }
}

class AlgoDocsApiException extends \Exception
{

}