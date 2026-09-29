<?php

namespace App\Http\Controllers;

use App\Services\MidtransService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MidtransWebhookController extends Controller
{
    /**
     * Handle incoming payment notification webhook from Midtrans.
     */
    public function handleNotification(Request $request, MidtransService $midtransService): JsonResponse
    {
        $payload = $request->all();

        if (empty($payload)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Empty notification payload.',
            ], 200);
        }

        try {
            $result = $midtransService->handleNotification($payload);

            return response()->json([
                'status' => 'success',
                'message' => 'Notification processed successfully.',
                'transaction_status' => $result['status'] ?? null,
            ], 200);
        } catch (Exception $e) {
            Log::warning('Midtrans Webhook Handled Warning: '.$e->getMessage(), [
                'order_id' => $payload['order_id'] ?? null,
                'status' => $payload['transaction_status'] ?? null,
            ]);

            // Always return HTTP 200 to Midtrans to acknowledge receipt and prevent "Delivery Failed" alert emails
            return response()->json([
                'status' => 'handled',
                'message' => $e->getMessage(),
            ], 200);
        }
    }
}
