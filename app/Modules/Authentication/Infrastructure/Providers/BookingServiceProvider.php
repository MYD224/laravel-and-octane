<?php

namespace App\Infrastructure\Providers;

use Illuminate\Support\ServiceProvider;
use App\Domain\Booking\Repositories\BookingRepositoryInterface;
use App\Domain\Booking\Repositories\TicketRepositoryInterface;
use App\Infrastructure\Persistence\Repositories\EloquentBookingRepository;
use App\Infrastructure\Persistence\Repositories\EloquentTicketRepository;

class BookingServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            BookingRepositoryInterface::class,
            EloquentBookingRepository::class
        );

        $this->app->bind(
            TicketRepositoryInterface::class,
            EloquentTicketRepository::class
        );
    }
}
