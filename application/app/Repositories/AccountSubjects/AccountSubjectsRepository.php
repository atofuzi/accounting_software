<?php

namespace App\Repositories\AccountSubjects;

use App\Models\UseAccountSubject;

class AccountSubjectsRepository implements AccountSubjectsRepositoryInterface
{
    protected $use_account_subjects;

    public function __construct(UseAccountSubject $use_account_subjects)
    {
        $this->use_account_subjects = $use_account_subjects;
    }

    public function getUseAccountSubjects($id)
    {
        $column = [
            'account_subject_id',
            'account_subjects.account_subject'
        ];
        $result = $this->use_account_subjects
            ->select($column)
            ->join('account_subjects', 'account_subject_id', 'account_subjects.id')
            ->where('user_id', $id)
            ->get();

        return $result;
    }
}
