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

    // 仕訳帳データ取得
    public function recordJournal(Request $request)
    {
        $result = $this->record_service->journal($request);
        return $result;
    }

    // 現金出納帳データ取得
    public function recordCash(Request $request)
    {
        $result = $this->record_service->getCashRecord($request);
        return $result;
    }

    // 預金出納帳データ取得
    public function recordDeposit(Request $request)
    {
        $result = $this->record_service->getDepositRecord($request);
        return $result;
    }

    // 売掛帳データ取得
    public function recordReceivable(Request $request)
    {
        $result = $this->record_service->getReceivableRecord($request);
        return $result;
    }

    // 買掛帳データ取得
    public function recordPayable(Request $request)
    {
        $result = $this->record_service->getPayableRecord($request);
        return $result;
    }

    // 経費帳データ取得
    public function recordExpenses(Request $request)
    {
        $result = $this->record_service->getExpensesRecord($request);
        return $result;
    }

    // 総勘定元帳（資産）データ取得
    public function recordTotalAssets(Request $request)
    {
        $result = $this->record_service->getTotalAssetsRecord($request);
        return $result;
    }

    // 総勘定元帳（負債・資本）データ取得
    public function recordTotalLiabilitiesAndCapital(Request $request)
    {
        $result = $this->record_service->getTotalLiabilitiesAndCapitalRecord($request);
        return $result;
    }

    // 総勘定元帳（経費）データ取得
    public function recordTotalExpenses(Request $request)
    {
        $result = $this->record_service->getTotalExpensesRecord($request);
        return $result;
    }

    // 総勘定元帳（経費）データ取得
    public function recordTotalEarnings(Request $request)
    {
        $result = $this->record_service->getTotalEarningsRecord($request);
        return $result;
    }
}
