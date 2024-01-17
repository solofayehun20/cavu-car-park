<?php

namespace App\Http\Controllers;

use App\Exceptions\BookingCreationException;
use App\Exceptions\HttpException;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\Booking;
use App\Services\BookingService;
use Illuminate\Validation\ValidationException as ValidationExceptionAlias;



class BookingController extends Controller
{
    protected BookingService $bookingService;

    public function __construct(BookingService $bookingService)
    {
        $this->bookingService = $bookingService;
    }

    public function checkAvailability(Request $request)
    {

        try {
        $this->validate($request, [
            'from' => 'required|date_format:Y-m-d H:i:s',
            'to' => 'required|date_format:Y-m-d H:i:s|after:from',
        ]);

        $from = $request->input('from');
        $to = $request->input('to');

        $availability = $this->bookingService->checkAvailability($from, $to);

        return response()->json(['available' => $availability],200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }

    }

    public function checkPrice(Request $request)
    {
        try {
            $this->validate($request, [
                'from' => 'required|date_format:Y-m-d H:i:s',
                'to' => 'required|date_format:Y-m-d H:i:s|after:from',
            ]);

            $from = $request->input('from');
            $to = $request->input('to');

            $price = $this->bookingService->calculatePrice($from, $to);

            return response()->json(['price' => $price], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function createBooking(Request $request)
    {
        try {
            $this->validate($request, [
                'from' => 'required|date_format:Y-m-d H:i:s',
                'to' => 'required|date_format:Y-m-d H:i:s|after:from',
            ]);

            $from = $request->input('from');
            $to = $request->input('to');

            $booking = $this->bookingService->createBooking($from, $to);

            return response()->json(['Booking Successful' => $booking], 200);
        } catch (ValidationException $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred while creating the booking.'], 400);
        }
    }


    public function cancelBooking(Request $request)
    {

        $this->validate($request, [
            'bookingId' => 'required',
        ]);


        $bookingId = $request->input('bookingId');

        try {
            $this->bookingService->cancelBooking($bookingId);
            return response()->json(['message' => 'Booking canceled successfully']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 404);
        }
    }

    public function amendBooking(Request $request)
    {
        $this->validate($request, [
            'bookingId' => 'required',
            'newFrom' => 'required|date_format:Y-m-d H:i:s',
            'newTo' => 'required|date_format:Y-m-d H:i:s|after:newFrom',
        ]);

        $bookingId = $request->input('bookingId');
        $newFrom = $request->input('newFrom');
        $newTo = $request->input('newTo');

        try {
            $this->bookingService->amendBooking($bookingId, $newFrom, $newTo);
            return response()->json(['message' => 'Booking amended successfully']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 404);

        }
    }


}
