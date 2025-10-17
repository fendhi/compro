<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    /**
     * Handle BCA QRIS webhook callback
     * Endpoint: POST /webhook/bca-qris
     */
    public function handleBCAQRIS(Request $request)
    {
        // Log incoming webhook for debugging
        Log::info('BCA QRIS Webhook Received', [
            'payload' => $request->all(),
            'headers' => $request->headers->all()
        ]);

        try {
            // Validate webhook signature (jika BCA provide signature)
            // if (!$this->validateSignature($request)) {
            //     return response()->json(['status' => 'error', 'message' => 'Invalid signature'], 401);
            // }

            // Extract payment data
            $amount = $request->input('amount'); // Rp 50000
            $reference = $request->input('reference'); // ORDER-001
            $status = $request->input('status'); // success/failed
            $transactionId = $request->input('transaction_id'); // BCA transaction ID
            $timestamp = $request->input('timestamp'); // Payment time

            // Validate required fields
            if (!$amount || !$reference || !$status) {
                Log::error('BCA QRIS Webhook: Missing required fields', $request->all());
                return response()->json([
                    'status' => 'error',
                    'message' => 'Missing required fields'
                ], 400);
            }

            // Only process successful payments
            if ($status !== 'success' && $status !== 'PAID' && $status !== 'SUCCESS') {
                Log::info('BCA QRIS Webhook: Payment not successful', ['status' => $status]);
                return response()->json([
                    'status' => 'ignored',
                    'message' => 'Payment not successful'
                ], 200);
            }

            // Find transaction by order_id (reference)
            $transaksi = Transaksi::where('order_id', $reference)
                                  ->where('payment_status', 'pending')
                                  ->first();

            if (!$transaksi) {
                Log::warning('BCA QRIS Webhook: Transaction not found', [
                    'reference' => $reference,
                    'amount' => $amount
                ]);
                return response()->json([
                    'status' => 'error',
                    'message' => 'Transaction not found'
                ], 404);
            }

            // Verify amount matches
            if ($transaksi->total_harga != $amount) {
                Log::error('BCA QRIS Webhook: Amount mismatch', [
                    'expected' => $transaksi->total_harga,
                    'received' => $amount
                ]);
                
                // Still mark as paid but flag for manual review
                $transaksi->update([
                    'payment_status' => 'paid',
                    'payment_verified' => false,
                    'payment_notes' => "Amount mismatch: Expected {$transaksi->total_harga}, Received {$amount}",
                    'external_transaction_id' => $transactionId,
                    'paid_at' => $timestamp ?? now()
                ]);

                return response()->json([
                    'status' => 'warning',
                    'message' => 'Amount mismatch, flagged for review'
                ], 200);
            }

            // Update transaction status
            $transaksi->update([
                'payment_status' => 'paid',
                'payment_verified' => true,
                'external_transaction_id' => $transactionId,
                'paid_at' => $timestamp ?? now()
            ]);

            Log::info('BCA QRIS Webhook: Transaction updated successfully', [
                'order_id' => $reference,
                'amount' => $amount,
                'transaction_id' => $transaksi->id
            ]);

            // Trigger real-time notification to frontend (optional)
            // broadcast(new TransactionPaidEvent($transaksi));

            return response()->json([
                'status' => 'success',
                'message' => 'Payment processed successfully',
                'data' => [
                    'order_id' => $reference,
                    'transaction_id' => $transaksi->id,
                    'amount' => $amount
                ]
            ], 200);

        } catch (\Exception $e) {
            Log::error('BCA QRIS Webhook Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Internal server error'
            ], 500);
        }
    }

    /**
     * Validate webhook signature (jika BCA provide)
     */
    private function validateSignature(Request $request)
    {
        $signature = $request->header('X-BCA-Signature');
        $payload = json_encode($request->all());
        $secret = env('BCA_WEBHOOK_SECRET'); // Set di .env

        if (!$signature || !$secret) {
            return false;
        }

        $expectedSignature = hash_hmac('sha256', $payload, $secret);
        
        return hash_equals($expectedSignature, $signature);
    }

    /**
     * Check payment status (for polling fallback)
     * Endpoint: GET /api/check-payment/{order_id}
     */
    public function checkPaymentStatus($orderId)
    {
        $transaksi = Transaksi::where('order_id', $orderId)->first();

        if (!$transaksi) {
            return response()->json([
                'status' => 'error',
                'message' => 'Transaction not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'order_id' => $orderId,
                'payment_status' => $transaksi->payment_status,
                'total_harga' => $transaksi->total_harga,
                'paid_at' => $transaksi->paid_at,
                'verified' => $transaksi->payment_verified ?? false
            ]
        ]);
    }
}
