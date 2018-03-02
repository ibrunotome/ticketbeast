<?php

namespace Unit\Billing;

use App\Billing\FakePaymentGateway;
use App\Billing\PaymentFailedException;
use Tests\TestCase;

class FakePaymentGatewayTest extends TestCase
{
    public function testChargesWithAValidPaymentTokenAreSuccessfull()
    {
        $paymentGateWay = new FakePaymentGateway();

        $paymentGateWay->charge(2500, $paymentGateWay->getValidTestToken());

        $this->assertEquals(2500, $paymentGateWay->totalCharges());
    }

    public function testChargesWithAnInvalidPaymentTokenFail()
    {
        try {
            $paymentGateWay = new FakePaymentGateway();
            $paymentGateWay->charge(2500, 'invalid-payment-token');
        } catch (PaymentFailedException $exception) {
            return;
        }

        $this->fail();
    }
}