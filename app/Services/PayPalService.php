<?php

namespace App\Services;

use GuzzleHttp\Client;

class PayPalService
{
    protected $client;
    protected $clientId;
    protected $secret;
    protected $apiBaseUrl;

    public function __construct()
    {
        $this->client = new Client();
        $this->clientId = env('PAYPAL_CLIENT_ID');
        $this->secret = env('PAYPAL_SECRET');
        $this->apiBaseUrl = env('PAYPAL_API_BASE_URL');
    }

    public function getAccessToken()
    {
        $response = $this->client->post($this->apiBaseUrl . '/v1/oauth2/token', [
            'headers' => [
                'Authorization' => 'Basic ' . base64_encode($this->clientId . ':' . $this->secret),
                'Content-Type'  => 'application/x-www-form-urlencoded',
            ],
            'form_params' => [
                'grant_type' => 'client_credentials',
            ],
        ]);

        $body = json_decode((string) $response->getBody(), true);

        return $body['access_token'];
    }

    public function createOrder($value)
    {
        try {
            $accessToken = $this->getAccessToken();

            $response = $this->client->post($this->apiBaseUrl . '/v2/checkout/orders', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Content-Type'  => 'application/json',
                ],
                'json' => [
                    'intent' => 'CAPTURE',
                    'purchase_units' => [
                        [
                            'amount' => [
                                'currency_code' => 'EUR',
                                'value' => $value,
                            ],
                        ],
                    ],
                    'payment_source' => [
                        'paypal' => [
                            'experience_context' => [
                                'payment_method_preference' => 'IMMEDIATE_PAYMENT_REQUIRED',
                                'brand_name' => 'test',
                                'locale' => 'en',
                                'landing_page' => 'LOGIN',
                                'user_action' => 'PAY_NOW',
                                // 'return_url' => route('paypal.success'),
                                // 'cancel_url' => route('paypal.cancel'),
                            ],
                        ],
                    ],
                    'application_context' => [
                        'return_url' => route('paypal.success'),
                        'cancel_url' => route('paypal.cancel'),
                    ],
                ],
            ]);

            $body = json_decode((string) $response->getBody(), true);

            if (isset($body['links'][1])) {
                foreach ($body['links'][1] as $link) {
                    return $link;
                }
            }

            throw new \Exception('Could not create PayPal order.');
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            $errorResponse = $e->getResponse();
            $errorBody = $errorResponse ? (string) $errorResponse->getBody() : 'No response body';
        }
    }

}
