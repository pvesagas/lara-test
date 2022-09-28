<?php

namespace App\Http\Services;

use Illuminate\Support\Facades\Log;
use Stripe\Exception\ApiErrorException;
use Stripe\StripeClient;

class PaymentService
{
    /**
     * @var StripeClient
     */
    private $oStripe;

    public function __construct()
    {
        $this->oStripe = new StripeClient(env('STRIPE_PRIVATE_KEY'));
    }

    /**
     * @param float $iTotal
     * @return array
     */
    public function createCheckout(float $iTotal): array
    {
        try {
            $aResponse = $this->oStripe->checkout->sessions->create([
                'payment_method_types' => ['card'],
                'mode'                 => 'payment',
                'success_url'          => env('APP_URL', 'https://lara-test.local.com') . '/cart/success',
                'cancel_url'           => env('APP_URL', 'https://lara-test.local.com') . '/cart',
                'line_items'           => [
                    [
                        'quantity'   => 1,
                        'price_data' => [
                            'currency'     => 'usd',
                            'unit_amount'  => (int) ($iTotal * 100),
                            'product_data' => [
                                'name' => 'test'
                            ]
                        ]
                    ]
                ],
            ]);
            return [
                'result' => true,
                'data'   => $aResponse->url
            ];
        } catch (ApiErrorException $oException) {
            Log::error($oException->getMessage());
        }

        return [
            'result' => false,
            'error'  => 'Something went wrong'
        ];
    }
}
