<?php

namespace App\Services;

use App\Repositories\AccountSubjects\AccountSubjectsRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class AccountSubjectsService
{
    private $account_subjects;

    public function __construct(AccountSubjectsRepositoryInterface $account_subjects)
    {
        $this->account_subjects = $account_subjects;
    }
    /**
     * ユーザが使用している会計科目を取得
     * 
     * @var integer $id
     * @return array
     */
    public function useAccountSubjects()
    {
        $user_id = Auth::id();
        $result = $this->account_subjects->getUseAccountSubjects($user_id);
        return $result;
    }
}
