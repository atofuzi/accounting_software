<?php

namespace App\Repositories\Bank;

use App\Models\UseBank;

class BankRepository implements BankRepositoryInterface
{
    protected $use_bank;

    /**
     * @var model $use_bank
     */
    public function __construct(UseBank $use_bank)
    {
        $this->use_bank = $use_bank;
    }
    public function getUseBanks($id)
    {
        $result = $this->use_bank
            ->select('banks.id', 'bank_name AS name')
            ->join('banks', 'use_banks.bank_id', '=', 'banks.id')
            ->where('use_banks.user_id', $id)
            ->get();

        return $result;
    }
}
