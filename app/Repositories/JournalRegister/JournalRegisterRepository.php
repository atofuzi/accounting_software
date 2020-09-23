<?php

namespace App\Repositories\JournalRegister;

use App\Models\Journal;
use App\Models\AccountsReceivableBook;
use App\Models\AccountsPayableBook;
use App\Models\ExpenseBook;
use App\Models\DepositAccountBook;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class JournalRegisterRepository implements JournalRegisterRepositoryInterface
{
    protected $journal;
    protected $deposit_book;
    protected $accounts_receivable_book;
    protected $accounts_payable_book;
    protected $expense_book;
    /**
     * 
     * @param model $journal, deposit_book
     * @param model $accounts_receivable_book, $accounts_payable_book
     * @param model $expense_book
     */
    public function __construct(
        Journal $journal,
        DepositAccountBook $deposit_book,
        AccountsReceivableBook $accounts_receivable_book,
        AccountsPayableBook $accounts_payable_book,
        ExpenseBook $expense_book
    ) {
        $this->journal = $journal;
        $this->deposit_book = $deposit_book;
        $this->accounts_receivable_book = $accounts_receivable_book;
        $this->accounts_payable_book = $accounts_payable_book;
        $this->expense_book = $expense_book;
        $this->user_id = Auth::id();
    }

    public function insertJournals($params)
    {
        $journal_id = $this->journal
            ->insertGetId([
                'unit_number' => $params['unit_number'],
                'user_id' => $params['user_id'],
                'account_date' => $params['account_date'],
                'account_subject_id' => $params['account_subject_id'],
                'summary' => $params['summary'],
                'amount' => $params['amount'],
                'journal_type' => $params['journal_type'],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        return $journal_id;
    }

    public function insertDepositAccountBooks($params, $id)
    {
        $bank_id = !empty($params['add_info_id']) ? $params['add_info_id'] : 1;
        $this->deposit_book->insert([
            'user_id' => $params['user_id'],
            'journal_id' => $id,
            'bank_id' => $bank_id,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
    }

    public function insertAccountsReceivableBooks($params, $id)
    {
        $supplier_id = !empty($params['add_info_id']) ? $params['add_info_id'] : 1;
        $this->accounts_receivable_book->insert([
            'user_id' => $params['user_id'],
            'journal_id' => $id,
            'supplier_id' => $supplier_id,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
    }

    public function insertAccountsPayableBooks($params, $id)
    {
        $supplier_id = !empty($params['add_info_id']) ? $params['add_info_id'] : 1;
        $this->accounts_payable_book->insert([
            'user_id' => $params['user_id'],
            'journal_id' => $id,
            'supplier_id' => $supplier_id,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
    }

    public function insertExpenseBooks($params, $id)
    {
        $this->expenseBook->insert([
            'user_id' => $params['user_id'],
            'journal_id' => $id,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
    }

    public function getJournalUnit($params, $id)
    {
        // 会計日・摘要（科目名+コメント）・仕訳タイプ・金額
        $column = [
            'journals.id',
            'journals.unit_number',
            'journals.account_date',
            'journals.account_subject_id',
            'journals.summary',
            'journals.journal_type',
            'journals.amount',
            'deposit_account_books.bank_id',
            'accounts_payable_books.supplier_id',
            'accounts_receivable_books.supplier_id',
        ];

        $result = $this->journal
            ->select($column)
            ->leftJoin('deposit_account_books', 'journals.id', '=', 'deposit_account_books.journal_id')
            ->leftJoin('accounts_payable_books', 'journals.id', '=', 'accounts_payable_books.journal_id')
            ->leftJoin('accounts_receivable_books', 'journals.id', '=', 'accounts_receivable_books.journal_id')
            ->where('journals.user_id', '=', $id)
            ->where('unit_number', '=', $params['unit_number'])
            ->orderBy('journals.id', 'asc')
            ->get();

        return $result;
    }

    public function deleteJournals($id)
    {
        $this->journal->where('id', $id)->delete();
        $this->deposit_book->where('journal_id', $id)->delete();
        $this->accounts_receivable_book->where('journal_id', $id)->delete();
        $this->accounts_payable_book->where('journal_id', $id)->delete();
        $this->expense_book->where('journal_id', $id)->delete();
    }
}
