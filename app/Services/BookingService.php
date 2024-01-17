<?php


namespace App\Services;

use App\Exceptions\BookingNotFoundException;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\Exceptions\BookingCreationException;
use Carbon\Carbon;

class BookingService
{
    public function checkAvailability($fromDateTime, $toDateTime): int
    {
        // Logic to check availability based on existing bookings
        $existingBookings = Booking::whereBetween('from', [$fromDateTime, $toDateTime])
            ->orWhereBetween('to', [$fromDateTime, $toDateTime])
            ->count();

        // Get the available spaces
        return config('ParkingSpace.max_spaces') - $existingBookings;
    }


    public function calculatePrice($fromDateTime, $toDateTime): float
    {
        // Parse the input date strings to Carbon instances
        $from = Carbon::parse($fromDateTime);
        $to = Carbon::parse($toDateTime);

        // Check if the booking spans multiple days
        $isMultiDayBooking = $from->diffInDays($to) > 0;

        $isWeekend = $from->isWeekend();

        // Check if the booking is in summer or winter
        $isSummer = $from->between(Carbon::parse('2023-06-21'), Carbon::parse('2023-09-22'));
        $isWinter = !$isSummer;

        $weekdayPrice = config('ParkingPrice.weekday_price');
        $weekendPrice = config('ParkingPrice.weekend_price');
        $summerPrice = config('ParkingPrice.summer_price');
        $winterPrice = config('ParkingPrice.winter_price');


        $totalHours = $isMultiDayBooking ? $from->diffInHours($to) : $from->floatDiffInHours($to);


        if ($isWeekend) {
            $pricePerHour = $weekendPrice;
        } else {
            $pricePerHour = $weekdayPrice;
        }


        if ($isSummer) {
            $pricePerHour = $summerPrice;
        } elseif ($isWinter) {
            $pricePerHour = $winterPrice;
        }

        return $totalHours * $pricePerHour;
    }

    /**
     * @throws BookingCreationException
     */
    public function createBooking($fromDateTime, $toDateTime)
    {
        try {
            $this->validateBookingParameters($fromDateTime, $toDateTime);

            $availableSpaces = $this->checkAvailability($fromDateTime, $toDateTime);

            $this->validateAvailableSpaces($availableSpaces);

            $randomUserId = User::inRandomOrder()->first()->id;

            \DB::beginTransaction();

            $booking = Booking::create([
                'user_id' => $randomUserId,
                'from' => $fromDateTime,
                'to' => $toDateTime,
            ]);

            // Decrement the available spaces
            Config::set('ParkingSpace.max_spaces', $availableSpaces - 1);

            \DB::commit();

            return $booking;
        } catch (\Exception $e) {
            \DB::rollback();
            throw new BookingCreationException('Error creating the booking.', $e->getCode(), $e);
        }
    }

    protected function validateBookingParameters($fromDateTime, $toDateTime): void
    {
        if (empty($fromDateTime) || empty($toDateTime)) {
            throw new HttpException(400, 'Please provide both date and time "from" and "to" parameters.');
        }
    }

    protected function validateAvailableSpaces($availableSpaces): void
    {
        if ($availableSpaces <= 0) {
            throw new HttpException(400, 'No available spaces for the selected date range.');
        }
    }


    /**
     * @throws BookingNotFoundException
     */
    public function cancelBooking(int $bookingId)
    {
        $booking = Booking::find($bookingId);

        if (!$booking) {
            throw new BookingNotFoundException();
        }

        $booking->delete();
    }


    /**
     * @throws BookingNotFoundException
     */
    public function amendBooking($bookingId, $newFromDateTime, $newToDateTime)
    {
        // Logic to amend a booking
        $booking = Booking::find($bookingId);

        if ($booking) {
            $booking->update([
                'from' => $newFromDateTime,
                'to' => $newToDateTime,
            ]);
        } else {
            throw new BookingNotFoundException();
        }
    }
}
