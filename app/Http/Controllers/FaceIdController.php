<?php

namespace App\Http\Controllers;

use App\Models\BiometricCredential;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FaceIdController extends Controller
{
    /**
     * Provide a challenge for Face ID login.
     */
    public function loginChallenge(Request $request): JsonResponse
    {
        $challenge = bin2hex(random_bytes(32));
        $request->session()->put('face_id_login_challenge', $challenge);

        $credentials = BiometricCredential::select('credential_id')->get()->pluck('credential_id');

        return response()->json([
            'challenge' => $challenge,
            'registered' => $credentials->isNotEmpty(),
            'rpId' => $request->getHost(),
            'credentials' => $credentials,
        ]);
    }

    /**
     * Verify Face ID assertion and log the user in.
     */
    public function loginVerify(Request $request): JsonResponse
    {
        $request->validate([
            'credential_id' => ['required', 'string'],
            'client_data_json' => ['nullable', 'string'],
        ]);

        $storedChallenge = $request->session()->get('face_id_login_challenge');

        if (! $storedChallenge) {
            return response()->json([
                'success' => false,
                'message' => 'Sesi challenge Face ID telah kadaluarsa. Silakan coba lagi.',
            ], 422);
        }

        // If client_data_json is provided (from WebAuthn response), verify challenge
        if ($request->filled('client_data_json')) {
            $clientData = json_decode(base64_decode($request->string('client_data_json')), true);
            if (is_array($clientData) && isset($clientData['challenge'])) {
                // Remove padding if any
                $receivedChallenge = rtrim($clientData['challenge'], '=');
                $expectedChallenge = rtrim($storedChallenge, '=');
                if ($receivedChallenge !== $expectedChallenge && base64_encode(hex2bin($storedChallenge)) !== $clientData['challenge']) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Verifikasi biometrik gagal (challenge mismatch).',
                    ], 422);
                }
            }
        }

        $credential = BiometricCredential::where('credential_id', $request->string('credential_id'))->first();

        if (! $credential || ! $credential->user) {
            return response()->json([
                'success' => false,
                'message' => 'Kredensial Face ID tidak ditemukan atau belum terdaftar.',
            ], 404);
        }

        $credential->update([
            'last_used_at' => now(),
        ]);

        $request->session()->forget('face_id_login_challenge');
        Auth::login($credential->user, true);
        $request->session()->regenerate();

        return response()->json([
            'success' => true,
            'message' => 'Verifikasi Face ID berhasil. Selamat datang kembali, '.$credential->user->name.'!',
            'redirect' => route('dashboard'),
        ]);
    }

    /**
     * Provide a challenge for Face ID registration (requires authenticated user).
     */
    public function registerChallenge(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        $challenge = bin2hex(random_bytes(32));
        $request->session()->put('face_id_register_challenge', $challenge);

        return response()->json([
            'challenge' => $challenge,
            'rp' => [
                'name' => 'SwanFlow',
                'id' => $request->getHost(),
            ],
            'user' => [
                'id' => (string) $user->id,
                'name' => $user->email,
                'displayName' => $user->name,
            ],
        ]);
    }

    /**
     * Verify and store newly registered Face ID credential.
     */
    public function registerVerify(Request $request): JsonResponse
    {
        $request->validate([
            'credential_id' => ['required', 'string'],
            'device_name' => ['nullable', 'string', 'max:100'],
            'public_key' => ['nullable', 'string'],
            'client_data_json' => ['nullable', 'string'],
        ]);

        $storedChallenge = $request->session()->get('face_id_register_challenge');

        if (! $storedChallenge) {
            return response()->json([
                'success' => false,
                'message' => 'Sesi pendaftaran Face ID telah kadaluarsa. Silakan ulangi proses pendaftaran.',
            ], 422);
        }

        /** @var User $user */
        $user = $request->user();

        $credential = $user->biometricCredentials()->updateOrCreate(
            ['credential_id' => $request->string('credential_id')],
            [
                'device_name' => $request->input('device_name', 'iPhone / Mobile Device'),
                'public_key' => $request->input('public_key'),
                'last_used_at' => now(),
            ]
        );

        $request->session()->forget('face_id_register_challenge');

        return response()->json([
            'success' => true,
            'message' => 'Face ID berhasil diaktifkan pada perangkat ini!',
            'credential_id' => $credential->id,
        ]);
    }

    /**
     * Delete a registered biometric credential.
     */
    public function destroy(Request $request, BiometricCredential $credential): RedirectResponse
    {
        if ($credential->user_id !== $request->user()->id) {
            abort(403, 'Aksi tidak diizinkan.');
        }

        $credential->delete();

        return redirect()->route('profile.edit')->with('success', 'Kredensial Face ID berhasil dihapus dari perangkat ini.');
    }
}
