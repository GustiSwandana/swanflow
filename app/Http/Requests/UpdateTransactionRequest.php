<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTransactionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<mixed>>
     */
    public function rules(): array
    {
        return [
            'wallet_id' => ['required', 'exists:wallets,id'],
            'target_wallet_id' => ['required_if:type,transfer', 'nullable', 'different:wallet_id', 'exists:wallets,id'],
            'category_id' => ['required_unless:type,transfer', 'nullable', 'exists:categories,id'],
            'type' => ['required', 'in:income,expense,transfer'],
            'amount' => ['required', 'numeric', 'min:1'],
            'date' => ['required', 'date'],
            'description' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'wallet_id.required' => 'Silakan pilih dompet yang digunakan.',
            'wallet_id.exists' => 'Dompet yang dipilih tidak valid.',
            'target_wallet_id.required_if' => 'Silakan pilih dompet tujuan transfer.',
            'target_wallet_id.different' => 'Dompet tujuan tidak boleh sama dengan dompet asal.',
            'target_wallet_id.exists' => 'Dompet tujuan yang dipilih tidak valid.',
            'category_id.required_unless' => 'Silakan pilih kategori transaksi.',
            'category_id.exists' => 'Kategori yang dipilih tidak valid.',
            'type.required' => 'Tipe transaksi wajib ditentukan.',
            'type.in' => 'Tipe transaksi harus berupa pemasukan, pengeluaran, atau transfer.',
            'amount.required' => 'Nominal transaksi wajib diisi.',
            'amount.numeric' => 'Nominal harus berupa angka yang valid.',
            'amount.min' => 'Nominal transaksi minimal Rp 1.',
            'date.required' => 'Tanggal transaksi wajib diisi.',
            'date.date' => 'Format tanggal tidak valid.',
        ];
    }
}
