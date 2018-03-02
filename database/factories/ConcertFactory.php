<?php
/**
 * Created by PhpStorm.
 * User: brunotome
 * Date: 02/03/18
 * Time: 07:46
 */

use App\Models\Concert;
use Carbon\Carbon;

$factory->define(Concert::class, function (Faker\Generator $faker) {
    return [
        'title'                  => $faker->title,
        'subtitle'               => $faker->title,
        'date'                   => Carbon::parse('+2 weeks'),
        'ticket_price'           => 200,
        'venue'                  => 'The Example Theatre',
        'venue_address'          => '123 Example Lane',
        'city'                   => 'Fakeville',
        'state'                  => 'ON',
        'zipcode'                => '90210',
        'additional_information' => 'For tickets, call (555) 555-5555.'
    ];
});

$factory->state(Concert::class, 'published', function (Faker\Generator $faker) {
    return [
        'published_at' => Carbon::parse('-1 week')
    ];
});

$factory->state(Concert::class, 'unpublished', function (Faker\Generator $faker) {
    return [
        'published_at' => null
    ];
});