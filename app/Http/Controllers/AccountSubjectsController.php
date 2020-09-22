<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AccountSubjectsService;

class AccountSubjectsController extends Controller
{
    protected $account_subjects_service;

    public function __construct(AccountSubjectsService $account_subjects_service)
    {
        $this->account_subjects_service = $account_subjects_service;
    }
    public function useAccountSubjects()
    {
        $result = $this->account_subjects_service->useAccountSubjects();
        return $result;
    }
}
