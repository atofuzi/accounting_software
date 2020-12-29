<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use App\Repositories\Bank\BankRepositoryInterface;


class BankService
{
    private $bank;

    public function __construct(BankRepositoryInterface $bank)
    {
        $this->bank = $bank;
    }

    public function useBanks()
    {
        $user_id = Auth::id();
        $result = $this->bank->getUseBanks($user_id);
        return $result;
    }
}
