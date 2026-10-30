<?php

namespace App\Http\Controllers;

use App\Services\WhatsAppTransactionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppWebhookController extends Controller
{
    public function __construct(
        protected WhatsAppTransactionService $waService
    ) {}

    /**
     * Handle incoming webhook verification (Meta WhatsApp Cloud API challenge) or webhook status check.
     */
    public function verify(Request $request): Response|JsonResponse
    {
        if ($request->has('hub_challenge')) {
            $verifyToken = env('WHATSAPP_VERIFY_TOKEN', 'swanflow_wa_secret');
            if ($request->input('hub_verify_token') === $verifyToken) {
                return response($request->input('hub_challenge'), 200, ['Content-Type' => 'text/plain']);
            }

            return response('Forbidden', 403);
        }

        return response()->json([
            'status' => 'active',
            'service' => 'SwanFlow WhatsApp Gateway Webhook',
            'timestamp' => now()->toISOString(),
        ]);
    }

    /**
     * Process incoming WhatsApp messages (from Fonnte, Meta, or direct HTTP API).
     */
    public function handle(Request $request): JsonResponse
    {
        Log::info('Incoming WhatsApp webhook payload: ', $request->all());

        $sender = $request->input('sender') ?? $request->input('from') ?? $request->input('phone');
        $message = $request->input('message') ?? $request->input('text') ?? $request->input('body');
        $imageUrl = $request->input('url') ?? $request->input('image_url') ?? $request->input('file');

        // Meta Cloud API payload extraction
        if ($request->has('entry')) {
            $entryMsg = $request->input('entry.0.changes.0.value.messages.0');
            if ($entryMsg) {
                $sender = $entryMsg['from'] ?? $sender;
                if (($entryMsg['type'] ?? '') === 'text') {
                    $message = $entryMsg['text']['body'] ?? $message;
                }
            }
        }

        if (empty($sender)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Sender phone number is required.',
            ], 400);
        }

        $result = $this->waService->handleIncoming((string) $sender, $message ? (string) $message : null, $imageUrl ? (string) $imageUrl : null);

        // If Fonnte Token is configured, optionally dispatch outward reply message via Fonnte API
        $fonnteToken = env('FONNTE_TOKEN');
        if (! empty($fonnteToken)) {
            try {
                Http::timeout(5)->withHeaders([
                    'Authorization' => $fonnteToken,
                ])->post('https://api.fonnte.com/send', [
                    'target' => $sender,
                    'message' => $result['reply'],
                ]);
            } catch (\Throwable $e) {
                Log::warning('Fonnte send API notification error: '.$e->getMessage());
            }
        }

        // Return Fonnte auto-reply compatible JSON structure
        return response()->json([
            'status' => $result['success'] ? 'success' : 'failed',
            'reply' => $result['reply'],
            'transaction_id' => $result['transaction']?->id,
            'amount' => $result['transaction']?->amount,
        ]);
    }
}
