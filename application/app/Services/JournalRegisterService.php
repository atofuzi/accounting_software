<?php

namespace App\Services;

use App\Repositories\JournalRegister\JournalRegisterRepositoryInterface;
use App\Enums\AccountSubjects;
use Facade\FlareClient\Enums\MessageLevels;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Exceptions\BaseErrorResponseException;
use App\Enums\Error;
use Illuminate\Http\Exceptions\HttpResponseException;

class JournalRegisterService
{

    private $journal_register = null;

    public function __construct(JournalRegisterRepositoryInterface $journal_register)
    {
        $this->journal_register = $journal_register;
    }
    /**
     * 仕訳帳データ一覧取得
     * 
     * @param 
     * @return array
     */
    public function journalRegister($request)
    {
        $user_id = Auth::id();

        $debit_items = $request->debit;
        $credit_items = $request->credit;

        // 登録データが同一会計単位であるこをを識別するユニークid
        $unit_number = uniqid();

        // 会計データ数で処理を分岐
        // データの少ない方からテーブルに格納する

        DB::beginTransaction();

        try {
            if (count($debit_items) <= count($credit_items)) {
                foreach ($debit_items as $debit_item) {
                    $debit_item['account_date'] = $request->account_date;
                    $debit_item['unit_number'] = $unit_number;
                    $debit_item['journal_type'] = AccountSubjects::TYPE_DEBIT; // debit:0
                    $this->registerProcessRun($debit_item, $user_id);
                }
                foreach ($credit_items as $credit_item) {
                    $credit_item['account_date'] = $request->account_date;
                    $credit_item['unit_number'] = $unit_number;
                    $credit_item['journal_type'] = AccountSubjects::TYPE_CREDIT; // credit:1
                    $this->registerProcessRun($credit_item, $user_id);
                }
            } else {
                foreach ($credit_items as $credit_item) {
                    $credit_item['account_date'] = $request->account_date;
                    $credit_item['unit_number'] = $unit_number;
                    $credit_item['journal_type'] = AccountSubjects::TYPE_CREDIT; // credit:1
                    $this->registerProcessRun($credit_item, $user_id);
                }
                foreach ($debit_items as $debit_item) {
                    $debit_item['account_date'] = $request->account_date;
                    $debit_item['unit_number'] = $unit_number;
                    $debit_item['journal_type'] = AccountSubjects::TYPE_DEBIT; // debit:0
                    $this->registerProcessRun($debit_item, $user_id);
                }
            }
        } catch (\Exception $e) {
            DB::rollBack();
            throw new HttpResponseException(response()->json(["code" => 500, 'message' => $e->getMessage()], 500));
        }

        DB::commit();
    }

    public function registerProcessRun($params, $user_id)
    {
        $params['user_id'] = $user_id;
        // 仕訳データ登録
        $journal_id = $this->journal_register->insertJournals($params);

        // 仕訳データ登録と同時に預金元帳、売掛金元帳、経費元帳、買掛金元帳、現金元帳を作成

        $accountSubjectId = $params['account_subject_id'];

        // 預金判定：account_subject_id 2〜5
        if (in_array($accountSubjectId, AccountSubjects::DEPOSIT_GROUP)) {
            // 預金元帳を作成する
            $this->journal_register->insertDepositAccountBooks($params, $journal_id);
        }

        // 売掛金判定：account_subject_id 7
        if ($accountSubjectId === AccountSubjects::ACCOUNTS_RECEIVABLE) {
            // 売掛金元帳を作成する
            $this->journal_register->insertAccountsReceivableBooks($params, $journal_id);
        }

        // 買掛金判定：account_subject_id 20
        if ($accountSubjectId === AccountSubjects::ACCOUNTS_PAYABLE) {
            // 買掛金元帳を作成する
            $this->journal_register->insertAccountsPayableBooks($params, $journal_id);
        }

        // 経費判定：account_subject_id 32〜52
        if ($accountSubjectId >= 32 && $accountSubjectId <= 52) {
            // 経費元帳を作成する
            $this->journal_register->insertExpenseBooks($params, $journal_id);
        }
    }
    public function journalEdit($request)
    {
        $user_id = Auth::id();
        $result = $this->journal_register->getJournalUnit($request, $user_id);

        // フロント側で使用するようにデータをフォーマット
        $format_data = [
            'unit_number' => '',
            'account_date' => '',
            'ids' => [],
            'debit' => [],
            'credit' =>  []
        ];
        $item_format = [
            'account_subject_id' => '',
            'summary' => '',
            'amount' => '',
            'add_info_id' => '',
        ];

        $format_data['unit_number'] = $result[0]->unit_number;
        $format_data['account_date'] = $result[0]->account_date;

        $debit_item_count = 0;
        $credit_item_count = 0;

        foreach ($result as $key => $journal) {
            // データの格納処理の回数が奇数回の場合、debitとcreditにitem_formatを追加する
            if (($key + 1) % 2 !== 0) {
                array_push($format_data['debit'], $item_format);
                array_push($format_data['credit'], $item_format);
            }
            if ($journal->journal_type === AccountSubjects::TYPE_DEBIT) {
                array_push($format_data['ids'], $journal->id);
                $format_data['debit'][$debit_item_count]['account_subject_id'] = $journal->account_subject_id;
                $format_data['debit'][$debit_item_count]['summary'] = $journal->summary;
                $format_data['debit'][$debit_item_count]['amount'] = $journal->amount;
                $format_data['debit'][$debit_item_count]['add_info_id'] = "";

                if ($journal->bank_id !== null) {
                    $format_data['debit'][$debit_item_count]['add_info_id'] = $journal->bank_id;
                }
                if ($journal->supplier_id !== null) {
                    $format_data['debit'][$debit_item_count]['add_info_id'] = $journal->supplier_id;
                }
                $debit_item_count++;
            } else {
                array_push($format_data['ids'], $journal->id);
                $format_data['credit'][$credit_item_count]['id'] = $journal->id;
                $format_data['credit'][$credit_item_count]['account_subject_id'] = $journal->account_subject_id;
                $format_data['credit'][$credit_item_count]['summary'] = $journal->summary;
                $format_data['credit'][$credit_item_count]['amount'] = $journal->amount;
                $format_data['credit'][$credit_item_count]['add_info_id'] = "";

                if ($journal->bank_id !== null) {
                    $format_data['credit'][$credit_item_count]['add_info_id'] = $journal->bank_id;
                }
                if ($journal->supplier_id !== null) {
                    $format_data['credit'][$credit_item_count]['add_info_id'] = $journal->supplier_id;
                }
                $credit_item_count++;
            }
        }
        return $format_data;
    }

    public function journalUpdate($request)
    {
        DB::beginTransaction();
        try {
            foreach ($request->ids as $id) {
                $this->journal_register->deleteJournals($id);
            }
            $this->journalRegister($request);
        } catch (\Exception $e) {
            DB::rollBack();
            throw new HttpResponseException(response()->json(["code" => Error::STOP_REGISTER_JOURNAL, 'message' => Error::getMessage(Error::STOP_REGISTER_JOURNAL)], 500));
        }

        DB::commit();
    }

    public function journalDelete($request)
    {
        foreach ($request->ids as $id) {
            $this->journal_register->deleteJournals($id);
        }
    }
}
