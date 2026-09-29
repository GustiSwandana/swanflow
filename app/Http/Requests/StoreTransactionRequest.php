<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTransactionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Clean formatted numeric inputs while preserving negative values and native numeric inputs.
     */
    protected function cleanNumericInput(mixed $val): mixed
    {
        if ($val === null || $val === '') {
            return $val;
        }

        if (is_numeric($val)) {
            return $val;
        }

        $str = trim((string) $val);
        $isNegative = str_starts_with($str, '-');
        $cleaned = preg_replace('/[^0-9]/', '', $str);

        if ($cleaned === '') {
            return $val;
        }

        return $isNegative ? "-$cleaned" : $cleaned;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('amount')) {
            $this->merge(['amount' => $this->cleanNumericInput($this->input('amount'))]);
        }

        if ($this->has('admin_fee') && $this->input('admin_fee') !== null && $this->input('admin_fee') !== '') {
            $adminFee = $this->cleanNumericInput($this->input('admin_fee'));
            $this->merge(['admin_fee' => $adminFee !== '' ? $adminFee : 0]);
        }
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
            'target_wallet_id' => [
                Rule::excludeIf(fn () => $this->input('type') !== 'transfer'),
                'required',
                'different:wallet_id',
                'exists:wallets,id',
            ],
            'category_id' => [
                Rule::excludeIf(fn () => $this->input('type') === 'transfer'),
                'required',
                'exists:categories,id',
            ],
            'type' => ['required', 'in:income,expense,transfer'],
            'amount' => ['required', 'numeric', 'min:1'],
            'admin_fee' => ['nullable', 'numeric', 'min:0'],
            'fee_payer' => ['nullable', 'in:source,destination'],
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
