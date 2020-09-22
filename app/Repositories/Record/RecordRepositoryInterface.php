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
}
