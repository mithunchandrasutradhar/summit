<?php

use App\Services\Payments\SslcommerzGateway;
use Illuminate\Support\Facades\Http;

it('reports valid with the gateway-reported tran_id/amount/currency when the val_id validates', function () {
    Http::fake([
        '*/validator/api/validationserverAPI.php*' => Http::response([
            'status' => 'VALID',
            'tran_id' => 'TXN-ABC123',
            'amount' => '5000.00',
            'currency' => 'BDT',
        ]),
    ]);

    $result = (new SslcommerzGateway)->verify(['val_id' => 'anything']);

    expect($result['determined'])->toBeTrue();
    expect($result['valid'])->toBeTrue();
    expect($result['tran_id'])->toBe('TXN-ABC123');
    expect($result['amount'])->toBe('5000.00');
    expect($result['currency'])->toBe('BDT');
});

it('is determined-but-invalid when the gateway explicitly reports a non-VALID status', function () {
    Http::fake([
        '*/validator/api/validationserverAPI.php*' => Http::response(['status' => 'INVALID']),
    ]);

    $result = (new SslcommerzGateway)->verify(['val_id' => 'anything']);

    expect($result['determined'])->toBeTrue();
    expect($result['valid'])->toBeFalse();
});

it('is determined-but-invalid immediately when no val_id is present, without calling the gateway', function () {
    Http::fake();

    $result = (new SslcommerzGateway)->verify([]);

    expect($result['determined'])->toBeTrue();
    expect($result['valid'])->toBeFalse();
    Http::assertNothingSent();
});

it('is undetermined (not a definitive failure) when the gateway is unreachable', function () {
    Http::fake([
        '*/validator/api/validationserverAPI.php*' => Http::response([], 500),
    ]);

    $result = (new SslcommerzGateway)->verify(['val_id' => 'anything']);

    expect($result['determined'])->toBeFalse();
    expect($result['valid'])->toBeFalse();
});

it('is undetermined when the HTTP request itself throws (connection error)', function () {
    Http::fake(function () {
        throw new \Illuminate\Http\Client\ConnectionException('could not connect');
    });

    $result = (new SslcommerzGateway)->verify(['val_id' => 'anything']);

    expect($result['determined'])->toBeFalse();
    expect($result['valid'])->toBeFalse();
});

it('generates a transaction id that does not embed the payable id or a predictable timestamp', function () {
    $payable = new class extends \Illuminate\Database\Eloquent\Model {};

    Http::fake([
        '*/gwprocess/v4/api.php' => Http::response(['GatewayPageURL' => 'https://example.test/pay']),
    ]);

    $result = (new SslcommerzGateway)->initiate($payable, 100.0, 'BDT');

    expect($result['transaction_id'])->toStartWith('TXN-');
    expect(strlen($result['transaction_id']))->toBeGreaterThan(20);
});
