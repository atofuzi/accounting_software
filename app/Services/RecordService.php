<?php

namespace App\Services;

use App\Repositories\Record\RecordRepositoryInterface;
use App\Enums\AccountSubjects;

class RecordService
{

    private $record = null;

    public function __construct(RecordRepositoryInterface $record)
    {
        $this->record = $record;
    }


    /**
     * 仕訳帳データ一覧取得
     * 
     * @param 
     * @return array
     */
    public function journal($params)
    {
        $data = $this->record->getJournalRecord($params);
        return $data;
    }

    /**
     * 現金出納帳データ一覧取得
     * 
     * @param 
     * @return array
     */
    public function getCashRecord($params)
    {
        $data = $this->record->getCashRecord($params);
        return $data;
    }

    /**
     * 預金出納帳データ一覧取得
     * 
     * @param 
     * @return array
     */
    public function getDepositRecord($params)
    {
        $data = $this->record->getDepositRecord($params);
        // total_balanceとレコード毎のbalanceを計算する
        foreach ($data as $key => $deposit_record) {
            $data[$key]['total_balance'] = $deposit_record['last_balance'];
            foreach ($deposit_record['items'] as $index => $item) {
                if ($item['journal_type'] === AccountSubjects::TYPE_DEBIT) {
                    $data[$key]['items'][$index]['balance'] = $data[$key]['total_balance'] + $item['amount'];
                } else {
                    $data[$key]['items'][$index]['balance'] = $data[$key]['total_balance'] - $item['amount'];
                }
                $data[$key]['total_balance'] =  $data[$key]['items'][$index]['balance'];
            }
        }
        return $data;
    }

    /**
     * 売掛帳データ一覧取得
     * 
     * @param 
     * @return array
     */
    public function getReceivableRecord($params)
    {
        $data = $this->record->getReceivableRecord($params);
        return $data;
    }

    /**
     * 買掛帳データ一覧取得
     * 
     * @param 
     * @return array
     */
    public function getPayableRecord($params)
    {
        $data = $this->record->getPayableRecord($params);
        return $data;
    }

    /**
     * 経費帳データ一覧取得
     * 
     * @param 
     * @return array
     */
    public function getExpensesRecord($params)
    {
        $data = $this->record->getExpensesRecord($params);
        return $data;
    }

    /**
     * 総勘定元帳データ一覧取得
     * 
     * @param 
     * @return array
     */
    public function getTotalAccountRecord($params)
    {
        $data = $this->record->getTotalAccountRecord($params);
        return $data;
    }
}
