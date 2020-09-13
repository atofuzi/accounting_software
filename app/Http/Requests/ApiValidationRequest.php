<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

trait ApiValidationRequest
{
    protected function failedValidation(Validator $validator)
    {
        $errors = [];

        $errorItems = $validator->errors()->toArray();
        foreach($errorItems as $key => $value){
            $errors[] = ['code' => $key, 'message' => $value[0]];
        }

        $response = [
            'status' => 0,
            'errors' => $errors,
            'result' => []
        ];
        throw new HttpResponseException(response()->json($response, 422));
    }
}
