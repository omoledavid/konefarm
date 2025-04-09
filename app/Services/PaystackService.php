<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class PaystackService
{
    protected $apiKey;

    public function __construct()
    {
        $this->apiKey = env('PAYSTACK_SECRET_KEY');
    }

    /**
     * Validate bank account number.
     *
     * @param string $accountNumber
     * @param string $bankCode
     * @return array
     */
    public function validateBankAccount($accountNumber, $bankCode)
    {
        $url = "https://api.paystack.co/bank/resolve";
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Cache-Control' => 'no-cache',
        ])->get($url, [
            'account_number' => $accountNumber,
            'bank_code' => $bankCode,
        ]);

        if ($response->failed()) {
            return [
                'error' => true,
                'message' => "HTTP Error: " . $response->body(),
            ];
        }

        return [
            'error' => false,
            'data' => $response->json(),
        ];
    }
}
