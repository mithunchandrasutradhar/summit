<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\ExhibitorApplication;
use App\Models\Payment;
use App\Models\SponsorshipEnquiry;
use App\Services\Payments\PaymentGateway;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function __construct(protected PaymentGateway $gateway) {}

    public function initiateSponsorship(string $locale, SponsorshipEnquiry $enquiry): RedirectResponse
    {
        abort_unless($enquiry->sponsorTier, 404);

        return $this->initiate($enquiry, (float) $enquiry->sponsorTier->price, [
            'customer_name' => $enquiry->contactPerson(),
            'customer_email' => $enquiry->email,
            'customer_phone' => $enquiry->field_values['phone'] ?? null,
            'product_name' => 'Sponsorship — '.$enquiry->sponsorTier->name,
        ]);
    }

    public function initiateExhibition(string $locale, ExhibitorApplication $application): RedirectResponse
    {
        abort_unless($application->preferredBooth, 404);

        return $this->initiate($application, (float) $application->preferredBooth->price, [
            'customer_name' => $application->contactPerson(),
            'customer_email' => $application->email,
            'customer_phone' => $application->field_values['phone'] ?? null,
            'product_name' => 'Exhibition Booth — '.$application->preferredBooth->booth_no,
        ]);
    }

    protected function initiate(Model $payable, float $amount, array $meta): RedirectResponse
    {
        $payment = Payment::create([
            'payable_type' => $payable->getMorphClass(),
            'payable_id' => $payable->getKey(),
            'amount' => $amount,
            'currency' => 'BDT',
            'gateway' => 'sslcommerz',
            'status' => 'initiated',
        ]);

        $result = $this->gateway->initiate($payable, $amount, 'BDT', $meta);

        if (! $result['redirect_url']) {
            $payment->update(['status' => 'failed']);

            return redirect()->route('payments.sslcommerz.fail');
        }

        $payment->update([
            'gateway_txn_id' => $result['transaction_id'],
            'status' => 'pending',
        ]);

        return redirect()->away($result['redirect_url']);
    }

    public function ipn(Request $request)
    {
        $this->handleCallback($request->all());

        return response('OK');
    }

    public function success(Request $request): View
    {
        $this->handleCallback($request->all());

        return view('payments.success');
    }

    public function fail(Request $request): View
    {
        $this->handleCallback($request->all());

        return view('payments.fail');
    }

    public function cancel(Request $request): View
    {
        return view('payments.cancel');
    }

    /**
     * Shared by the IPN (server-to-server, authoritative) and the
     * success/fail browser redirects (which may arrive before or instead of
     * the IPN) — idempotent, and always re-verifies against the gateway's
     * own validation API rather than trusting the posted payload.
     */
    protected function handleCallback(array $payload): void
    {
        $tranId = $payload['tran_id'] ?? null;

        if (! $tranId) {
            return;
        }

        $payment = Payment::where('gateway_txn_id', $tranId)->first();

        if (! $payment || $payment->status === 'paid') {
            return;
        }

        $payment->update(['payload' => $payload]);

        $result = $this->gateway->verify($payload);

        if (! $result['valid']) {
            // Only mark the payment failed on a definitive negative answer from
            // the gateway — an unreachable/erroring validation API is not proof
            // the payment failed, and shouldn't foreclose a later genuine IPN.
            if ($result['determined']) {
                $payment->update(['status' => 'failed']);
            }

            return;
        }

        // A VALID val_id only proves *some* transaction was validated — it must
        // still match the payment we're trying to confirm, otherwise a val_id
        // for a smaller/unrelated transaction could be replayed against this one.
        $tranIdMatches = ($result['tran_id'] ?? null) === $payment->gateway_txn_id;
        $amountMatches = isset($result['amount']) && round((float) $result['amount'], 2) === round((float) $payment->amount, 2);
        $currencyMatches = isset($result['currency']) && $result['currency'] === $payment->currency;

        if (! $tranIdMatches || ! $amountMatches || ! $currencyMatches) {
            $payment->update(['status' => 'failed']);

            return;
        }

        $payment->update(['status' => 'paid', 'paid_at' => now()]);

        $payable = $payment->payable;

        if ($payable && method_exists($payable, 'onPaymentConfirmed')) {
            $payable->onPaymentConfirmed($payment);
        }
    }
}
