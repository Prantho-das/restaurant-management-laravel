<?php

namespace App\Services;

use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class NotificationService
{
    public function __construct(
        protected SmsService $smsService
    ) {}

    /**
     * Send Order Confirmation to Customer
     */
    public function sendOrderConfirmation(Order $order): void
    {
        // SMS Notification
        if ($order->customer_phone) {
            $message = "Order Confirmed! Your Order ID: {$order->order_number}. Total: ".number_format($order->total_amount, 2).' BDT. Thank you for dining with us!';
            $this->smsService->send($order->customer_phone, $message);
        }

        // Email Notification (If email exists)
        // Since the current order model doesn't have customer_email,
        // we might want to add it or use the associated user's email if applicable.
        /*
        if ($order->customer_email) {
            Mail::to($order->customer_email)->send(new \App\Mail\OrderConfirmation($order));
        }
        */
    }

    /**
     * Send OTP for login or sensitive actions
     */
    public function sendOtp(string $phone, string $otp): bool
    {
        $message = "Your OTP for Antigravity Restaurant POS is: {$otp}. Please do not share this with anyone.";

        return $this->smsService->send($phone, $message);
    }
}
