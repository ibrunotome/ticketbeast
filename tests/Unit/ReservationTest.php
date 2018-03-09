<?php

namespace Tests\Unit;

use App\Models\Concert;
use App\Models\Reservation;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class ReservationTest extends TestCase
{
    use DatabaseMigrations;

    public function testCalculatingTheTotalCost()
    {
        $concert = factory(Concert::class)->create([
            'ticket_price' => 1200
        ])->addTickets(3);

        $tickets = $concert->findTickets(3);

        $reservation = new Reservation($tickets);

        $this->assertEquals(3600, $reservation->totalCost());
    }
}