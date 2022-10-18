<template>
  <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">Send Money</h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default">
                       
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="alert alert-danger alert-dismissable" v-if="errors.length">
                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                      <b class="input--error">Please check the fields:</b>
                                      <ul>
                                        <li class="input--error" v-for="error in errors" :key="error">{{ error }}</li>
                                      </ul>
                                    </div>
                                    <div v-if="success" class="alert alert-success alert-dismissable">
                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                         <span>{{success_message}}</span>
                                    </div>
                                    <form role="form" @submit="onSubmit">
                                      <div class="form-group">
                                            <label>Receiver</label>
                                            <select class="form-control" v-model="form.receiver_id">
                                              <option value="">Select a Reciver</option>
                                               <option
                                                  v-for="(user, index) in listUser"
                                                  :value="user.id"
                                                  :key="index"
                                                >
                                                  {{ user.name }}
                                                </option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                          <label for="disabledSelect">Disabled input</label>
                                          <input class="form-control" id="wallet" type="text" v-model="wallet" disabled>
                                        </div>
                                         <div class="form-group">
                                            <label for="disabledSelect">Amount</label>
                                            <input class="form-control" id="disabledInput" type="text" placeholder="0.00" v-model="form.amount">
                                        </div>
                                                    
                                        <button type="submit" class="btn btn-primary">Submit Button</button>
                                        <button type="reset" class="btn btn-secondary" @click="onReset">Reset Button</button>
                                    </form>
                                </div>
                                <!-- /.col-lg-6 (nested) -->
                            </div>
                            <!-- /.row (nested) -->
                        </div>
                        <!-- /.panel-body -->
                    </div>
                    <!-- /.panel -->
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
        </div>
        <!-- /#page-wrapper -->
</template>

<script>

import { mapGetters } from 'vuex'
  export default {
    data() {
      return {
        errors: [],
        form: {
          amount: "",
          receiver_id: '',
          sender_id: ''
        },
      
        show: true,
        listUser: [],
        wallet: "",
        senderId: null,
        success_message:"",
        success:false
      }
    },
      computed: {
        ...mapGetters(['user'])
    },
    mounted() {
      this.userList();
      this.userProfile();
      this.setUser()
    },
    methods: {

      setUser(){
        this.senderId = this.user.id;
      },

      onSubmit(event) {
        event.preventDefault();
    
        this.errors = [];

        if (!this.form.receiver_id) {
          this.errors.push('Please select receiver');
        }
        if (!this.form.amount) {
          this.errors.push('Please input amount');
        }
        if (parseFloat(this.form.amount) > parseFloat(this.wallet)) {
          this.errors.push('Amount must be less then wallet');
        }
        if (this.form.amount <= 0) {
          this.errors.push('Amount must be greater then 0');
        }

        if (this.errors.length) {
          return;
        }
        this.form.sender_id = this.senderId;

        let data = {
          'sender_id':this.form.sender_id,
          'receiver_id':this.form.receiver_id,
          'amount':this.form.amount,
        }
       this.sendMony(data);
    
      },

      onReset(event) {
        event.preventDefault()
        // Reset our form values
        this.form.amount = "";
    
        // Trick to reset/clear native browser form validation state
        this.show = false
        this.$nextTick(() => {
          this.show = true
        })
      },

      async userList() {
        try {
          const response = await axios.get("user-list");
          if (response.data.data){
            this.listUser = response.data.data;
          }

        } catch (error) {
          notify.authError(error);
        }
      },

      async sendMony(data) {        
        try {
          
            const response = await axios.post("send-money", data);
            this.wallet = this.wallet  - data.amount;
            this.success_message = 'Send money succesfull.'
            this.success = true;
            this.amout = ""
          } catch (error) {
            console.log(error)
          }
      },

      async userProfile() {
        try {
          const response = await axios.get("user-profile");
          if (response.data.data){
            this.wallet = response.data.data.wallet;
          }

        } catch (error) {
          notify.authError(error);
        }
      },
    }
  }
</script>
<style scoped>
  .input--error{
    color:red;
  }
</style>