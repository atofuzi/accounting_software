<template>
  <div class="container">
    <div class="col-md-8">
      <button v-on:click="add" class="btn btn-info my-3">テーブル追加</button>
      <li v-for="error in errors" class="text-danger">{{error.message}}</li>
      <JournalInputComponent
        v-for="table in journalTables"
        :accountDate="data.accountDate"
        :journalData="data.items[table.id]"
        :journalSubjects="journalSubjects"
        :gentians="gentians"
        :banks="banks"
        :suppliers="suppliers"
        :key="table.id"
        :count="table.id"
        v-on:change="updateJournalData(table.id,$event)"
      ></JournalInputComponent>
      <button @click="register()" class="btn btn-outline-info">データを登録</button>
      <button v-if="nextTableId > 1" @click="tableDelete" class="btn btn-outline-info">テーブル削除</button>
    </div>
  </div>
</template>

<script>
import JournalInputComponent from "./parts/JournalInputComponent";
import myAxios from "../utils/api.js";
import {
  getAccountSubjects,
  getBankLists,
  getSupplierLists,
  registerJournal,
} from "../api/journal.js";

export default {
  data() {
    return {
      data: {
        accountDate: "",
        items: [
          {
            debit: {
              accountSubjectId: "",
              amount: "",
              summary: "",
              addInfoId: "",
            },
            credit: {
              accountSubjectId: "",
              amount: "",
              summary: "",
              addInfoId: "",
            },
          },
        ],
      },
      journalTables: [{ id: 0 }],
      nextTableId: 1,
      journalSubjects: [],
      gentians: [],
      banks: [],
      suppliers: [],
      errors: [],
    };
  },
  components: {
    JournalInputComponent,
  },
  created: function () {
    console.log("会計科目情報取得");
    getAccountSubjects()
      .then((response) => {
        console.log(response);
        this.journalSubjects = response.data;
      })
      .catch((error) => {
        console.log("会計科目情報取得失敗");
      });

    console.log("銀行リスト取得");
    getBankLists()
      .then((response) => {
        console.log(response.data);
        this.banks = response.data;
      })
      .catch((error) => {
        console.log("銀行リスト取得失敗");
      });

    console.log("取引先リスト取得");
    getSupplierLists()
      .then((response) => {
        console.log(response.data);
        this.suppliers = response.data;
      })
      .catch((error) => {
        console.log("銀行リスト取得失敗");
      });
  },
  methods: {
    add: function () {
      this.journalTables.push({ id: this.nextTableId });
      this.data.items.push({
        debit: {
          accountSubjectId: "",
          amount: "",
          summary: "",
          addInfoId: "",
        },
        credit: {
          accountSubjectId: "",
          amount: "",
          summary: "",
          addInfoId: "",
        },
      });
      this.nextTableId++;
    },
    tableDelete: function () {
      this.journalTables.pop();
      this.data.items.pop();
      this.nextTableId--;
    },
    updateJournalData: function (index, inputData) {
      if (
        (inputData.key === "amount" ||
          inputData.key === "accountSubjectId" ||
          inputData.key === "addInfoId") &&
        inputData.value !== ""
      ) {
        this.data.items[index][inputData.type][inputData.key] = Number(
          inputData.value
        );
      } else if (inputData.key === "accountDate") {
        this.data.accountDate = inputData.value;
      } else {
        this.data.items[index][inputData.type][inputData.key] = inputData.value;
      }
    },
    register: function () {
      console.log("会計データ登録");

      registerJournal(this.data)
        .then((response) => {
          console.log("会計データ登録成功");
          //location.href = 'http://localhost:8888/accounting_software/public/home';
        })
        .catch((error) => {
          console.log("会計データ登録失敗");
          console.log(error.response.data.errors);
          this.errors = error.response.data.errors;
        });
    },
  },
};
</script>