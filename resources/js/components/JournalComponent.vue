<template>
  <div class="container">
      <div class="col">
      <button v-on:click="add" class="btn btn-info my-3">テーブル追加</button> 
        <JournalTableComponent 
            v-for="table in journalTables"
            :journalData="data[table.id]"
            :journalSubjects="journalSubjects"
            :key="table.id"
            :count="table.id"
            v-on:change="updateJournalData(table.id,$event)"
        >
        </JournalTableComponent>
        <form action="api/journal" method="POST">
          <button class="btn btn-outline-info">データを登録</button> 
        </form>
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
                gentianNumber : ""
              },
              credit:{
                accountDate : "",
                accountSubjectId : "",
                amount : "",
                summary : "",
                gentianNumber : ""
              }
            }
        ],
        journalTables:[
            { id:0 },
        ],
        nextTableId:1,
        journalSubjects:{
            0: '',
            1: '事業主貸',
            2: '普通預金',
            3: '未払金',
            4: '売掛金'
        },
      }
    },
    components:{
      JournalTableComponent
    },
    created: function() {
      console.log('ユーザー情報取得');
      axios.get('http://localhost:8888/accounting_software/public/api/user')
      .then(response => {
          console.log(response.data);
      })
      .catch(error => {
        console.log('未ログインユーザです');
      });
    },
    methods:{
      add: function(){
        this.journalTables.push({ id: this.nextTableId })
        this.data.push(
              {
                debit:{
                  accountDate : "",
                  accountSubjectId : "",
                  amount : "",
                  summary : "",
                  gentianNumber : ""
                },
                credit:{
                  accountDate : "",
                  accountSubjectId : "",
                  amount : "",
                  summary : "",
                  gentianNumber : ""
              }
            })
        this.nextTableId = this.nextTableId + 1
      },
      updateJournalData:function(id,inputData){
        if(inputData.key === "amount" || inputData.key === "gentianNumber"){
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