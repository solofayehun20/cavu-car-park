<?php

namespace App\Exceptions;

use Exception;

class BookingNotFoundException extends Exception
{

    /**
     * @param string $message
     */
    public function __construct(string $message = 'Booking not found')
    {
        parent::__construct($message);
    }
}
