<?php

namespace App\Repositories\JournalRegister;

interface JournalRegisterRepositoryInterface
{
    /**
     * 仕訳帳データ登録
     * 
     * @var array $params
     * @return integer $id
     */
    public function insertJournals($params);

    /**
     * 預金元帳登録
     * 
     * @var array $params
     * @var integer $id
     * @var integer $userId
     * @return void
     * 
     *  */
    public function insertDepositAccountBooks($params, $id);

    /**
     * 売掛金元帳を作成する
     * 
     * @var array $params
     * @var integer $id
     * @var integer $userId
     * @return void
     * 
     *  */

    public function insertAccountsReceivableBooks($params, $id);

    /**
     *  買掛金元帳を作成する
     * 
     * @var array $params
     * @var integer $id
     * @var integer $userId
     * @return void
     * 
     *  */
    public function insertAccountsPayableBooks($params, $id);

    /**
     *  経費帳を作成する
     * 
     * @var array $params
     * @var integer $id
     * @var integer $userId
     * @return void
     * 
     *  */
    public function insertExpenseBooks($params, $id);

    /**
     * 編集する仕訳データを仕訳単位で取得する
     * 
     * @var array $params
     * @var integer $id
     * @return array 
     */
    public function getJournalUnit($params, $id);

    /**
     * 仕訳データを削除する
     * 
     * @var integer $id
     * @return array 
     */
    public function deleteJournals($id);
}
