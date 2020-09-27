<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\UseAccountsSubject;
use App\Models\AccountsReceivableBook;
use App\Models\AccountsPayableBook;
use App\Models\ExpenseBook;
use App\Models\DepositAccountBook;
use App\Services\RecordService;
use App\Http\Requests\JournalValidationRequest;

class RecordController extends Controller
{
    private $record_service = null;

    public function __construct(RecordService $record_service)
    {
        $this->record_service = $record_service;
    }

    public function recordJournal(Request $request)
    {
        $result = $this->record_service->journal($request);
        return $result;
    }
}
