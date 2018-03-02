<?php

namespace Unit\Billing;

use App\Billing\FakePaymentGateway;
use Tests\TestCase;

class FakePaymentGatewayTest extends TestCase
{
    function testChargesWithAValidPaymentTokenAreSuccessfull()
    {
        $paymentGateWay = new FakePaymentGateway();

        $paymentGateWay->charge(2500, $paymentGateWay->getValidTestToken());

        $this->assertEquals(2500, $paymentGateWay->totalCharges());
    }
}