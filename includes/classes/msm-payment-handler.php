<?php

use Stripe\Stripe;
use Stripe\Checkout\Session;

class MSM_Payment_Handler {
    public static function init() {
        require_once plugin_dir_path(__FILE__) . '../vendor/autoload.php';
        Stripe::setApiKey('your_stripe_secret_key');
    }

    public static function create_checkout_session($amount, $currency, $description) {
        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => $currency,
                    'product_data' => [
                        'name' => $description,
                    ],
                    'unit_amount' => $amount * 100,
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => home_url('/subscription-success'),
            'cancel_url' => home_url('/subscription-cancel'),
        ]);

        return $session->url;
    }

    
}
