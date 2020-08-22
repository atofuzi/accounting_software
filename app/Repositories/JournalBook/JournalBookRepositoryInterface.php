<?php 

namespace App\Repositories\JournalBook;

interface JournalBookRepositoryInterface
{
    /**
     * 仕訳帳データ取得
     * 
     * @var array $params
     * @return array 
     */
    public function getList($params);
}