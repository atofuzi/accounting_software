<?php

namespace App\Enums;

class AccountSubjects
{
    /**
     * 会計科目関係の定数
     */
    const NORMAL_DEPOSIT = 2;
    const CURRENT_ACCOUNT = 3;
    const TIME_DEPOSIT = 4;
    const OTHER_DEPOSIT = 5;
    const BILLS_RECEIVABLE = 6;
    const ACCOUNTS_RECEIVABLE = 7;
    const BILLS_PAYABLE = 19;
    const ACCOUNTS_PAYABLE = 20;

    public $addInfoGroup = [
        self::NORMAL_DEPOSIT,
        self::CURRENT_ACCOUNT,
        self::TIME_DEPOSIT,
        self::OTHER_DEPOSIT,
        self::BILLS_RECEIVABLE,
        self::ACCOUNTS_RECEIVABLE,
        self::BILLS_PAYABLE,
        self::ACCOUNTS_PAYABLE,
    ];
}
