<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BkashService
{
    protected string $baseUrl;

    protected string $username;

    protected string $password;

    protected string $appKey;

    protected bool $sandbox;

    public function __construct()
    {
        $this->sandbox = Setting::getValue('payment_bkash_sandbox', '1') === '1';
        $this->username = Setting::getValue('payment_bkash_store_username', '');
        $this->password = Setting::getValue('payment_bkash_store_password', '');
        $this->appKey = Setting::getValue('payment_bkash_app_key', '');

        $this->baseUrl = $this->sandbox
            ? 'https://api-sandbox.bkash.com'
            : 'https://api.bkash.com';
    }

    /**
     * Get OAuth2 grant token for API authentication.
     */
    public function getGrantToken(): ?string
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Basic '.base64_encode($this->username.':'.$this->password),
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->post("{$this->baseUrl}/api/checkout/v1.0.0/entity/type/status");

            if ($response->successful()) {
                return $response->json('id_token') ?? null;
            }

            Log::error('bKash Grant Token Failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
        } catch (\Exception $e) {
            Log::error('bKash Grant Token Exception', ['error' => $e->getMessage()]);
        }

        return null;
    }

    /**
     * Create a payment request and get checkout URL.
     *
     * @return array{payment_id: string, checkout_url: string}|null
     */
    public function createPayment(Order $order): ?array
    {
        $grantToken = $this->getGrantToken();

        if (! $grantToken) {
            return null;
        }

        $callbackUrl = route('payment.bkash.callback');

        $response = Http::withHeaders([
            'Authorization' => 'Bearer '.$grantToken,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'X-App-Key' => $this->appKey,
        ])->post("{$this->baseUrl}/api/checkout/v1.2.0/create", [
            'amount' => number_format($order->total_amount, 2, '.', ''),
            'currency' => 'BDT',
            'intent' => 'sale',
            'merchantInvoiceNumber' => $order->order_number,
            'callbackURL' => $callbackUrl,
            'payerReference' => 'POS Order '.$order->order_number,
            'payerInfo' => [
                'name' => $order->customer_name ?: 'Walking Customer',
                'email' => $order->customer_phone ?: '',
                'mobile' => $order->customer_phone ?: '',
            ],
        ]);

        if ($response->successful()) {
            $data = $response->json();

            return [
                'payment_id' => $data['paymentID'] ?? null,
                'checkout_url' => $data['bkashURL'] ?? null,
            ];
        }

        Log::error('bKash Create Payment Failed', [
            'order' => $order->order_number,
            'status' => $response->status(),
            'body' => $response->body(),
        ]);

        return null;
    }

    /**
     * Execute payment after user approval.
     */
    public function executePayment(string $paymentId, string $grantToken): array
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer '.$grantToken,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'X-App-Key' => $this->appKey,
        ])->post("{$this->baseUrl}/api/checkout/v1.2.0/execute", [
            'paymentID' => $paymentId,
        ]);

        if ($response->successful()) {
            return $response->json();
        }

        Log::error('bKash Execute Payment Failed', [
            'payment_id' => $paymentId,
            'status' => $response->status(),
            'body' => $response->body(),
        ]);

        return ['status' => 'failed'];
    }

    /**
     * Query payment status.
     */
    public function queryPayment(string $paymentId, string $grantToken): array
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer '.$grantToken,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'X-App-Key' => $this->appKey,
        ])->get("{$this->baseUrl}/api/checkout/v1.2.0/payment/{$paymentId}");

        if ($response->successful()) {
            return $response->json();
        }

        Log::error('bKash Query Payment Failed', [
            'payment_id' => $paymentId,
            'status' => $response->status(),
            'body' => $response->body(),
        ]);

        return [];
    }

    /**
     * Verify payment is completed and successful.
     */
    public function verifyPayment(string $paymentId, float $expectedAmount): bool
    {
        $grantToken = $this->getGrantToken();

        if (! $grantToken) {
            return false;
        }

        $data = $this->queryPayment($paymentId, $grantToken);

        return isset($data['transactionStatus']) &&
               $data['transactionStatus'] === 'Success' &&
               isset($data['amount']) &&
               (float) $data['amount'] >= $expectedAmount;
    }

    /**
     * Check if bKash is enabled in settings.
     */
    public static function isEnabled(): bool
    {
        return Setting::getValue('payment_bkash_enabled', '0') === '1';
    }

    /**
     * Get store user ID from order (utility).
     */
    public function getStoreUserId(): string
    {
        // Extract user ID from store username if follows pattern
        // bKash store username is usually the user ID
        return $this->username;
    }
}
