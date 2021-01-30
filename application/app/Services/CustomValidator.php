<?php

namespace App\Services;
use Illuminate\Validation\Validator;

class CustomValidator extends Validator
{
    /** 
     * 仕訳帳専用のバリデーション
    */

    public function validateNotZero($attribute,$value,$parameters){
        return $value > 0;
    }
}