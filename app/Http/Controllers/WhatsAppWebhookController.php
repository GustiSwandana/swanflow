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

        // Log payload to dedicated whatsapp.log so user can debug anytime
        $logLine = sprintf(
            "[%s] SENDER: %s | MSG: %s | RAW: %s\n",
            now()->toDateTimeString(),
            (string) $sender,
            (string) $message,
            json_encode($request->all())
        );
        @file_put_contents(storage_path('logs/whatsapp.log'), $logLine, FILE_APPEND);

        $result = $this->waService->handleIncoming((string) $sender, $message ? (string) $message : null, $imageUrl ? (string) $imageUrl : null);

        // Dispatched outward reply message via Fonnte API (checks User profile token first, then env)
        $user = $this->waService->findUserBySender((string) $sender);
        $fonnteToken = $user?->fonnte_token ?: env('FONNTE_TOKEN');

        if (! empty($fonnteToken)) {
            try {
                $fonnteRes = Http::timeout(8)->withHeaders([
                    'Authorization' => $fonnteToken,
                ])->post('https://api.fonnte.com/send', [
                    'target' => $sender,
                    'message' => $result['reply'],
                ]);
                $sendOutput = sprintf("[%s] FONNTE SEND RES: %s\n", now()->toDateTimeString(), $fonnteRes->body());
                @file_put_contents(storage_path('logs/whatsapp.log'), $sendOutput, FILE_APPEND);
            } catch (\Throwable $e) {
                Log::error('Fonnte send API error: '.$e->getMessage());
                @file_put_contents(storage_path('logs/whatsapp.log'), '['.now()->toDateTimeString().'] FONNTE ERR: '.$e->getMessage()."\n", FILE_APPEND);
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
