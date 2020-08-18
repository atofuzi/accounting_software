<template>
    <div>
        <table class="table table-striped">
            <thead>
                <th>借方</th>
                <th></th>
                <th>借方</th>
                <th></th>
            </thead>
            <tbody>
                <tr v-if="count == 0">
                    <th>会計日</th>
                    <td><input type="date" name="accountDate" :value="journalData.debit.accountDate" @change="change($event, 'debit')"></td>
                    <th></th>
                    <td></td>
                </tr>
                <tr>
                    <th>会計科目</th>
                    <td>
                        <select name="accountSubjectId" :value="journalData.debit.accountSubjectId" @change="change($event, 'debit')">
                            <option value="0"></option>
                            <option v-for="(value, index) in journalSubjects" :key="index" :value="value.accountSubjectId">{{ value.accountSubject }}</option>
                        </select>
                    </td>
                    <th>会計科目</th>
                    <td>
                        <select name="accountSubjectId" :value="journalData.credit.accountSubjectId" @change="change($event, 'credit')">
                            <option value="0"></option>
                            <option v-for="(value, index) in journalSubjects" :key="index" :value="value.accountSubjectId">{{ value.accountSubject }}</option>
                        </select>
                    </td>
                </tr>
                <tr> 
                    <th v-if="isActiveDebit || isActiveCredit">{{ addInfoTitleDebit }}</th>
                    <td v-if="isActiveDebit">
                        <select v-if="isActiveDebit" name="addInfoId" :value="journalData.debit.addInfoId" @change="change($event, 'debit')">
                            <option value=""></option>
                            <option v-for="(value, index) in addListsDebit" :key="index" :value="value.id">{{ value.name }}</option>
                        </select>
                    </td>
                    <td v-else-if="isActiveCredit"></td>
                    <th v-if="isActiveCredit">{{ addInfoTitleCredit }}</th>
                    <td v-if="isActiveCredit">
                        <select name="addInfoId" :value="journalData.credit.addInfoId" @change="change($event, 'credit')">
                            <option value=""></option>
                            <option v-for="(value, index) in addListsCredit" :key="index" :value="value.id">{{ value.name }}</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th>摘要</th>
                    <td><input type="text" name='summary' :value="journalData.debit.summary" @change="change($event, 'debit')"></td>
                    <th>摘要</th>
                    <td><input type="text" name='summary' :value="journalData.credit.summary" @change="change($event, 'credit')"></td>
                </tr>
                <tr>
                    <th>金額</th>
                    <td><input type="number" name="amount" :value="journalData.debit.amount" @change="change($event, 'debit')"></td>
                      <th>金額</th>
                    <td><input type="number" name="amount" :value="journalData.credit.amount" @change="change($event, 'credit')"></td>
                </tr>
            </tbody>
            </table>
    </div>
</template>

<script>
export default {
    data(){
        return{
            isActiveDebit: false,
            isActiveCredit: false,
            addInfoTitleDebit: "",
            addInfoTitleCredit: "",
            addListsDebit:[],
            addLlistsDebit:[],
            
        }
    },
    props:{
        journalSubjects:Array,
        journalData:Object,
        journalType:Object,
        banks:Array,
        suppliers:Array,
        count:Number
    },
    methods:{
        change: function(event,journalType){
            const data = { key : event.target.name, value : event.target.value , type : journalType}
            console.log(data)
            this.$emit('change', data)
            if(event.target.name === 'accountSubjectId'){
                this.addSubject()
            }
        },
        addSubject: function(){
            if(this.journalData.debit.accountSubjectId >= 2 && this.journalData.debit.accountSubjectId <=5){
                this.isActiveDebit = true
                this.addInfoTitleDebit = "取引銀行"
                this.addListsDebit = this.banks
            }else if(this.journalData.debit.accountSubjectId == 7 || this.journalData.debit.accountSubjectId == 20){
                this.isActiveDebit = true
                this.addInfoTitleDebit = "取引先"
                this.addListsDebit = this.suppliers
            }else{
                this.isActiveDebit = false
                this.addInfoTitleDebit = ""
                this.$emit('change', { key: 'addInfoId', value: "", type: 'debit'})
            } 
            
            if(this.journalData.credit.accountSubjectId >= 2 && this.journalData.credit.accountSubjectId <=5){
                this.isActiveCredit = true
                this.addInfoTitleCredit = "取引銀行"
                this.addListsCredit = this.banks
            }else if(this.journalData.credit.accountSubjectId == 7 || this.journalData.credit.accountSubjectId == 20){
                this.isActiveCredit = true
                this.addInfoTitleCredit = "取引先"
                this.addListsCredit = this.suppliers
            }else{
                this.isActiveCredit = false
                this.addInfoTitleCredit = ""
                this.$emit('change', { key: 'addInfoId', value: "", type: 'credit'})
            }     
        }
    },
}
</script>