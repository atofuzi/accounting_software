import axios from "../utils/api.js";

// 仕訳帳登録画面
// 利用会計科目データの取得
export function getAccountSubjects() {
    return axios.get('use_account_subjects');
}

// 利用銀行リストの取得
export function getBankLists() {
    return axios.get('bank_lists');
}

// 取引先リストの取得
export function getSupplierLists() {
    return axios.get('supplier_lists');
}

// 仕訳データのデータの登録
export function registerJournal(requestData) {
    return axios.post('journal_register', requestData);
}

/* // 仕訳データのデータの登録
export function registerJournal(requestData){
    const items = requestData.items;
    Object.keys(items).forEach(function(index){
        Object.keys(items[index]).forEach(function(key){
            Object.keys(items[index][key]).forEach(function(value){
                // 空の場合はリクエストパラメータから削除する
                if(!items[index][key][value]){
                    delete items[index][key][value];
                }
            })
        })
    });
    return  axios.post('journal_register', requestData);
} */

