<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\BankService;

class BankController extends Controller
{
    private $bank_service = null;

    public function __construct(BankService $bank_service)
    {
        $this->bank_service = $bank_service;
    }
    public function useBankLists()
    {
        $result = $this->bank_service->useBanks();
        return $result;
    }
}
