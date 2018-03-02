<?php

namespace App\Http\Controllers;

use App\Billing\PaymentFailedException;
use App\Billing\PaymentGateway;
use App\Models\Concert;

class ConcertOrdersController extends Controller
{
    private $paymentGateway;

    public function __construct(PaymentGateway $paymentGateway)
    {
        $this->paymentGateway = $paymentGateway;
    }

    public function store($concertId)
    {
        $this->validate(request(), [
            'email'           => [
                'required',
                'email'
            ],
            'ticket_quantity' => [
                'required',
                'integer',
                'min:1'
            ],
            'payment_token'   => ['required'],
        ]);

        try {
            $concert = Concert::find($concertId);
            $this->paymentGateway->charge(request('ticket_quantity') * $concert->ticket_price,
                request('payment_token'));
            $order = $concert->orderTickets(request('email'), request('ticket_quantity'));

            return response()->json([], 201);
        } catch (PaymentFailedException $e) {
            return response()->json([], 422);
        }
    }
}