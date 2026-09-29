<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderBankAccount;
use App\Models\OrderSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ClientPortalController extends Controller
{
    /**
     * Show the public Client Portal page for a specific project token.
     */
    /**
     * Show the public Client Portal page for a specific project token.
     */
    public function show(string $token): View
    {
        $order = Order::where('token', $token)
            ->with(['deliverables', 'storedFile', 'folder'])
            ->firstOrFail();

        $settings = OrderSetting::firstOrCreate(
            ['user_id' => $order->user_id],
            ['studio_name' => 'Lensa Art Studio']
        );

        $bankAccounts = OrderBankAccount::where('user_id', $order->user_id)
            ->where('is_active', true)
            ->get();

        $downloadUrl = $this->resolveDownloadUrl($order);

        // Deliverable metadata for SwanDrive integration
        $deliverableType = 'external';
        $zipUrl = null;
        $folderName = null;
        $fileName = null;
        $fileCount = 0;
        $fileSizeFormatted = null;

        if ($order->folder_id && $order->folder) {
            $deliverableType = 'folder';
            $folderName = $order->folder->name;
            $fileCount = $order->folder->files()->count();
            if ($order->status === 'verified' || $order->is_free) {
                $zipUrl = route('drive.shared.folder.zip', ['token' => $order->folder->share_token]);
            }
        } elseif ($order->stored_file_id && $order->storedFile) {
            $deliverableType = 'file';
            $fileName = $order->storedFile->original_name ?: $order->storedFile->title;
            $fileSizeFormatted = $order->storedFile->formatted_size;
        }

        return view('pay.client', [
            'order' => $order,
            'settings' => $settings,
            'bankAccounts' => $bankAccounts,
            'downloadUrl' => $downloadUrl,
            'deliverableType' => $deliverableType,
            'zipUrl' => $zipUrl,
            'folderName' => $folderName,
            'fileName' => $fileName,
            'fileCount' => $fileCount,
            'fileSizeFormatted' => $fileSizeFormatted,
            'token' => $token,
        ]);
    }

    /**
     * Get JSON data for the client portal (for polling and initial fetch).
     */
    public function getProjectData(string $token): JsonResponse
    {
        $order = Order::where('token', $token)
            ->with(['deliverables', 'storedFile', 'folder'])
            ->first();

        if (! $order) {
            return response()->json([
                'error' => 'Proyek tidak ditemukan atau link ini sudah tidak aktif.',
            ], 404);
        }

        $settings = OrderSetting::firstOrCreate(
            ['user_id' => $order->user_id],
            ['studio_name' => 'Lensa Art Studio']
        );

        $bankAccounts = OrderBankAccount::where('user_id', $order->user_id)
            ->where('is_active', true)
            ->get(['id', 'bank_name', 'account_number', 'account_name']);

        $downloadUrl = $this->resolveDownloadUrl($order);

        // Normalize status for client UI compatibility
        $clientStatus = match ($order->status) {
            'verified' => 'APPROVED',
            'waiting_verification' => 'PENDING_VERIFICATION',
            'rejected' => 'REJECTED',
            default => $order->is_free ? 'APPROVED' : 'UNPAID',
        };

        // Deliverable metadata for SwanDrive integration
        $deliverableType = 'external';
        $zipUrl = null;
        $folderName = null;
        $fileName = null;
        $fileCount = 0;
        $fileSizeFormatted = null;

        if ($order->folder_id && $order->folder) {
            $deliverableType = 'folder';
            $folderName = $order->folder->name;
            $fileCount = $order->folder->files()->count();
            if ($order->status === 'verified' || $order->is_free) {
                $zipUrl = route('drive.shared.folder.zip', ['token' => $order->folder->share_token]);
            }
        } elseif ($order->stored_file_id && $order->storedFile) {
            $deliverableType = 'file';
            $fileName = $order->storedFile->original_name ?: $order->storedFile->title;
            $fileSizeFormatted = $order->storedFile->formatted_size;
        }

        return response()->json([
            'success' => true,
            'project' => [
                'id' => $order->id,
                'token' => $order->token,
                'client_name' => $order->client_name,
                'project_title' => $order->project_title,
                'amount' => (float) $order->amount,
                'discount' => (float) $order->discount,
                'discount_label' => $order->discount_label,
                'final_amount' => (float) $order->final_amount,
                'is_free' => (bool) $order->is_free,
                'status' => $clientStatus,
                'raw_status' => $order->status,
                'gdrive_url' => $downloadUrl,
                'download_url' => $downloadUrl,
                'deliverable_type' => $deliverableType,
                'zip_url' => $zipUrl,
                'folder_name' => $folderName,
                'file_name' => $fileName,
                'file_count' => $fileCount,
                'file_size_formatted' => $fileSizeFormatted,
                'notes' => $order->notes,
                'reject_reason' => $order->rejection_reason,
            ],
            'settings' => [
                'studio_name' => $settings->studio_name,
                'bank_instructions' => $settings->bank_instructions,
                'qris_url' => $settings->qris_image_path ? Storage::url($settings->qris_image_path) : null,
            ],
            'bank_accounts' => $bankAccounts,
        ]);
    }

    /**
     * Handle payment proof upload from client.
     */
    public function uploadProof(Request $request, string $token): JsonResponse
    {
        $order = Order::where('token', $token)->first();

        if (! $order) {
            return response()->json(['error' => 'Proyek tidak ditemukan.'], 404);
        }

        $request->validate([
            'proof' => 'required|file|mimes:jpeg,png,jpg,webp,pdf,heic,heif,gif|max:25600', // max 25MB
        ]);

        // Delete previous proof file if exists
        if ($order->payment_proof_path) {
            $prevFilename = basename($order->payment_proof_path);
            $prevPublic = public_path('uploads/'.$prevFilename);
            if (file_exists($prevPublic)) {
                @unlink($prevPublic);
            }
            if (Storage::disk('public')->exists($order->payment_proof_path)) {
                Storage::disk('public')->delete($order->payment_proof_path);
            }
        }

        $file = $request->file('proof');
        $filename = 'proof_'.$order->id.'_'.time().'.'.$file->getClientOriginalExtension();
        $path = $file->storeAs('payment_proofs', $filename, 'public');

        // Copy to public/uploads for direct static access
        $uploadsDir = public_path('uploads');
        if (! is_dir($uploadsDir)) {
            @mkdir($uploadsDir, 0755, true);
        }
        @copy(storage_path('app/public/'.$path), $uploadsDir.'/'.$filename);

        $order->payment_proof_path = $path;
        $order->status = 'waiting_verification';
        $order->payment_submitted_at = now();
        $order->rejection_reason = null;
        $order->save();

        return $this->getProjectData($token);
    }

    /**
     * Resolve final download URL (External link, SwanFlow file, or SwanFlow folder).
     */
    protected function resolveDownloadUrl(Order $order): ?string
    {
        // Zero Leak Guarantee: only expose link when verified or free
        if ($order->status !== 'verified' && ! $order->is_free) {
            return null;
        }

        if (! empty($order->gdrive_url)) {
            return $order->gdrive_url;
        }

        if ($order->stored_file_id && $order->storedFile) {
            return route('drive.shared.download', ['token' => $order->storedFile->share_token]);
        }

        if ($order->folder_id && $order->folder) {
            return route('drive.shared.folder.view', ['token' => $order->folder->share_token]);
        }

        return '#';
    }
}
