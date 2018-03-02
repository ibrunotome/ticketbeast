<?php

namespace Tests\Feature;

use Carbon\Carbon;
use Tests\TestCase;

class ViewConcertListingTest extends TestCase
{
    public function userCanViewAConcertListing()
    {
        // Arrange
        // Create a concert
        $concert = Concert::create([
            'title'                  => 'The Red Chord',
            'subtitle'               => 'with Animosity an Lethargy',
            'date'                   => Carbon::parse('December 13, 2016 8:00pm'),
            'ticket_price'           => 3250,
            'venue'                  => 'The Mosh Pit',
            'city'                   => 'Laraville',
            'state'                  => 'ON',
            'zip'                    => '17916',
            'additional_information' => 'For tickets, call (555) 555-5555.'
        ]);


        // Act
        // View the concert listing
        $this->visit('/concerts/' . $concert->id);

        // Assert
        // See the concert details
        $this->see('The Red Chord');
        $this->see('with Animosity and Lethargy');
        $this->see('December 13, 2016');
        $this->see('8:00pm');
        $this->see('32.50');
        $this->see('The Mosh Pit');
        $this->see('123 Example Lane');
        $this->see('Laraville, ON 17916');
        $this->see('For tickets, call (555) 555-5555.');
    }
}
