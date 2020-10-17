<?php

namespace App\Repositories\Record;

interface RecordRepositoryInterface
{
    /**
     * 仕訳帳データ取得
     * 
     * @var array $params
     * @return array 
     */
    public function getJournalRecord($params);

    /**
     * 現金出納帳データ取得
     * 
     * @var array $params
     * @return array 
     */
    public function getCashRecord($params);

    /**
     * 預金出納帳データ取得
     * 
     * @var array $params
     * @return array 
     */
    public function getDepositRecord($params);

    /**
     * 売掛帳データ取得
     * 
     * @var array $params
     * @return array 
     */
    public function getReceivableRecord($params);
    /**
     * 買掛帳データ取得
     * 
     * @var array $params
     * @return array 
     */
    public function getPayableRecord($params);

    /**
     * 経費帳データ取得
     * 
     * @var array $params
     * @return array 
     */
    public function getExpensesRecord($params);

    /**
     * 総勘定元帳（資産）データ取得
     * 
     * @var array $params
     * @return array 
     */
    public function getTotalAssetsRecord($params);

    /**
     * 総勘定元帳（負債・資本）データ取得
     * 
     * @var array $params
     * @return array 
     */
    public function getTotalLiabilitiesAndCapitalRecord($params);

    /**
     * 総勘定元帳（経費）データ取得
     * 
     * @var array $params
     * @return array 
     */
    public function getTotalExpensesRecord($params);

    /**
     * 総勘定元帳（売上）データ取得
     * 
     * @var array $params
     * @return array 
     */
    public function getTotalEarningsRecord($params);

}
