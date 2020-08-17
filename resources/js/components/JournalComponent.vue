<template>
  <div class="container">
      <div class="col-md-8">
      <button v-on:click="add" class="btn btn-info my-3">テーブル追加</button> 
        <JournalTableComponent 
            v-for="table in journalTables"
            :journalData="data[table.id]"
            :journalSubjects="journalSubjects"
            :gentians="gentians"
            :banks="banks"
            :suppliers="suppliers"
            :key="table.id"
            :count="table.id"
            v-on:change="updateJournalData(table.id,$event)"
        >
        </JournalTableComponent>
          <button class="btn btn-outline-info">データを登録</button> 
      </div>
  </div>
</template>

<script>
import JournalTableComponent from './parts/JournalTableComponent'
export default {
    data(){
      return{
        data:[
            {
              debit:{
                accountDate : "",
                accountSubjectId : "",
                amount : "",
                summary : "",
                addInfoId : "",
              },
              credit:{
                accountDate : "",
                accountSubjectId : "",
                amount : "",
                summary : "",
                addInfoId: ""
              }
            }
        ],
        journalTables:[
            { id:0 },
        ],
        nextTableId:1,
        journalSubjects:[],
        gentians:[],
        banks:[],
        suppliers:[]
      }
    },
    components:{
      JournalTableComponent
    },
    created: async function() {
      console.log('ユーザー情報取得');
      
      const user = await axios.get('http://localhost:8888/accounting_software/public/api/user')
      .catch(error => {
        console.log('ユーザー情報取得失敗');
      })

      console.log('会計科目情報取得');
      await axios.get('http://localhost:8888/accounting_software/public/api/use_account_subjects/' + user.data.id )
      .then(response => {
          console.log(response.data);
          this.journalSubjects = response.data;
      })
      .catch(error => {
        console.log('会計科目情報取得失敗');
      });
      
      console.log('銀行リスト取得');
      await axios.get('http://localhost:8888/accounting_software/public/api/bank_lists')
      .then(response => {
          console.log(response.data);
          this.banks = response.data;
      })
      .catch(error => {
        console.log('銀行リスト取得失敗');
      });
      
      console.log('取引先リスト取得');
      await axios.get('http://localhost:8888/accounting_software/public/api/supplier_lists')
      .then(response => {
          console.log(response.data);
          this.suppliers = response.data;
      })
      .catch(error => {
        console.log('銀行リスト取得失敗');
      });
    },
    methods:{
      add: function(){
        this.journalTables.push({ id: this.nextTableId })
        this.data.push(
              {
                debit:{
                  accountDate: "",
                  accountSubjectId: "",
                  amount: "",
                  summary: "",
                  addInfoId: "",
                },
                credit:{
                  accountDate: "",
                  accountSubjectId: "",
                  amount: "",
                  summary: "",
                  addInfoId: ""
              }
            })
        this.nextTableId = this.nextTableId + 1
      },
      updateJournalData:function(id,inputData){
        if(inputData.key === "amount"){
          this.data[id][inputData.type][inputData.key] = Number(inputData.value)
        }else{
          this.data[id][inputData.type][inputData.key] = inputData.value
        }
        if(inputData.key === "accountDate"){
          this.data[id]['credit'][inputData.key] = inputData.value
        }
      }
    }
}
</script>