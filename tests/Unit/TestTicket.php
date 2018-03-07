<?php

namespace Tests\Unit;

use App\Models\Concert;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class TestTicket extends TestCase
{
    use DatabaseMigrations;

    function testATicketCanBeReleased()
    {
        $concert = factory(Concert::class)->create();

        $concert->addTickets(1);
        $order  = $concert->orderTickets('jane@example.com', 1);
        $ticket = $order->tickets()->first();
        $this->assertEquals($order->id, $ticket->order_id);

        $ticket->release();

        $this->assertNull($ticket->fresh()->order_id);
    }
}