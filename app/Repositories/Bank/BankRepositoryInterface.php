<?php

namespace App\Repositories\Bank;

interface BankRepositoryInterface
{
    /**
     * ユーザが使用している銀行リストを取得
     * 
     * @var integer $id
     * @return array  
     */
    public function getUseBanks($id);
}
