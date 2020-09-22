<?php

namespace App\Repositories\AccountSubjects;

interface AccountSubjectsRepositoryInterface
{
    /**
     * ユーザが使用している会計科目を取得
     * 
     * @var integer $id
     * @return array
     */
    public function getUseAccountSubjects($id);
}
