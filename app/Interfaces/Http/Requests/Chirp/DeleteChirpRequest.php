<?php

namespace App\Interfaces\Http\Requests\Chirp;

use App\Models\Chirp;
use Illuminate\Foundation\Http\FormRequest;

class DeleteChirpRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        /** @var Chirp $chirp */
        $chirp = $this->route('chirp');

        return $this->user()?->can('delete', $chirp) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<mixed>|string>
     */
    public function rules(): array
    {
        return [];
    }
}
