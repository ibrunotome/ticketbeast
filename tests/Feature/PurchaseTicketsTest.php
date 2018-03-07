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

    private $paymentGateway;

    protected function setUp()
    {
        parent::setUp();

        $this->paymentGateway = new FakePaymentGateway();
        $this->app->instance(PaymentGateway::class, $this->paymentGateway);
    }

    private function orderTickets($concert, $params)
    {
        $this->json('POST', '/concerts/' . $concert->id . '/orders', $params);
    }

    private function assertValidationError($field)
    {
        $this->assertResponseStatus(422);
        $this->assertArrayHasKey($field, $this->decodeResponseJson()['errors']);
    }

    public function testCustomerCanPurchaseTicketsToAPublishedConcert()
    {
        $concert = factory(Concert::class)->states('published')->create(['ticket_price' => 3250]);
        $concert->addTickets(3);

        $this->orderTickets($concert, [
            'email'           => 'john@example.com',
            'ticket_quantity' => 3,
            'payment_token'   => $this->paymentGateway->getValidTestToken(),
        ]);

        $this->assertResponseStatus(201);
        $this->assertEquals(9750, $this->paymentGateway->totalCharges());
        $order = $concert->orders()->where('email', 'john@example.com')->first();
        $this->assertNotNull($order);
        $this->assertEquals(3, $order->tickets()->count());
    }

    public function testEmailIsRequiredToPurchaseTickets()
    {
        $concert = factory(Concert::class)->states('published')->create();
        $this->orderTickets($concert, [
            'ticket_quantity' => 3,
            'payment_token'   => $this->paymentGateway->getValidTestToken(),
        ]);

        $this->assertValidationError('email');
    }

    public function testEmailMustBeValidToPurchaseTickets()
    {
        $concert = factory(Concert::class)->states('published')->create();
        $this->orderTickets($concert, [
            'email'           => 'not-an-email-address',
            'ticket_quantity' => 3,
            'payment_token'   => $this->paymentGateway->getValidTestToken(),
        ]);

        $this->assertValidationError('email');
    }

    public function testTicketQuantityIsRequiredToPurchaseTickets()
    {
        $concert = factory(Concert::class)->states('published')->create();
        $this->orderTickets($concert, [
            'email'         => 'john@example.com',
            'payment_token' => $this->paymentGateway->getValidTestToken(),
        ]);

        $this->assertValidationError('ticket_quantity');
    }

    public function testTicketQuantityMustBeAtLeast1ToPurchaseTickets()
    {
        $concert = factory(Concert::class)->states('published')->create();
        $this->orderTickets($concert, [
            'email'           => 'john@example.com',
            'ticket_quantity' => 0,
            'payment_token'   => $this->paymentGateway->getValidTestToken(),
        ]);

        $this->assertValidationError('ticket_quantity');
    }

    public function testPaymentTokenIsRequired()
    {
        $concert = factory(Concert::class)->states('published')->create();
        $this->orderTickets($concert, [
            'email'           => 'john@example.com',
            'ticket_quantity' => 3,
        ]);

        $this->assertValidationError('payment_token');
    }

    public function testAnOrderIsNotCreatedIfPaymentFails()
    {
        $concert = factory(Concert::class)->states('published')->create(['ticket_price' => 3250]);
        $concert->addTickets(3);

        $this->orderTickets($concert, [
            'email'           => 'john@example.com',
            'ticket_quantity' => 3,
            'payment_token'   => 'invalid-payment-token',
        ]);

        $this->assertResponseStatus(422);
        $order = $concert->orders()->where('email', 'john@example.com')->first();
        $this->assertNull($order);
    }

    public function testCannotPurchaseTicketsToAnUnpublishedConcert()
    {
        $concert = factory(Concert::class)->states('unpublished')->create();
        $concert->addTickets(3);

        $this->orderTickets($concert, [
            'email'           => 'john@example.com',
            'ticket_quantity' => 3,
            'payment_token'   => $this->paymentGateway->getValidTestToken()
        ]);

        $this->assertResponseStatus(404);
        $this->assertEquals(0, $concert->orders()->count());
        $this->assertEquals(0, $this->paymentGateway->totalCharges());
    }

    public function testCannotPurchaseMoreTicketsThanRemain()
    {
        $this->disableExceptionHandling();
        $concert = factory(Concert::class)->states('published')->create();
        $concert->addTickets(50);

        $this->orderTickets($concert, [
            'email'           => 'john@example.com',
            'ticket_quantity' => 51,
            'payment_token'   => $this->paymentGateway->getValidTestToken(),
        ]);

        $this->assertResponseStatus(422);

        $order = $concert->orders()->where('email', 'john@example.com')->first();
        $this->assertNull($order);
        $this->assertEquals(0, $this->paymentGateway->totalCharges());
        $this->assertEquals(50, $concert->ticketsRemaining());
    }
}