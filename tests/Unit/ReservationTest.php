<?php

namespace Tests\Unit;

use App\Models\Reservation;
use Tests\TestCase;

class ReservationTest extends TestCase
{
    public function testCalculatingTheTotalCost()
    {
        $tickets = collect([
            (object)['price' => 1200],
            (object)['price' => 1200],
            (object)['price' => 1200],
        ]);

        $reservation = new Reservation($tickets);

        $this->assertEquals(3600, $reservation->totalCost());
    }
}