<?php

namespace App\Services\Payments;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

/**
 * SSLCommerz session-based checkout: initiate() opens a payment session via
 * their "api.php" endpoint and returns the GatewayPageURL to redirect the
 * payer to; verify() re-checks a val_id against their validation API rather
 * than trusting the callback payload's own "status" field, since that can be
 * spoofed by a third party posting directly to our IPN/success URLs.
 */
class SslcommerzGateway implements PaymentGateway
{
    protected string $apiBase;

    public function __construct()
    {
        $this->apiBase = config('services.sslcommerz.sandbox', true)
            ? 'https://sandbox.sslcommerz.com'
            : 'https://securepay.sslcommerz.com';
    }

    public function initiate(Model $payable, float $amount, string $currency, array $meta = []): array
    {
        // Deliberately not derived from the payable's id/timestamp: a guessable
        // tran_id would let someone grief a real pending payment by POSTing a
        // garbage IPN for it. See verify() for the corresponding cross-check.
        $tranId = 'TXN-'.Str::upper(Str::random(24));

        $response = Http::asForm()->post("{$this->apiBase}/gwprocess/v4/api.php", [
            'store_id' => config('services.sslcommerz.store_id'),
            'store_passwd' => config('services.sslcommerz.store_password'),
            'total_amount' => $amount,
            'currency' => $currency,
            'tran_id' => $tranId,
            'success_url' => route('payments.sslcommerz.success'),
            'fail_url' => route('payments.sslcommerz.fail'),
            'cancel_url' => route('payments.sslcommerz.cancel'),
            'ipn_url' => route('payments.sslcommerz.ipn'),
            'shipping_method' => 'NO',
            'product_name' => $meta['product_name'] ?? config('app.name'),
            'product_category' => 'service',
            'product_profile' => 'general',
            'cus_name' => $meta['customer_name'] ?? 'N/A',
            'cus_email' => $meta['customer_email'] ?? 'no-reply@example.com',
            'cus_add1' => 'N/A',
            'cus_city' => 'Dhaka',
            'cus_country' => 'Bangladesh',
            'cus_phone' => $meta['customer_phone'] ?? 'N/A',
            'num_of_item' => 1,
        ]);

        $data = $response->json() ?? [];

        return [
            'transaction_id' => $tranId,
            'redirect_url' => $data['GatewayPageURL'] ?? '',
        ];
    }

    public function verify(array $payload): array
    {
        $valId = $payload['val_id'] ?? null;

        if (! $valId) {
            return ['determined' => true, 'valid' => false];
        }

        try {
            $response = Http::get("{$this->apiBase}/validator/api/validationserverAPI.php", [
                'val_id' => $valId,
                'store_id' => config('services.sslcommerz.store_id'),
                'store_passwd' => config('services.sslcommerz.store_password'),
                'format' => 'json',
            ]);
        } catch (\Throwable) {
            return ['determined' => false, 'valid' => false];
        }

        if ($response->failed()) {
            return ['determined' => false, 'valid' => false];
        }

        $data = $response->json() ?? [];
        $status = $data['status'] ?? null;

        return [
            'determined' => true,
            'valid' => in_array($status, ['VALID', 'VALIDATED'], true),
            'tran_id' => $data['tran_id'] ?? null,
            'amount' => $data['amount'] ?? null,
            'currency' => $data['currency'] ?? null,
        ];
    }
}
