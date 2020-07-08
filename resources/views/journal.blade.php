@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    <form method="post" action="">
                    @csrf
                        <table class="table table-striped">
                            <thead>
                                <th>
                                    {{ __('Debit') }}
                            </th>
                            <th></th>
                            <th>
                                    {{ __('Credit') }}
                            </th>
                            <th></th>
                            </thead>
                            <tbody>
                                <tr>
                                    <th>{{ __('Account_date') }}</th>
                                    <td><input type="date" name="account_date"></td>
                                    <th></th>
                                    <td></td>
                                </tr>
                                <tr>
                                    <th>{{ __('Account_subject') }}</th>
                                    <td>
                                        <select name="debit_account_subject_id">
                                            <option value="0"></option>
                                            <option value="1">事業主貸</option>
                                            <option value="2">普通預金</option>
                                            <option value="3">未払金</option>
                                            <option value="4">売掛金</option>
                                        </select>
                                    </td>
                                    <th>{{ __('Account_subject') }}</th>
                                    <td>
                                        <select name="credit_account_subject_id">
                                            <option value="0"></option>
                                            <option value="1">事業主貸</option>
                                            <option value="2">普通預金</option>
                                            <option value="3">未払金</option>
                                            <option value="4">売掛金</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <th>{{ __('Summary') }}</th>
                                    <td><input type="text" name="debit_summary"></td>
                                    <th>{{ __('Summary') }}</th>
                                    <td><input type="text" name="credit_summary"></td>
                                </tr>
                                <tr>
                                    <th>{{ __('Amount') }}</th>
                                    <td><input type="text" name="debit_amount"></td>
                                    <th>{{ __('Amount') }}</th>
                                    <td><input type="text" name="credit_amount"></td>
                                </tr>
                                <tr>
                                    <th>{{ __('Gentian_number') }}</th>
                                    <td><input type="text" name="debit_gentian_number"></td>
                                    <th>{{ __('Gentian_number') }}</th>
                                    <td><input type="text" name="credit_gentian_number"></td>
                                </tr>
                            </tbody>
                        </table>
                        <input type="submit" class="btn btn-primary" value="登録">
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection