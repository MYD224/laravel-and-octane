<?php

namespace App\Infrastructure\Persistence\Repositories;

use App\Domain\Booking\Entities\Booking;
use App\Domain\Booking\Repositories\BookingRepositoryInterface;
use App\Models\Booking as BookingModel;

class EloquentBookingRepository implements BookingRepositoryInterface
{
    public function save(Booking $booking): Booking
    {
        // Persist or update booking row. Keep mapping small and focused on persistence concerns.
        BookingModel::updateOrCreate(
            ['id' => $booking->id],
            [
                'ticket_id' => $booking->ticket->id,
                'status' => $booking->status,
                // add other columns mapping here if needed (seat_num, price, etc.)
            ]
        );

        return $booking;
    }

    public function findById(string $id): ?Booking
    {
        $m = BookingModel::find($id);
        if (!$m) {
            return null;
        }

        // Simplified mapping: hydrate a minimal Ticket entity using available data from the model.
        // Adjust this to match your Ticket constructor/signature or hydrate additional value objects (SeatNum, Price) as needed.
        $ticket = new \App\Domain\Booking\Entities\Ticket(
            "id1", 
            "user1", 
            new \App\Domain\Booking\ValueObjects\SeatNumber("A1"),
            new \App\Domain\Booking\ValueObjects\PassengerName("John Doe"),
            new \App\Domain\Booking\ValueObjects\Price(100.00)
        );

        // If your Booking entity expects different args, update the constructor call below accordingly.
        return new Booking($m->id, $ticket, $m->status);
    }
}