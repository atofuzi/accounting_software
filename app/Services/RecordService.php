<?php

namespace App\Services;

use App\Repositories\Record\RecordRepositoryInterface;

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
}
