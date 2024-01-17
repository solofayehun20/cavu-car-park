<?php

namespace App\Exceptions;


namespace App\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException as SymfonyHttpException;

class HttpException
{
    public static function notFound($message = 'Not Found', $code = 404)
    {
        return new SymfonyHttpException($code, $message);
    }

    public static function badRequest($message = 'Bad Request', $code = 400)
    {
        return new SymfonyHttpException($code, $message);
    }

}
