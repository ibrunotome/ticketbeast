<?php

namespace Tests\Feature;

use App\Billing\FakePaymentGateway;
use App\Billing\PaymentGateway;
use App\Models\Concert;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class PurchaseTicketsTest extends TestCase
{
    use DatabaseMigrations;

    function testCustomerCanPurchaseConcertTickets()
    {
        $paymentGateway = new FakePaymentGateway();
        $this->app->instance(PaymentGateway::class, $paymentGateway);

        $concert = factory(Concert::class)->create([
            'ticket_price' => 3250
        ]);

        $this->json('POST', '/concerts/' . $concert->id . '/orders', [
            'email'           => 'john@example.com',
            'ticket_quantity' => 3,
            'payment_token'   => $paymentGateway->getValidTestToken()
        ]);

        $this->assertResponseStatus(201);

        $this->assertEquals(9750, $paymentGateway->totalCharges());

        $order = $concert->orders()->where('email', 'john@example.com')->first();

        $this->assertNotNull($order);

        $this->assertEquals(3, $order->tickets->count());
    }
}