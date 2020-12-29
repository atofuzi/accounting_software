<?php

namespace App\Http\Controllers;

use App\Http\Requests\JournalValidationRequest;
use Illuminate\Http\Request;
use App\Services\JournalRegisterService;

class JournalRegisterController extends Controller
{
    private $journal_register_service = null;

    public function __construct(journalRegisterService $journal_register_service)
    {
        $this->journal_register_service = $journal_register_service;
    }

    public function journalRegister(JournalValidationRequest $request)
    {
        $result = $this->journal_register_service->journalRegister($request);
        return $result;
    }
    public function journalEdit(Request $request)
    {
        $result = $this->journal_register_service->journalEdit($request);
        return $result;
    }
    public function journalUpdate(JournalValidationRequest $request)
    {
        $result = $this->journal_register_service->journalUpdate($request);
        return $result;
    }
    public function journalDelete(Request $request)
    {
        $result = $this->journal_register_service->journalDelete($request);
        return $result;
    }
}
