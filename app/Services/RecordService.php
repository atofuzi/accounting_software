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

        $data['total_balance'] = $data['last_balance'];
        foreach ($data['items'] as $key => $cash_record) {
            if ($cash_record['journal_type'] === AccountSubjects::TYPE_DEBIT) {
                $data['items'][$key]['balance'] =  $data['total_balance'] + $cash_record['amount'];
            } else if ($cash_record['journal_type'] === AccountSubjects::TYPE_CREDIT) {
                $data['items'][$key]['balance'] =  $data['total_balance'] - $cash_record['amount'];
            }
            $data['total_balance'] = $data['items'][$key]['balance'];
        }
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
        if(!empty($data)){
            $data = $this->getBalanceAndTotalBalance($data);
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
        // total_balanceとレコード毎のbalanceを計算する;
        if(!empty($data)){
            $data = $this->getBalanceAndTotalBalance($data);
        }
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
        if(!empty($data)){
            $data = $this->getBalanceAndTotalBalance($data);
        }
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
        // total_balanceとレコード毎のbalanceを計算する;
        if(!empty($data)){
            $data = $this->getBalanceAndTotalBalance($data);
        }
        return $data;
    }

    /**
     * 総勘定元帳（資産）データ一覧取得
     * 
     * @param 
     * @return array
     */
    public function getTotalAssetsRecord($params)
    {
        $data = $this->record->getTotalAssetsRecord($params);
        // total_balanceとレコード毎のbalanceを計算する;
        if(!empty($data)){
            $data = $this->getBalanceAndTotalBalance($data);
        }
        return $data;
    }


    /**
     * 総勘定元帳（負債・資本）データ一覧取得
     * 
     * @param 
     * @return array
     */
    public function getTotalLiabilitiesAndCapitalRecord($params)
    {
        $data = $this->record->getTotalLiabilitiesAndCapitalRecord($params);
        // total_balanceとレコード毎のbalanceを計算する;
        if(!empty($data)){
            $data = $this->getBalanceAndTotalBalance($data, 'credit');
        }
        return $data;
    }


    /**
     * 総勘定元帳（経費）データ一覧取得
     * 
     * @param 
     * @return array
     */
    public function getTotalCostRecord($params)
    {
        $data = $this->record->getTotalCostRecord($params);
        // total_balanceとレコード毎のbalanceを計算する;
        if(!empty($data)){
            $data = $this->getBalanceAndTotalBalance($data);
        }
        return $data;
    }

    /**
     * 総勘定元帳（売上）データ一覧取得
     * 
     * @param 
     * @return array
     */
    public function getTotalEarningsRecord($params)
    {
        $data = $this->record->getTotalEarningsRecord($params);
        // total_balanceとレコード毎のbalanceを計算する;
        if(!empty($data)){
            $data = $this->getBalanceAndTotalBalance($data, 'credit');
        }
        return $data;
    }


    public function getBalanceAndTotalBalance($data, $asset_type = null){
        if (empty($asset_type)) { 
            foreach ($data as $key => $record) {
                $data[$key]['total_balance'] = $record['last_balance'];
                foreach ($record['items'] as $index => $item) {
                    if ($item['journal_type'] === AccountSubjects::TYPE_DEBIT) {
                        $data[$key]['items'][$index]['balance'] = $data[$key]['total_balance'] + $item['amount'];
                    } else {
                        $data[$key]['items'][$index]['balance'] = $data[$key]['total_balance'] - $item['amount'];
                    }
                    $data[$key]['total_balance'] =  $data[$key]['items'][$index]['balance'];
                }
            }
        } else {
            foreach ($data as $key => $record) {
                $data[$key]['total_balance'] = $record['last_balance'];
                foreach ($record['items'] as $index => $item) {
                    if ($item['journal_type'] === AccountSubjects::TYPE_CREDIT) {
                        $data[$key]['items'][$index]['balance'] = $data[$key]['total_balance'] + $item['amount'];
                    } else {
                        $data[$key]['items'][$index]['balance'] = $data[$key]['total_balance'] - $item['amount'];
                    }
                    $data[$key]['total_balance'] =  $data[$key]['items'][$index]['balance'];
                }
            }
        }
        
        return $data;
    }
}
