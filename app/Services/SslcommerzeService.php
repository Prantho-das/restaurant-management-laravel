<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SslcommerzeService
{
    protected string $baseUrl;

    protected string $storeId;

    protected string $storePassword;

    protected bool $sandbox;

    public function __construct()
    {
        $this->sandbox = Setting::getValue('payment_sslcommerze_sandbox', '1') === '1';
        $this->storeId = Setting::getValue('payment_sslcommerze_store_id', '');
        $this->storePassword = Setting::getValue('payment_sslcommerze_store_password', '');

        $this->baseUrl = $this->sandbox
            ? 'https://sandbox.sslcommerz.com'
            : 'https://securepay.sslcommerz.com';
    }

    /**
     * Create SSLCommerze session and get checkout URL.
     *
     * @return string|null Checkout URL or null on failure
     */
    public function createCheckoutUrl(Order $order): ?string
    {
        $redirectUrl = route('payment.sslcommerze.success');
        $cancelUrl = route('payment.sslcommerze.fail');
        $ipnUrl = route('payment.sslcommerze.ipn');

        $data = [
            'store_id' => $this->storeId,
            'store_passwd' => $this->storePassword,
            'total_amount' => number_format($order->total_amount, 2, '.', ''),
            'currency' => 'BDT',
            'tran_id' => $order->order_number,
            'success_url' => $redirectUrl,
            'fail_url' => $cancelUrl,
            'cancel_url' => $cancelUrl,
            'ipn_url' => $ipnUrl,
            'product_name' => 'POS Order - '.$order->order_number,
            'product_category' => 'Restaurant',
            'product_profile' => 'general',
            'cus_name' => $order->customer_name ?: 'Walking Customer',
            'cus_email' => '',
            'cus_add1' => '',
            'cus_add2' => '',
            'cus_city' => '',
            'cus_state' => '',
            'cus_postcode' => '',
            'cus_country' => ' Bangladesh',
            'cus_phone' => $order->customer_phone ?: 'N/A',
            'cus_fax' => '',
            'ship_name' => '',
            'ship_add1' => '',
            'ship_add2' => '',
            'ship_city' => '',
            'ship_state' => '',
            'ship_postcode' => '',
            'ship_country' => '',
        ];

        try {
            $response = Http::asForm()->post("{$this->baseUrl}/gwprocess/v1/api/session", $data);

            if ($response->successful()) {
                $result = $response->json();

                if (isset($result['status']) && $result['status'] === 'SUCCESS') {
                    $sessionKey = $result['sessionkey'] ?? null;
                    if ($sessionKey) {
                        return "{$this->baseUrl}/gwprocess/v1/session/{$sessionKey}";
                    }
                }

                Log::error('SSLCommerze Session Creation Failed', [
                    'response' => $result,
                    'order' => $order->order_number,
                ]);
            } else {
                Log::error('SSLCommerze API Error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
            }
        } catch (\Exception $e) {
            Log::error('SSLCommerze Session Exception', [
                'error' => $e->getMessage(),
                'order' => $order->order_number,
            ]);
        }

        return null;
    }

    /**
     * Validate and process success callback.
     *
     * @param  array  $data  Response data from SSLCommerze
     * @return array{valid: bool, status: string, amount: float, txn_id: string}
     */
    public function validateResponse(array $data): array
    {
        // SSLCommerze sends status in 'status' field (SUCCESS, FAILED, CANCELLED)
        $status = $data['status'] ?? 'UNKNOWN';
        $amount = (float) ($data['total_amount'] ?? $data['amount'] ?? 0);
        $txnId = $data['tran_id'] ?? $data['transaction_id'] ?? '';

        return [
            'valid' => in_array($status, ['SUCCESS', 'VALID']),
            'status' => $status,
            'amount' => $amount,
            'txn_id' => $txnId,
            'raw' => $data,
        ];
    }

    /**
     * Check if SSLCommerze is enabled.
     */
    public static function isEnabled(): bool
    {
        return Setting::getValue('payment_sslcommerze_enabled', '0') === '1';
    }
}
