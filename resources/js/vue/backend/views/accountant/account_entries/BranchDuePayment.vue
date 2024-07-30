<template>
    <div class="container">
        <div class="card list_card">
            <div class="card-header">
                <h4>Branch Due Entry</h4>
                <div class="btns">
                    <a href="" @click.prevent="$router.push({ name: `AccountBranchDue` })"  class="btn rounded-pill btn-outline-warning" >
                        <i class="fa fa-arrow-left me-5px"></i>
                        <span >
                            Back
                        </span>
                    </a>
                </div>
            </div>
            <form @submit.prevent="()=>''" class="create_form" autocomplete="false">
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-12">
                            <h4>Due Orders</h4>
                            <table class="table text-nowrap">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Order Id</th>
                                        <th>Total Payable</th>
                                        <th>Total Paid</th>
                                        <th>Total Due</th>
                                        <th>Pay Amount</th>
                                        <th>Due Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="order in due_orders" :key="order.id">
                                        <td>{{ order.sales_id }}</td>
                                        <td>{{ order.total_price }}</td>
                                        <td>{{ order.total_paid }}</td>
                                        <td>{{ order.total_price - order.total_paid }}</td>
                                        <td class="text-end">
                                            <span class="pe-1 text-warning font-medium-1">
                                                {{ order.pay_amount }}
                                            </span>
                                            <!-- <input
                                                v-model="order.pay_amount"
                                                type="number"
                                                @change="calc_due_payment_of_orders()"
                                                style="width:100px;" class="text-end float-right form-control"> -->
                                        </td>
                                        <td class="text-end">
                                            {{ order.due_amount }}
                                            <!-- <input
                                                v-model="order.minus"
                                                @keyup="calc_order_due_amount(order)"
                                                value="0"
                                                type="text"
                                                style="width:100px;" class="text-end float-right form-control"> -->
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot class="border-top">
                                    <tr>
                                        <td colspan="4" class="text-end">Payable</td>
                                        <td class="text-end ">
                                            <b class="pe-1 text-warning font-medium-1">
                                                {{ total_due?.toFixed(2) }}
                                            </b>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="6" class="border-0"></td>
                                    </tr>
                                    <tr>
                                        <td colspan="4" class="text-end">Deposit Amount</td>
                                        <td class="text-end ">
                                            <b class="pe-1" :class="{'text-danger': advance_paid <= 0,'text-success': advance_paid > 0 }">
                                                {{  advance_paid?.toFixed(2) }}
                                            </b>
                                        </td>
                                    </tr>
                                    <tr v-if="advance_paid > 0">
                                        <td colspan="4" class="text-end">Paid</td>
                                        <td class="text-end ">
                                            <span class="pe-1">
                                                <input type="number"
                                                    v-model="total_paid"
                                                    @change="validate_paid_amount"
                                                    style="width:100px;"
                                                    class="text-end float-right form-control">
                                            </span>
                                        </td>
                                    </tr>
                                    <tr >
                                        <td v-if="total_due > 0" colspan="4" class="text-end text-danger">
                                            Due
                                        </td>
                                        <td v-else colspan="4" class="text-end text-warning">Present Extra Money</td>
                                        <td class="text-end ">
                                            <b class="pe-1" v-if="total_due > 0">
                                                {{ total_due }}
                                            </b>
                                            <b class="pe-1" v-else>
                                                {{ present_extra }}
                                            </b>
                                        </td>
                                    </tr>

                                </tfoot>
                            </table>

                        </div>
                    </div>
                </div>
                <div class="card-footer text-center">
                    <div v-if="advance_paid > 0 && due_orders.length > 0">
                        <button v-if="!submitting" @click.prevent="pay_due_submit" type="button" class="btn btn-outline-info" >
                            <i class="fa fa-upload"></i>
                            Submit
                        </button>
                        <button v-else class="btn btn-outline-danger">
                            Submitting
                        </button>
                    </div>
                    <div v-else>
                        user do not have enough money or order to pay.
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
import { mapActions, mapGetters, mapMutations } from 'vuex'
import InputField from '../../components/InputField.vue'
import CateogryTypesModal from "../account_types/components/ManagementModal.vue"
/** store and route prefix for export object use */
import PageSetup from './PageSetup';
const {route_prefix, store_prefix} = PageSetup;
export default {
    components: { InputField, CateogryTypesModal },
    data: function(){
        return {
            /** store prefix for JSX */
            store_prefix,
            route_prefix,
            total_paid: 0,
            advance_paid: 0,
            account_vlaues: [],
            account_id: null,
            account_number_id: null,
            trx_id: null,
            submitting: false,

            due_orders: [],
            available_amount_for_pay: 0,
        }
    },
    created: async function () {
        await this.fetch_user({id: this.$route.params.user_id}),
        await this.branch_all_dues_by_id(this.$route.params.user_id);
        await this.fetch_payment_accounts();

        this.due_orders = this.orders;
        this.advance_paid = this.user.transaction.extra_money;

        // this.calc_due_payment_of_orders();
        this.calc_available_amount_for_pay();
        this.total_paid = this.available_amount_for_pay;

        this.$watch('total_paid',function(v){
            this.calc_due_payment_of_orders();
        })
    },
    methods: {
        ...mapActions([
            `fetch_user`,
            'branch_all_dues_by_id',
            `fetch_payment_accounts`,
        ]),
        ...mapMutations([

        ]),
        call_store: function(name, params=null){
            this[name](params)
        },

        pay_due_submit: async function(){
            let orders = this.orders.map(i=>({id: i.id, pay_amount: i.pay_amount }));
            // let account_id = this.account_id;
            // let account_number_id = this.account_number_id;
            // let trx_id = this.trx_id;
            let user_id = this.$route.params.user_id;
            let total_paid = this.total_paid;

            let extra_money = this.extra_money;
            if(extra_money > 0){
                extra_money = 0; // it is due, not extra money.
            }

            let that = this;
            let cconfirm = await window.s_confirm("Submit payment?");
            if(!cconfirm){
                return 0;
            }
            that.submitting = true;
            if(that.submitting){
                try {
                    await axios.post('/accountant/account-entry/store/due',{
                        orders,
                        user_id,
                        extra_money,
                        total_paid,
                    });
                    that.submitting = false;
                    that.total_paid = 0;
                    await that.fetch_user({id: user_id});
                    await that.branch_all_dues_by_id(user_id);
                    that.advance_paid = that.user.transaction.extra_money;
                    that.due_orders = that.orders;
                    that.calc_due_payment_of_orders();

                } catch (error) {
                    that.submitting = false;
                    console.log(error);
                }
            }

        },

        calc_due_payment_of_orders: function(){
            // let rest_amount = this.advance_paid;
            let rest_amount = +this.total_paid;

            this.due_orders = this.due_orders.map(order => {
                let order_due = order.total_price - order.total_paid;
                if(rest_amount >= order_due){
                    order.pay_amount = order.total_price;
                    order.due_amount = 0;
                    rest_amount = rest_amount - order_due;
                }else{
                    if(rest_amount > 0 && rest_amount <= order_due){
                        order.pay_amount = rest_amount;
                    }else{
                        order.pay_amount = 0;
                    }
                    order.due_amount = order_due - order.pay_amount;
                    rest_amount  -= rest_amount;
                }
                return order;
            });
        },

        set_selected_account_values: function(account_id){
            this.account_vlaues = this.accounts.find(i=>i.id==account_id)?.numbers || [];
        },
        calc_available_amount_for_pay: function(){
            let rest_amount = +this.advance_paid;
            let available_amount_for_pay = 0;
            this.due_orders = this.due_orders.map(order => {
                let order_due = order.total_price - order.total_paid;
                if(rest_amount >= order_due){
                    order.pay_amount = order.total_price;
                    order.due_amount = 0;
                    rest_amount = rest_amount - order_due;
                    available_amount_for_pay += order.pay_amount;
                }else{
                    if(rest_amount > 0 && rest_amount <= order_due){
                        order.pay_amount = rest_amount;
                    }else{
                        order.pay_amount = 0;
                    }
                    order.due_amount = order_due - order.pay_amount;
                    rest_amount  -= rest_amount;

                    available_amount_for_pay += order.pay_amount;
                }
                return order;
            });
            this.available_amount_for_pay = available_amount_for_pay;
        },
        validate_paid_amount: function(){
            if(this.total_paid < 0){
                this.total_paid = 0;
            }
            if(this.total_paid > this.available_amount_for_pay){
                this.total_paid = this.available_amount_for_pay;
            }
        },
        // calc_order_due_amount: function(order){
        //     let due_amount = +order.minus > 0? order.total_price - order.total_paid - order.minus : order.total_price - order.total_paid;
        //     console.log(due_amount);
        //     // order.due_amount = due_amount;
        // }
    },
    beforeDestroy: function(){

    },
    computed: {
        ...mapGetters({
            orders: 'all_branch_due_orders',
            accounts: `get_payment_accounts`,
            user: `get_user`,
        }),
        total_due: function(){
            let order_dues = this.orders.reduce((t,i) => t += (i.total_price - i.total_paid), 0);
            return order_dues - this.total_paid;
        },
        present_extra: function(){
            let extra = this.advance_paid - this.total_paid;
            if(this.advance_paid > 0 && this.total_due <= 0){
                return Math.abs(extra).toFixed(2);
            }else{
                return 0;
            }
        }
    }
};
</script>

<style>
</style>
