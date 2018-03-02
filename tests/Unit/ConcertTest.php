<?php

namespace Tests\Unit;

use App\Models\Concert;
use Carbon\Carbon;
use Tests\TestCase;

class ConcertTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testCanGetFormattedDate()
    {
        $concert = factory(Concert::class)->make([
            'date' => Carbon::parse('2016-12-01 8:00pm')
        ]);

        $this->assertEquals('December 1, 2016', $concert->formatted_date);
    }

    public function cancGetFormattedStartTime()
    {
        $concert = factory(Concert::class)->make([
            'date' => Carbon::parse('2016-12-01 17:00:00')
        ]);

        $this->assertEquals('5:00', $concert->formatted_start_time);
    }

    public function canGetTicketPriceInDollars()
    {
        $concert = factory(Concert::class)->make([
            'ticket_price' => 6750
        ]);

        $this->assertEquals('67.50', $concert->ticket_price_in_dollars);
    }
}
