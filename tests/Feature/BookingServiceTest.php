<?php

namespace Tests\Feature;
use App\Exceptions\BookingCreationException;

use Tests\TestCase;

/**
 * @property $bookingService
 */
class BookingServiceTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */


    public function testCheckAvailability()
    {
        // Scenario 1: Check availability with valid date range
        $response1 = $this->json('get', '/check-availability', [
            'from' => '2023-01-01 12:00:00',
            'to' => '2023-01-01 14:00:00',
        ]);
        $response1->assertStatus(200);

        // Scenario 2: Check availability with an invalid date range
        $response2 = $this->json('get', '/check-availability', [
            'from' => 'invalid_date',
            'to' => 'invalid_date',
        ]);
        $response2->assertStatus(400);
    }


    public function testCheckPrice()
    {
       // Scenario 1: Create a booking  price with a valid date range
        $response1 = $this->json('get', '/check-price', [
            'from' => '2023-01-01 12:00:00',
            'to' => '2023-01-01 14:00:00',
        ]);
        $response1->assertStatus(200)->assertJsonStructure(['price']);

        // Scenario 2: Attempt to check booking price for  with an invalid date range
        $response2 = $this->json('POST', '/create-booking', [
            'from' => 'invalid_date',
            'to' => 'invalid_date',
        ]);
        $response2->assertStatus(400);

    }


    public function testCreateBooking()
    {
        // Scenario 1: Create a booking with a valid date range
        $response1 = $this->json('POST', '/create-booking', [
            'from' => '2024-01-01 12:00:00',
            'to' => '2024-01-01 14:00:00',
        ]);
        $response1->assertStatus(200);

        // Scenario 2: Attempt to create a booking with an invalid date range
        $response2 = $this->json('POST', '/create-booking', [
            'from' => 'invalid_date',
            'to' => 'invalid_date',
        ]);
        $response2->assertStatus(400);
    }


//    public function testCancelBooking()
//    {
//
//        // Scenario 1: Cancel an existing booking
//        $response1 = $this->json('POST', '/cancel-booking/{bookingId}', ['bookingId' => 11]); // Assuming the booking ID is 1
//        $response1->assertJson(['message' => 'Booking canceled successfully']);
//
//        // Scenario 2: Attempt to cancel a non-existing booking
//        $response2 = $this->json('POST', '/cancel-booking/{bookingId}', ['bookingId' => 999]);
//        $response2->assertStatus(404); // Assuming a 404 status for BookingNotFoundException
//    }


    public function testAmendBooking()
    {
        // Scenario 1: Amend an existing booking with a valid date range

        $response1 = $this->json('POST', '/amend-booking/{bookingId}', [
            'bookingId' => 15,
            'newFrom' => '2023-01-01 08:00:00',
            'newTo' => '2023-01-01 14:00:00',
        ]);
        $response1->assertStatus(200);

        // Scenario 2: Attempt to amend a non-existing booking
        $response2 = $this->json('POST', '/amend-booking/{bookingId}', [
            'bookingId' => 999,
            'newFrom' => '2023-01-01 12:00:00',
            'newTo' => '2023-01-01 14:00:00',
        ]);
        $response2->assertStatus(404);
    }







}
