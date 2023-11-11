<?php

namespace App\Services;

use App\Models\PaymentModel;

class StripeService extends BaseService
{
    public function __construct(PaymentModel $paymentModel)
    {
        $this->model = $paymentModel;
    }

    public function createPayment($orderId, $total)
    {
        // This is your test secret API key.
        \Stripe\Stripe::setApiKey('sk_test_51OBCCpE6LGCspsFahjsGyB5UvI9rUpGp4XDVCkDqs4NCWjE9MUikvnkbll1bQFrgwmutZbYlIpudISAmf5sYO5KL00eY9t6tK1');

        header('Content-Type: application/json');

        $YOUR_DOMAIN = 'http://127.0.0.1:8000/';

        $checkout_session = \Stripe\Checkout\Session::create([
            'line_items' => [[
                # Provide the exact Price ID (e.g. pr_1234) of the product you want to sell
                'price' => 'price_1OBCgkE6LGCspsFavSjHSFZT',
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => 'http://127.0.0.1:8000' . '/store/stripe/success?order_id=' . $orderId,
            'cancel_url' => 'http://127.0.0.1:8000' . '/cancel',
        ]);
        return $checkout_session->url;
    }
}
