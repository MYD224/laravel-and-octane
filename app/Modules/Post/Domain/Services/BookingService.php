<?php
namespace App\Domain\Booking\Services;


use App\Domain\Booking\Entities\Ticket;
use App\Domain\Booking\ValueObjects\SeatNumber;
use App\Domain\Booking\ValueObjects\PassengerName;
use App\Domain\Booking\ValueObjects\Price;
use App\Domain\Booking\Repositories\TicketRepositoryInterface;
use App\Domain\Booking\Exceptions\SeatAlreadyBookedException;


class BookingService
{
    public function __construct(private TicketRepositoryInterface $tickets) {}


    public function reserveTicket(string $busId, string $Id, string $seat, string $passengerName, float $price, ?string $idempotencyKey = null): Ticket
    {
        $seatVO = new SeatNumber($seat);
        $nameVO = new PassengerName($passengerName);
        $priceVO = new Price($price);


        if ($this->tickets->isSeatBooked($busId, $seatVO->value())) {
            throw new SeatAlreadyBookedException("Seat {$seat} already booked");
        }


        $ticket = new Ticket($busId, $Id, $seatVO, $nameVO, $priceVO);


        $this->tickets->save($ticket);


        return $ticket;
    }
}
