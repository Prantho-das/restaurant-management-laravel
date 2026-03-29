<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\BkashService;
use App\Services\SslcommerzeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    protected BkashService $bkashService;

    protected SslcommerzeService $sslcommerzeService;

    public function __construct()
    {
        $this->bkashService = new BkashService;
        $this->sslcommerzeService = new SslcommerzeService;
    }

    /**
     * Initiate bKash payment for an order.
     * Creates order as pending and redirects to bKash.
     */
    public function initiateBkash(Order $order, Request $request)
    {
        if (! $this->bkashService->isEnabled()) {
            return back()->with('error', 'bKash payment is disabled.');
        }

        // Validate order is pending and amount matches
        if ($order->status !== 'pending') {
            return back()->with('error', 'Order is not in pending status.');
        }

        // Get checkout URL from bKash
        $result = $this->bkashService->createPayment($order);

        if (! $result || ! isset($result['checkout_url'])) {
            return back()->with('error', 'Failed to initialize bKash payment. Please try again.');
        }

        // Store payment ID in order reference_no and session
        $order->update(['reference_no' => $result['payment_id']]);
        session(['bkash_payment_id' => $result['payment_id']]);

        // Redirect to bKash checkout page
        return redirect($result['checkout_url']);
    }

    /**
     * Handle bKash callback (user returns from bKash after approval).
     */
    public function bkashCallback(Request $request)
    {
        $paymentId = $request->query('paymentID') ?? session('bkash_payment_id');

        if (! $paymentId) {
            return redirect()->route('pos')->with('error', 'No payment ID found.');
        }

        // Find order by payment ID reference
        $order = Order::where('reference_no', $paymentId)
            ->orWhere('order_number', $request->query('order_number', ''))
            ->first();

        if (! $order) {
            return redirect()->route('pos')->with('error', 'Order not found.');
        }

        // Verify payment with bKash
        $isVerified = $this->bkashService->verifyPayment($paymentId, $order->total_amount);

        if ($isVerified) {
            $order->update([
                'status' => 'completed',
                'payment_status' => 'paid',
                'transaction_id' => $paymentId,
                'gateway_response' => json_encode($request->all()),
                'paid_at' => now(),
            ]);

            Log::info('bKash payment successful', ['order' => $order->order_number]);

            return redirect()->route('pos')->with('success', 'Payment successful! Order completed.');
        } else {
            $order->update([
                'status' => 'failed',
                'payment_status' => 'failed',
                'gateway_response' => json_encode($request->all()),
            ]);

            Log::warning('bKash payment failed', ['order' => $order->order_number]);

            return redirect()->route('pos')->with('error', 'Payment failed. Please try again.');
        }
    }

    /**
     * bKash execute endpoint (alternative flow).
     */
    public function bkashExecute(Request $request)
    {
        $paymentId = $request->query('paymentID');

        if (! $paymentId) {
            return response()->json(['error' => 'No payment ID'], 400);
        }

        $grantToken = $this->bkashService->getGrantToken();

        if (! $grantToken) {
            return response()->json(['error' => 'Unable to get grant token'], 500);
        }

        $result = $this->bkashService->executePayment($paymentId, $grantToken);

        return response()->json($result);
    }

    /**
     * Initiate SSLCommerze payment.
     */
    public function initiateSslcommerze(Order $order)
    {
        if (! $this->sslcommerzeService->isEnabled()) {
            return back()->with('error', 'SSLCommerze payment is disabled.');
        }

        if ($order->status !== 'pending') {
            return back()->with('error', 'Order is not in pending status.');
        }

        $checkoutUrl = $this->sslcommerzeService->createCheckoutUrl($order);

        if (! $checkoutUrl) {
            return back()->with('error', 'Failed to create SSLCommerze session. Please try again.');
        }

        return redirect($checkoutUrl);
    }

    /**
     * Handle SSLCommerze successful payment.
     */
    public function sslcommerzeSuccess(Request $request)
    {
        $data = $request->all();
        $validation = $this->sslcommerzeService->validateResponse($data);

        $orderNumber = $data['tran_id'] ?? $data['order_number'] ?? null;
        $order = $orderNumber ? Order::where('order_number', $orderNumber)->first() : null;

        if ($order && $validation['valid']) {
            $order->update([
                'status' => 'completed',
                'payment_status' => 'paid',
                'transaction_id' => $validation['txn_id'],
                'gateway_response' => json_encode($data),
                'paid_at' => now(),
            ]);

            Log::info('SSLCommerze payment successful', ['order' => $order->order_number]);

            return redirect()->route('pos')->with('success', 'Payment successful! Order completed.');
        }

        $statusMessage = match ($validation['status']) {
            'FAILED' => 'Payment failed.',
            'CANCELLED' => 'Payment was cancelled.',
            default => 'Payment verification failed.',
        };

        if ($order) {
            $order->update([
                'status' => 'failed',
                'payment_status' => 'failed',
                'gateway_response' => json_encode($data),
            ]);
        }

        return redirect()->route('pos')->with('error', $statusMessage);
    }

    /**
     * Handle SSLCommerze failed payment.
     */
    public function sslcommerzeFail(Request $request)
    {
        $data = $request->all();
        $orderNumber = $data['tran_id'] ?? $data['order_number'] ?? null;

        if ($orderNumber) {
            $order = Order::where('order_number', $orderNumber)->first();
            if ($order) {
                $order->update([
                    'status' => 'failed',
                    'payment_status' => 'failed',
                    'gateway_response' => json_encode($data),
                ]);
            }
        }

        Log::warning('SSLCommerze payment failed', ['data' => $data]);

        return redirect()->route('pos')->with('error', 'Payment failed or was cancelled.');
    }

    /**
     * Handle SSLCommerze IPN (Instant Payment Notification).
     * This is called by SSLCommerze server for async payment confirmation.
     */
    public function sslcommerzeIpn(Request $request)
    {
        $data = $request->all();
        $validation = $this->sslcommerzeService->validateResponse($data);

        $orderNumber = $data['tran_id'] ?? null;
        $order = $orderNumber ? Order::where('order_number', $orderNumber)->first() : null;

        if ($order && $validation['valid']) {
            $order->update([
                'status' => 'completed',
                'payment_status' => 'paid',
                'transaction_id' => $validation['txn_id'],
                'gateway_response' => json_encode($data),
                'paid_at' => now(),
            ]);

            Log::info('SSLCommerze IPN payment successful', ['order' => $order->order_number]);

            return response('OK', 200);
        }

        return response('FAILED', 400);
    }
}
