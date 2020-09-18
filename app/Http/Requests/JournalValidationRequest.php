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

    private $accountSubjects = null;

    public function __construct()
    {
        $this->accountSubjects = new AccountSubjects;
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
            'items.0.*.account_subject_id' => 'required',
            'items.0.*.summary' => 'required',
            'items.0.*.amount' => 'required',
            'items.*.debit.amount' => 'not_zero',
            'items.*.credit.amount' => 'not_zero'
        ];

        return $rules;
    }

    public function attributes()
    {
        return [
            'account_date' => '会計日',
            'items.0.debit.account_subject_id' => '借方の会計科目',
            'items.0.credit.account_subject_id' => '借方の会計科目',
            'items.0.debit.summary' => '借方の摘要',
            'items.0.credit.summary' => '貸方の摘要',
            'items.0.debit.amount' => '借方の金額',
            'items.0.credit.amount' => '貸方の金額',
            'items.*.debit.amount' => '借方の金額',
            'items.*.credit.amount' => '貸方の金額',
            'items.0.debit.add_info_id' => '借方の取引銀行または取引先',
            'items.0.credit.add_info_id' => '借方の取引銀行または取引先'
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
        //if ($validator->fails()) return;
        $validator->after(function ($validator) {
            $depositAmount = 0;
            $creditAmount = 0;
            foreach ($this->input('items') as $journal_data) {
                // 借方と貸方のそれぞれの金額の合計値が等しいかの確認
                if (!empty($journal_data['debit']['amount'])) {
                    $depositAmount = $depositAmount + $journal_data['debit']['amount'];
                }
                if (!empty($journal_data['credit']['amount'])) {
                    $creditAmount = $creditAmount + $journal_data['credit']['amount'];
                }
            }
            if ($creditAmount !== $depositAmount) {
                $validator->errors()->add('amount_check', '借方と貸方の金額が異なっています');
            };
        });
        $validator->sometimes('items.0.debit.add_info_id', 'required', function ($formData) {
            $result = false;
            foreach ($formData->items as $journal_data) {
                if (in_array($journal_data['debit']['account_subject_id'], $this->accountSubjects->addInfoGroup)) {
                    $result = true;
                }
            }
            return $result;
        });
        $validator->sometimes('items.0.credit.add_info_id', 'required', function ($formData) {
            $result = false;
            foreach ($formData->items as $journal_data) {
                if (in_array($journal_data['credit']['account_subject_id'], $this->accountSubjects->addInfoGroup)) {
                    $result = true;
                }
            }
            return $result;
        });
        $validator->after(function ($validator) {
            foreach ($this->items as $key => $journal_data) {
                if ($key > 0) {
                    if (
                        !empty($journal_data['credit']['account_subject_id'])
                        || !empty($journal_data['credit']['summary'])
                        || !empty($journal_data['credit']['amount'])
                    ) {
                        $validator->errors()->add('add_table_credit', '追加したテーブルの貸方に未入力項目があります');
                    }
                }
            }
        });
        $validator->after(function ($validator) {
            foreach ($this->items as $key => $journal_data) {
                if ($key > 0) {
                    if (
                        !empty($journal_data['debit']['account_subject_id'])
                        || !empty($journal_data['debit']['summary'])
                        || !empty($journal_data['debit']['amount'])
                    ) {
                        $validator->errors()->add('add_table_debit', '追加したテーブルの借方に未入力項目があります');
                    }
                }
            }
        });
    }
}
