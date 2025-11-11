<?php


namespace App\Infrastructure\Persistence\Repositories;


use App\Domain\Booking\Entities\Ticket;
use App\Domain\Booking\Repositories\TicketRepositoryInterface;
use App\Models\Ticket as TicketModel;


class EloquentTicketRepository implements TicketRepositoryInterface
{
    public function save(Ticket $ticket): Ticket
    {
        $model = TicketModel::create([
            'bus_id' => $ticket->busId,
            'user_id' => $ticket->userId,
            'seat_number' => $ticket->seatNumber()->value(),
            'passenger_name' => $ticket->passengerName()->value(),
            'price' => $ticket->price()->value(),
        ]);


        $ticket->id = $model->id;
        return $ticket;
    }


    public function isSeatBooked(string $busId, string $seatNumber): bool
    {
        return TicketModel::where('bus_id', $busId)->where('seat_number', $seatNumber)->exists();
    }


    public function findById(string $id): ?Ticket
    {
        $m = TicketModel::find($id);
        if (!$m) return null;


        return new Ticket(
            $m->bus_id,
            $m->user_id,
            new \App\Domain\Booking\ValueObjects\SeatNumber($m->seat_number),
            new \App\Domain\Booking\ValueObjects\PassengerName($m->passenger_name),
            new \App\Domain\Booking\ValueObjects\Price((float)$m->price)
        );
    }
}
