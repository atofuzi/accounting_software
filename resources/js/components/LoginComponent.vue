<template>
    <div class="row">
        <div class="offset-4 col-md-4">
            <br>
            <h4>ログイン</h4>
            <br>
            <div class="form-group">
                <label>Email</label>
                <input type="text" class="form-control" v-model="userName">
                <div class="alert alert-danger" v-text="errors.email" v-if="errors.email"></div>
            </div>
            <div class="form-group">
                <label>パスワード</label>
                <input type="password" class="form-control" v-model="password">
                <div class="alert alert-danger" v-text="errors.email" v-if="errors.email"></div>
            </div>
            <div class="form-group">
                <button type="button" class="btn btn-dark btn-block" @click="login">ログイン</button>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    data(){
        return {
            userName: '',
            password: '',
            grantType: 'password',
            clientId: 1,
            clientSecret: 'gjjfMazrUofJhluvGV9Vqr9Bd5HJgnrCm3y5c4FY',
            scop: '',
            errors: {}
        }
    },
    methods: {
        async login(){
            console.log('ログイン開始');
            var getTokenUrl = 'http://localhost:8888/accounting_software/public/oauth/token';
            let params = new URLSearchParams();
            params.append('username', this.userName);
            params.append('password', this.password);
            params.append('grant_type', this.grantType);
            params.append('client_id', this.clientId);
            params.append('client_secret', this.clientSecret);
            params.append('scop', this.scop);

            await axios.post(getTokenUrl, params)
            .then(function(response){
                console.log('token取得');                    
            })
            .catch(function(error){
                var responseErrors = error.response.data.errors;
                var errors = {};

                for(var key in responseErrors){
                    errors[key] = responseErrors[key][0];
                }
                self.errors = errors;
            });

            var loginUrl = 'http://localhost:8888/accounting_software/public/login';
            let loginData = new URLSearchParams();
            loginData.append('email',this.userName);
            loginData.append('password',this.password);
            
            await axios.post(loginUrl,loginData).then(function(response){
                console.log('ログイン成功');
                location.href = 'http://localhost:8888/accounting_software/public/home';
            })
            .catch(function(error){

            });     
        }
    }
}

</script>
