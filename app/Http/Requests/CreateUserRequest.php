<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Taktik Ninja: Nyusupin data 'name' otomatis sebelum divalidasi
     */
    protected function prepareForValidation()
    {
        if ($this->has('nama_lengkap')) {
            $this->merge([
                'name' => $this->nama_lengkap, // Duplikat buat kolom lama 'name'
                'status' => ($this->status_akun == 'Aktif') ? 1 : 0 // Duplikat otomatis buat kolom lama 'status'
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        // Aturan validasi baru yang udah disesuaikan sama form Skripsi lu
        return [
            'nama_lengkap' => 'required|string|max:255',
            'username'     => 'required|string|max:255|unique:users,username',
            'password'     => 'required|min:6',
            'id_divisi'    => 'required',
            'status_akun'  => 'required',
            'email'        => 'nullable|email|unique:users,email',
            'nip'          => 'nullable',
            'jabatan'      => 'nullable',
            'no_hp'        => 'nullable'
        ];
    }
}