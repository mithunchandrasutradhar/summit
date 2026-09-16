<?php

namespace App\Services\Payments;

use Illuminate\Database\Eloquent\Model;

interface PaymentGateway
{
    /**
     * Start a payment session for the given payable model and return the
     * gateway's transaction id plus the URL to redirect the payer to.
     *
     * @return array{transaction_id: string, redirect_url: string}
     */
    public function initiate(Model $payable, float $amount, string $currency, array $meta = []): array;

    /**
     * Verify a callback/IPN payload against the gateway's validation API.
     *
     * @return array{determined: bool, valid: bool, tran_id?: string, amount?: string, currency?: string}
     *
     * `determined` is false when the gateway couldn't be reached at all (network
     * error/non-2xx) — callers should leave the payment's status unchanged in that
     * case rather than treating an unreachable gateway as a failed payment.
     * `valid` reflects only the gateway's own status field; callers must still
     * cross-check `tran_id`/`amount`/`currency` against the payment they expected,
     * since a validly-validated val_id could belong to a different transaction.
     */
    public function verify(array $payload): array;
}
