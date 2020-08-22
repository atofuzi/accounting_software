<?php

namespace App\Services;

use App\Repositories\JournalBook\JournalBookRepositoryInterface;

class JournalBookService{

    private $journalBook = null;

    public function __construct(JournalBookRepositoryInterface $journalBook)
    {
        $this->journalBook = $journalBook;
    }
    /**
     * 仕訳帳データ一覧取得
     * 
     * @param 
     * @return array
     */
    public function getJournalBookList($params)
    {
        $data = $this->journalBook->getList($params);
        return $data;
    }
}