<?php

namespace App\Exceptions;

use Exception;

class BookingCreationException extends Exception
{
public function __construct($message = 'Booking creation error.', $code = 0, Exception $previous = null)
{
parent::__construct($message, $code, $previous);
}
}
