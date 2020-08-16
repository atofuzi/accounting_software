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
                <tr>
                    <th>会計日</th>
                    <td><input type="date" name="accountDate" :value="journalData.debit.accountDate" @change="change($event, 'debit')"></td>
                    <th></th>
                    <td></td>
                </tr>
                <tr>
                    <th>会計科目</th>
                    <td>
                        <select name="accountSubjectId" :value="journalData.debit.accountSubjectId" @change="change($event, 'debit')">
                            <option v-for="(value, index) in journalSubjects" :key="index" :value="index">{{ value }}</option>
                        </select>
                    </td>
                    <th>会計科目</th>
                    <td>
                        <select name="accountSubjectId" :value="journalData.credit.accountSubjectId" @change="change($event, 'credit')">
                            <option v-for="(value, index) in journalSubjects" :key="index" :value="index">{{ value }}</option>
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
                <tr>
                    <th>元丁番号</th>
                    <td><input type="number" name="gentianNumber" :value="journalData.debit.gentianNumber" @change="change($event, 'debit')"></td>
                    <th>元丁番号</th>
                    <td><input type="number" name="gentianNumber" :value="journalData.credit.gentianNumber" @change="change($event, 'credit')"></td>
                </tr>
            </tbody>
            </table>
    </div>
</template>

<script>
export default {
    props:{
        journalSubjects:Object,
        journalData:Object,
        journalType:Object
    },
    methods:{
        change: function(event,journalType){
            const data = { key : event.target.name, value : event.target.value , type : journalType}
            console.log(data)
            this.$emit('change', data)
        }
    }
}
</script>