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
            ], 400);
        }

        try {
            $result = $midtransService->handleNotification($payload);

            return response()->json([
                'status' => 'success',
                'message' => 'Notification processed successfully.',
                'transaction_status' => $result['status'] ?? null,
            ]);
        } catch (Exception $e) {
            Log::error('Midtrans Webhook Controller Exception: '.$e->getMessage(), [
                'payload' => $payload,
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
