<template>
    <div class="container">
        <div class="card list_card">
            <div class="card-header">
                <h4>Income Entry</h4>
                <div class="btns">
                    <!-- <router-link :to="{ name: `All${route_prefix}` }" class="btn rounded-pill btn-outline-warning" >
                        <i class="fa fa-arrow-left me-5px"></i>
                        Back
                    </router-link> -->
                </div>
            </div>
            <form @submit.prevent="call_store(`store_${store_prefix}`,$event.target)" class="create_form" autocomplete="false">
                <div class="card-body">
                    <div class="row justify-content-center">
                        <div class="col-lg-10">
                            <div class="admin_form form_1">
                                <div class=" form-group full_width d-grid align-content-start gap-1 mb-2 " >
                                    <label>
                                        Select Income Category
                                        <b class="text-warning">*</b>
                                    </label>
                                    <!-- <CateogryTypesModal :id="'type_id'" :select_qty="1"></CateogryTypesModal> -->
                                    <select name="category" class="form-select" id="category">
                                        <option value="">select</option>
                                        <option v-for="cat in categories" :key="cat.id" :value="cat.id">
                                            {{ cat.title }}
                                        </option>
                                    </select>
                                </div>

                                <div class=" form-group full_width d-grid align-content-start gap-1 mb-2 " >
                                    <label>
                                        Select Customer
                                        <b class="text-warning">*</b>
                                    </label>
                                    <CustomerModal></CustomerModal>
                                </div>

                                <div class=" form-group full_width d-grid align-content-start gap-1 mb-2 " >
                                    <input-field
                                        :label="`Entry Amount <b class='text-warning'>*</b>`"
                                        :name="`amount`"
                                        :keyup="on_expense_change"
                                    />
                                </div>

                                <div class="form-group full_width d-grid align-content-start gap-1 mb-2 ">
                                    <label for="">Amount in text</label>
                                    <input type="text" v-model="number_in_text" id="amount_in_text" name="amount_in_text" class="form-control">
                                </div>

                                <div class="form-group mb-2">
                                    <label for="Account">
                                        Account
                                        <b class="text-warning">*</b>
                                    </label>
                                    <select id="account_id" @change="set_selected_account_values($event.target.value)" name="account_id" class="form-select">
                                        <option value="">select</option>
                                        <option  v-for="account in accounts" :key="account.id" :value="account.id">
                                            {{ account.name.replaceAll('_',' ') }}
                                        </option>
                                    </select>

                                    <ul class="mt-2">
                                        <li v-for="value in account_vlaues" :key="value.id">
                                            <label :for="value.id">
                                                <div class="d-flex flex-wrap gap-2">
                                                    <input class="order-1" :id="value.id" name="payment_method" type="radio" :value="JSON.stringify(value)">
                                                    <div class="order-2">
                                                        {{ value.value }}
                                                    </div>
                                                </div>
                                            </label>
                                        </li>
                                    </ul>
                                </div>

                                <div class="form-group mb-2">
                                    <label for="">TRX ID</label>
                                    <input type="text" id="trx_id" name="trx_id" class="form-control">
                                </div>

                                <div class="form-group mb-2">
                                    <label for="">Folio No</label>
                                    <input type="text" id="receipt_no" name="receipt_no" class="form-control">
                                </div>

                                <div class=" form-group d-grid full_width align-content-start gap-1 mb-2 " >
                                    <label for="description">
                                        Entry Description
                                        <b class="text-warning">*</b>
                                    </label>
                                    <textarea class="form-control" id="description" name="description"></textarea>
                                </div>

                                <div class=" form-group d-grid full_width align-content-start gap-1 mb-2 " >
                                    <label for="reference">Entry Reference</label>
                                    <textarea class="form-control" id="reference" name="reference"></textarea>
                                </div>
                                <div class=" form-group full_width d-grid align-content-start gap-1 mb-2 " >
                                    <input-field
                                        :label="`Attachment`"
                                        :name="`attachments`"
                                        :type="`file`"
                                        :id="`attachments`"
                                        :multiple="true"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-center">
                    <button type="submit" class="btn btn-outline-info" >
                        <i class="fa fa-upload"></i>
                        Submit
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import InputField from '../../components/InputField.vue'
import CateogryTypesModal from "../account_types/components/ManagementModal.vue"
import CustomerModal from "../customers/components/ManagementModal.vue"
/** store and route prefix for export object use */
import PageSetup from './PageSetup';
const {route_prefix, store_prefix} = PageSetup;

export default {
    components: { InputField, CateogryTypesModal, CustomerModal },
    data: function(){
        return {
            /** store prefix for JSX */
            store_prefix,
            route_prefix,
            account_vlaues: [],
            number_in_text: '',
        }
    },
    created: async function () {
        await this[`fetch_accountant_category_income_categories`]();
        await this.fetch_payment_accounts();
    },
    methods: {
        ...mapActions([
            `store_${store_prefix}`,
            `fetch_accountant_category_income_categories`,
            `fetch_payment_accounts`,
        ]),
        call_store: function(name, params=null){
            this[name](params)
        },
        set_selected_account_values: function(account_id){
            this.account_vlaues = this.accounts.find(i=>i.id==account_id)?.numbers || [];
        },
        on_expense_change: function(v){
            let inputValue = v.value;
            inputValue = inputValue.replace(/[^0-9.]/g, '');
            inputValue = inputValue.replace(/(\..*)\./g, '$1');
            v.event.target.value = inputValue;
            try {
                this.number_in_text = convertAmount(inputValue);
            } catch (error) {
                this.number_in_text = '';
            }
            // console.log(v, inputValue, this.number_in_text);
        }
    },
    computed: {
        ...mapGetters({
            categories: `get_income_categories`,
            accounts: `get_payment_accounts`,
        }),
    }
};
</script>

<style>
</style>
