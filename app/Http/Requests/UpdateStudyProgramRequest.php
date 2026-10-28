<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateStudyProgramRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $studyProgramId = $this->studyProgram ? $this->studyProgram->id : $this->route('studyProgram')?->id;

        return [
            'faculty_id' => ['required', 'exists:faculties,id'],
            'nama' => [
                'required',
                'string',
                'max:255',
                Rule::unique('study_programs', 'nama')
                    ->where(function ($query) {
                        return $query->where('faculty_id', $this->faculty_id);
                    })
                    ->ignore($studyProgramId),
            ],
        ];
    }

    /**
     * Custom error messages in Indonesian.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'faculty_id.required' => 'Fakultas wajib dipilih.',
            'faculty_id.exists' => 'Fakultas yang dipilih tidak valid.',
            'nama.required' => 'Nama Program Studi wajib diisi.',
            'nama.string' => 'Nama Program Studi harus berupa teks.',
            'nama.max' => 'Nama Program Studi tidak boleh lebih dari 255 karakter.',
            'nama.unique' => 'Nama Program Studi sudah ada dalam Fakultas tersebut.',
        ];
    }
}
