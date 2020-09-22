<?php

namespace App\Services;

use App\Repositories\JournalRegister\JournalRegisterRepositoryInterface;
use App\Enums\AccountSubjects;
use Facade\FlareClient\Enums\MessageLevels;
use Illuminate\Support\Facades\Auth;

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
        $data = [];
        $data['unit_number'] = $result[0]->unit_number;
        $data['account_date'] = $result[0]->account_date;
        $data['ids'] = [];

        $debit_item_count = 0;
        $credit_item_count = 0;

        foreach ($result as $journal) {
            if ($journal->journal_type === AccountSubjects::TYPE_DEBIT) {
                array_push($data['ids'], $journal->id);
                $data['debit'][$debit_item_count]['account_subject_id'] = $journal->account_subject_id;
                $data['debit'][$debit_item_count]['summary'] = $journal->summary;
                $data['debit'][$debit_item_count]['amount'] = $journal->amount;
                $data['debit'][$debit_item_count]['add_info_id'] = "";

                if ($journal->bank_id !== null) {
                    $data['debit'][$debit_item_count]['add_info_id'] = $journal->bank_id;
                }
                if ($journal->supplier_id !== null) {
                    $data['debit'][$debit_item_count]['add_info_id'] = $journal->supplier_id;
                }
                $debit_item_count++;
            } else {
                array_push($data['ids'], $journal->id);
                $data['credit'][$credit_item_count]['id'] = $journal->id;
                $data['credit'][$credit_item_count]['account_subject_id'] = $journal->account_subject_id;
                $data['credit'][$credit_item_count]['summary'] = $journal->summary;
                $data['credit'][$credit_item_count]['amount'] = $journal->amount;
                $data['credit'][$credit_item_count]['add_info_id'] = "";

                if ($journal->bank_id !== null) {
                    $data['credit'][$credit_item_count]['add_info_id'] = $journal->bank_id;
                }
                if ($journal->supplier_id !== null) {
                    $data['credit'][$credit_item_count]['add_info_id'] = $journal->supplier_id;
                }
                $credit_item_count++;
            }
        }
        return $data;
    }

    public function journalUpdate($request)
    {
        foreach ($request->ids as $id) {
            $this->journal_register->deleteJournals($id);
        }
        $this->journalRegister($request);
    }

    public function journalDelete($request)
    {
        foreach ($request->ids as $id) {
            $this->journal_register->deleteJournals($id);
        }
    }
}
