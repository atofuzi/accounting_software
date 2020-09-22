<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Enums\AccountSubjects;

class JournalValidationRequest extends FormRequest
{
    use ApiValidationRequest;
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
     * Get the validation rules that apply to the request.
     *
     * @return array
     */

    private $account_subjects = null;

    public function __construct()
    {
        $this->account_subjects = new accountSubjects;
    }

    public function rules()
    {
        $rules = [];

        $rules = [
            // 会計日は入力必須
            // 一つ目のテーブルは全て入力必須
            // 取引先・銀行は会計科目により入力必須にする必要がある
            // 金額に0は入力できない
            // 2つ目のテーブルはnullでもOKだが一つでもデータが入っている場合は、入力必須にする必要がある
            'account_date' => 'required',
            'debit.0.account_subject_id' => 'required',
            'credit.0.account_subject_id' => 'required',
            'debit.0.summary' => 'required',
            'credit.0.summary' => 'required',
            'debit.0.amount' => 'required | not_zero',
            'credit.0.amount' => 'required | not_zero',
        ];

        return $rules;
    }

    public function attributes()
    {
        return [
            'account_date' => '会計日',
            'debit.0.account_subject_id' => '借方の会計科目',
            'credit.0.account_subject_id' => '借方の会計科目',
            'debit.0.summary' => '借方の摘要',
            'credit.0.summary' => '貸方の摘要',
            'debit.0.amount' => '借方の金額',
            'credit.0.amount' => '貸方の金額',
            'debit.0.add_info_id' => '借方の取引銀行または取引先',
            'credit.0.add_info_id' => '借方の取引銀行または取引先'
        ];
    }
    public function messages()
    {
        return [
            'required' => ':attributeにデータを入力してください',
            'not_zero' => ':attributeに0は入力できません'
        ];
    }

    public function withValidator(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $validator->sometimes('debit.0.add_info_id', 'required', function ($formData) {
            $result = false;
            if (in_array($formData['debit'][0]['account_subject_id'], $this->account_subjects->add_info_group)) {
                $result = true;
            }
            return $result;
        });
        $validator->sometimes('credit.0.add_info_id', 'required', function ($formData) {
            $result = false;
            if (in_array($formData['credit'][0]['account_subject_id'], $this->account_subjects->add_info_group)) {
                $result = true;
            }
            return $result;
        });

        $validator->after(function ($validator) {
            $count = 0;
            foreach ($this->input('debit') as $key => $debit_data) {
                if ($key > 0) {
                    if (
                        !isset($debit_data['account_subject_id'])
                        || !isset($debit_data['summary'])
                        || !isset($debit_data['amount'])
                    ) {
                        $validator->errors()->add('add_table_debit', '追加したテーブルの借方に未入力項目があります');
                        $count++;
                    } else if (
                        in_array($debit_data['account_subject_id'], $this->account_subjects->add_info_group)
                        && !isset($debit_data['add_info_id'])
                    ) {
                        $validator->errors()->add('add_table_debit', '追加したテーブルの借方に未入力項目があります');
                        $count++;
                    }
                }
            }
            foreach ($this->input('credit') as $key => $credit_data) {
                if ($key > 0) {
                    if (
                        !isset($credit_data['account_subject_id'])
                        || !isset($credit_data['summary'])
                        || !isset($credit_data['amount'])
                    ) {
                        $validator->errors()->add('add_table_credit', '追加したテーブルの貸方に未入力項目があります');
                        $count++;
                    } else if (
                        in_array($credit_data['account_subject_id'], $this->account_subjects->add_info_group)
                        && !isset($credit_data['add_info_id'])
                    ) {
                        $validator->errors()->add('add_table_credit', '追加したテーブルの貸方に未入力項目があります');
                        $count++;
                    }
                }
            }
            if (!$count) {
                $debit_amount = 0;
                $credit_amount = 0;
                foreach ($this->input('debit') as $debit_data) {
                    $debit_amount = $debit_amount + $debit_data['amount'];
                }
                foreach ($this->input('credit') as $debit_data) {
                    $credit_amount = $credit_amount + $debit_data['amount'];
                }
                if ($credit_amount !== $debit_amount) {
                    $validator->errors()->add('amount_check', '借方と貸方の金額が異なっています');
                };
            }
        });
    }
}
