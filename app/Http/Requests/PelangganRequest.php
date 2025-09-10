<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PelangganRequest extends FormRequest
{
    /**
     * Tentukan apakah user berhak melakukan request ini.
     * Middleware auth sudah mengatur otorisasi, jadi return true.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Aturan validasi untuk request ini.
     */
    public function rules(): array
    {
        // Ambil ID pelanggan untuk pengecualian unique saat update
        $pelangganId = $this->route('pelanggan') ? $this->route('pelanggan')->id : null;

        return [
            'nama' => ['required', 'string', 'max:255'],
            'telepon' => ['nullable', 'string', 'max:20'],
            'alamat' => ['nullable', 'string', 'max:1000'],
            'no_ktp' => [
                'nullable',
                'string',
                'max:20',
                Rule::unique('pelanggan', 'no_ktp')->ignore($pelangganId),
            ],
            'file_ktp' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'], // max 2MB
            'status_member' => ['required', 'in:member,non_member'], // Validasi ini akan bekerja sekarang
            'point' => ['nullable', 'integer', 'min:0'],
        ];
    }

    /**
     * Prepare data sebelum validasi.
     * Hanya atur 'point' jika tidak ada, biarkan 'status_member' divalidasi secara alami.
     */
    protected function prepareForValidation(): void
    {
        // Jika status_member tidak ada dalam request (misal, karena tidak dipilih dari dropdown),
        // maka set ke null agar validasi 'required' bisa menangkapnya.
        // Jika ada, gunakan nilai yang dikirim.
        $statusMemberValue = $this->input('status_member');
        if (empty($statusMemberValue)) {
            $statusMemberValue = null; // Set to null or empty string so 'required' rule fails
        }

        $this->merge([
            'status_member' => $statusMemberValue,
            'point' => $this->input('point', 0),
        ]);
    }
}
