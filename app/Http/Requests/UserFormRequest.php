<?php

namespace App\Http\Requests;

use App\Models\User;
use App\Rules\StrongPassword;
use App\Rules\ValidMobileNumber;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UserFormRequest extends FormRequest
{
    protected function failedValidation(Validator $validator)
    {

        throw new HttpResponseException(
            response()->json([
                'status' => false,
                'error' => $validator->errors()
            ], 200)
        );
    }
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = User::VALIDATION_RULES;
        if (request()->update_id) {
            $rules['email'][2] = 'unique:users,email,' . request()->update_id;
            $rules['mobile_no'][1] = 'unique:users,mobile_no,' . request()->update_id;
        } else {
            $rules['password']              = ['required', 'string', 'confirmed', new StrongPassword];
            $rules['password_confirmation'] = ['required', 'string'];
        }
        $rules['mobile_no'][2] = new ValidMobileNumber;
        return $rules;
    }
}
