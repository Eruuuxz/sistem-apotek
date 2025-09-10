<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule; 

class PelangganRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Atur ke true karena otorisasi akan ditangani oleh middleware di route
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Ambil ID pelanggan dari route jika sedang dalam mode update
        $pelangganId = $this->route('pelanggan') ? $this->route('pelanggan')->id : null;

        return [
            'nama' => ['required', 'string', 'max:255'],
            'telepon' => ['nullable', 'string', 'max:20'],
            'alamat' => ['nullable', 'string', 'max:1000'],
            // no_ktp harus unik, kecuali untuk dirinya sendiri saat update
            'no_ktp' => [
                'nullable',
                'string',
                'max:20',
                Rule::unique('pelanggan', 'no_ktp')->ignore($pelangganId),
            ],
            'file_ktp' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'], // Hanya gambar, max 2MB
            // Sesuaikan validasi status_member dengan tipe enum
            'status_member' => ['required', 'in:member,non_member'], // 'required' karena ada default value
            'point' => ['nullable', 'integer', 'min:0'], // Tambahkan validasi untuk point
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Konversi checkbox status_member menjadi nilai enum yang sesuai
        // Jika checkbox dicentang, set ke 'member', jika tidak, set ke 'non_member'
        $this->merge([
            'status_member' => $this->has('status_member') ? 'member' : 'non_member',
            'point' => $this->input('point', 0), // Pastikan point selalu ada, default 0
        ]);
    }
}