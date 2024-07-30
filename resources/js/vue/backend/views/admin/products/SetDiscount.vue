<template>
    <div class="container">
        <div class="card list_card">
            <div class="card-header">
                <h4>Set Descount</h4>
                <div class="btns">
                    <router-link :to="{ name: `All${route_prefix}` }" class="btn rounded-pill btn-outline-warning" >
                        <i class="fa fa-arrow-left me-5px"></i>
                        Back
                    </router-link>
                </div>
            </div>
            <form @submit.prevent="call_store(`store_discount_${store_prefix}`,$event.target)" class="create_form" autocomplete="false">
                <div class="card-body">
                    <div class="row justify-content-center">
                        <div class="col-lg-10">
                            <div class="admin_form form_1">
                                <div class=" form-group full_width d-grid align-content-start gap-1 mb-2 " >
                                    <label >Select Product</label>
                                    <management-modal :id="'product_id'" :select_qty="1"></management-modal>
                                </div>
                                <div v-if="Object.keys(selected).length" class="form-group full_width d-grid align-content-start gap-1 mb-2 " >
                                    <div>
                                        <!-- {{ selected }} -->
                                        <table class="table">
                                            <tbody>
                                                <tr>
                                                    <th style="width: 225px;">Product Name</th>
                                                    <th style="width: 3px;">:</th>
                                                    <th>{{ selected.product_name }}</th>
                                                </tr>
                                                <tr>
                                                    <th>Cost</th>
                                                    <th style="width: 3px;">:</th>
                                                    <th>{{ selected.cost }} TK</th>
                                                </tr>
                                                <tr>
                                                    <th>Sales Price</th>
                                                    <th style="width: 3px;">:</th>
                                                    <th>{{ selected.sales_price }} TK</th>
                                                </tr>
                                                <tr>
                                                    <th>Discount Parcent</th>
                                                    <th style="width: 3px;">:</th>
                                                    <th>{{ selected.discount_parcent }}%</th>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div v-if="Object.keys(selected).length" class=" form-group d-grid align-content-start gap-1 mb-2 " >
                                    <input-field
                                        :label="`Percent Discount`"
                                        :name="`percent_discount`"
                                        :mutator="discount_change"
                                        :value="selected.discount_parcent"
                                    />
                                </div>
                                <div v-if="Object.keys(selected).length" class=" form-group d-grid align-content-start gap-1 mb-2 " >
                                    <input-field
                                        :label="`Flat Discount`"
                                        :name="`flat_discount`"
                                        :mutator="flat_discount_change"
                                        :value="selected.discount_flat"
                                    />
                                </div>
                                <!-- <div v-if="Object.keys(selected).length" class=" form-group d-grid align-content-start gap-1 mb-2 " >
                                    <input-field
                                        :label="`Expire Date`"
                                        :name="`expire_date`"
                                        :type="'datetime-local'"
                                        :value="formatDate(selected.discount_info.expire_date,'YYYY-MM-DD HH:MM')"
                                    />
                                </div> -->
                                <div v-if="Object.keys(selected).length" class=" form-group d-grid align-content-start gap-1 mb-2 " >
                                    <input-field
                                        :label="`Discount Price`"
                                        :name="`discount_amount`"
                                        :type="'text'"
                                        :readonly="true"
                                        :styles="'cursor:not-allowed;'"
                                        :value="discount_amount || selected.discount_flat"
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
import ManagementModal from './components/ManagementModal.vue';
/** store and route prefix for export object use */
import PageSetup from './PageSetup';
const {route_prefix, store_prefix} = PageSetup;

export default {
    components: { InputField, ManagementModal },
    data: function(){
        return {
            /** store prefix for JSX */
            store_prefix,
            route_prefix,
            selected: {},
            discount_amount: 0,
            flat_discount: 0,
        }
    },
    created: function () {
        this.$watch(`selected_items`,function(){
            this.selected = this.selected_items[0] || {};
        })
    },
    methods: {
        ...mapActions([`store_discount_${store_prefix}`]),
        call_store: function(name, params=null){
            this[name](params)
        },
        discount_change: function({value, name, event}){
            if(!value){
                value = 0;
            }
            if(value < 0 ){
                event.target.value = 0;
            }
            if(value > 100){
                event.target.value = 100;
            }
            document.querySelector('input[name="flat_discount"]').value = 0;

            this.calc_discount_price(+value, "percent");
            console.log(value);

        },
        flat_discount_change: function({value, name, event}){
            if(!value){
                value = 0;
            }
            if(value < 0 ){
                event.target.value = 0;
            }
            if(value > this.selected.sales_price ){
                event.target.value = this.selected.sales_price;
            }
            value = +value;
            document.querySelector('input[name="percent_discount"]').value = 0;
            this.calc_discount_price(value, "flat");
            console.log(value);
        },
        calc_discount_price: function(value=0, type="flat"){
            if(type=='percent'){
                let discount_price = this.selected.cost * value / 100;
                this.discount_amount = this.selected.cost - discount_price;
                this.flat_discount = discount_price
                document.querySelector('input[name="flat_discount"]').value = discount_price;
            }
            if(type=='flat'){
                this.flat_discount = value;
                this.discount_amount = this.selected.cost - value;
                document.querySelector('input[name="percent_discount"]').value = Math.round(100*value/this.selected.cost);
            }
            this.discount_amount = this.selected.cost - this.flat_discount;

            console.log(this.selected.cost, this.flat_discount);
        }
    },
    computed: {
        ...mapGetters({
            selected_items: `get_${store_prefix}_selected`
        }),
    }
};
</script>

<style>
</style>
