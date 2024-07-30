<template>
    <div class="container-fluid">
        <div class="card list_card" v-if="data">
            <div class="card-header">
                <h4>Edit Order</h4>
                <div class="btns d-flex">
                    <router-link
                        :to="{name:`Details${route_prefix}`,params:{id: data.id}}">
                        <span class="btn btn-outline-success rounded-pill me-2">
                            Details
                        </span>
                    </router-link>
                    <router-link :to="{ name: `BranchOrder` }" class="btn d-flex rounded-pill btn-outline-warning" >
                        <i class="fa fa-arrow-left me-5px"></i>
                        Back
                    </router-link>
                </div>
            </div>
            <!-- <form @submit.prevent="call_store(`store_${store_prefix}`,$event.target)" autocomplete="false"> -->
            <div onsubmit="event.preventDefault()">
                <div class="card-body">
                    <div class="pb-3">
                        <ProductManagementModal></ProductManagementModal>
                    </div>
                    <div class="order_at_a_glance">
                        <div class="products_list custom_scroll">
                            <div class="table">
                                <div class="border border-1 position-sticky top-0 borde-info p-1 rounded-sm mb-2">
                                    <table class="table ">
                                        <thead class="position-sticky" style="position: sticky; top: 0;">
                                            <tr>
                                                <th style="width: 10px;">SL</th>
                                                <th class="text-start">Title</th>
                                                <th style="width: 125px;">Price</th>
                                                <th style="width: 125px;">Qty</th>
                                                <th style="width: 125px;">Com %</th>
                                                <th style="width: 125px;">D.Price</th>
                                                <th style="width: 200px;" class="text-end">Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(product, index) in selected_products" :key="product.id" :title="product.product_name">
                                                <td>{{ index+1 }}</td>
                                                <td class="text-start">
                                                    <img :src="`/${product.thumb_image}`" style="height: 60px;">
                                                    <!-- {{ product.product_name }} <br> -->
                                                    <!-- <br>
                                                    <div>
                                                        ৳ {{ product.current_price.toString().enToBn() }}
                                                    </div>
                                                    <br> -->
                                                    <!-- <a href="#" @click.prevent="remove_cart({product})" class="text-danger">delete</a> -->
                                                </td>
                                                <td>
                                                    {{ product.cost.toString().enToBn() }}
                                                </td>
                                                <td class="text-center">
                                                    <input type="number" min="0"
                                                        v-model="product.qty"
                                                        style="width: 70px;" class="form-control">
                                                </td>
                                                <td class="text-center">
                                                    <input type="number" min="0"
                                                        v-model="product.discount_parcent"
                                                        @keyup="update_discount(product)"
                                                        style="width: 70px;" class="form-control">
                                                </td>
                                                <td>
                                                    {{ product.sales_price.toFixed(2).toString().enToBn() }}
                                                </td>
                                                <td class="text-end">
                                                    ৳ {{ (product.qty * product.sales_price).toFixed(2).toString().enToBn() }}
                                                </td>
                                            </tr>
                                        </tbody>
                                        <tfoot style="position: sticky; bottom: 0;">
                                            <tr>
                                                <th colspan="6" class="text-end">total</th>
                                                <th class="text-end font_20 text-nowrap">৳ {{ tota_order_price.toFixed(2).toString().enToBn() }}</th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="total p-1 border border-1">
                            <form action="#" class="mt-3" v-if="order_carts.length">
                                <div class="mb-2 d-flex gap-2 justify-content-end">
                                    <label for="delivery_charge">Sub Total</label>
                                    <div class="form-control text-end font_20" style="width: 180px;">
                                        ৳ {{ tota_order_price.toFixed(2).toString().enToBn() }}
                                    </div>
                                </div>

                                <div class="mb-2 d-flex gap-2 justify-content-end">
                                    <label for="delivery_charge">
                                        Delivery Charge <br>
                                        <span @click.stop="balance_fraction" class="text-warning">Balance Amount</span>
                                    </label>
                                    <input type="number" v-model="shipping_charge" step=".01" name="delivery_charge" id="delivery_charge" class="form-control text-end font_20" style="width: 180px">
                                </div>
                                <!-- <div  class="mb-2 d-flex gap-2 justify-content-end">
                                    <label for="discount">Discount on product</label>
                                    <input type="number" readonly v-model="discount_on_product" step=".01" name="discount" id="discount" class="form-control text-end font_20" style="width: 180px">
                                </div> -->

                                <div class="mb-2 d-flex gap-2 justify-content-end">
                                    <label for="delivery_charge">Total</label>
                                    <div class="form-control text-end font_20" style="width: 180px">
                                        ৳ {{ (+tota_order_price + +shipping_charge).toFixed(2).toString().enToBn() }}
                                    </div>
                                </div>

                                <div class="mb-2 d-flex gap-2 justify-content-end">
                                    <label for="discount">Discount on total %</label>
                                    <input type="number" v-model="total_discount_percent" step=".01" name="discount" id="discount" class="form-control text-end font_20" style="width: 180px">
                                </div>

                                <div class="mb-2 d-flex gap-2 justify-content-end">
                                    <label for="delivery_charge">Total Payable</label>
                                    <div class="form-control text-end font_20" style="width: 180px">
                                        ৳ {{ (+tota_order_price + +shipping_charge - +total_discount).toFixed(2).toString().enToBn() }}
                                    </div>
                                </div>
                                <div class="mb-2 d-flex gap-2 justify-content-end">
                                    <label for="total_paid">Total Paid</label>
                                    <input type="number" v-model="total_paid" step=".01" name="total_paid" id="total_paid" class="form-control text-end font_20" style="width: 180px">
                                </div>
                                <div class="mb-2 d-flex gap-2 justify-content-end">
                                    <label for="total_paid">Due</label>
                                    <div :class="{'bg-danger':total_due<0, 'bg-success': total_due ==0, 'bg-warning': total_due > 0}"
                                        class="form-control text-light text-end text-black font_20" style="width: 180px">
                                        ৳ {{ (total_due).toFixed(2).toString().enToBn() }}
                                    </div>
                                </div>

                                <div class="mb-2">
                                    <label class="me-2" id="cash">
                                        <input type="radio" v-model="account_id" name="cash" id="cash" value="1">
                                        Cash
                                    </label>
                                    <label class="me-2" id="bank">
                                        <input type="radio" v-model="account_id" name="bank" id="bank" value="2">
                                        Bank
                                    </label>
                                </div>

                                <div class="mb-2" v-if="account_id =='2'">
                                    <select name="account_number_id" v-model="account_number_id" id="" class="form-select">
                                        <option v-for="ac in accounts[1].numbers" :value="ac.id" :key="ac.id">
                                            {{ ac.value }}
                                        </option>
                                    </select>
                                    <div class="mt-2">
                                        <label class="mb-1" for="">Trx ID</label>
                                        <input type="text" v-model="trx_id" class="form-control" name="trx_id">
                                    </div>
                                </div>

                                <div class="d-flex gap-1 flex-wrap">
                                    <button type="button" @click.prevent="update_order({account_id, account_number_id, trx_id, shipping_charge, total_discount, total_paid, selected_products})"  class="btn btn-outline-info" >
                                        <i class="fa fa-paper-plane"></i>
                                        Update Order
                                    </button>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>
                <div class="card-footer text-center">

                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { mapActions, mapGetters, mapMutations, mapState } from 'vuex'
import InputField from '../../components/InputField.vue'
import UserManagementModal from "../../users/components/UserManagementModal.vue"
/** store and route prefix for export object use */
import PageSetup from './PageSetup';
import ProductManagementModal from "../products/components/ManagementModal.vue"

const {route_prefix, store_prefix} = PageSetup;

export default {
    components: { InputField, UserManagementModal, ProductManagementModal},
    data: function(){
        return {
            /** store prefix for JSX */
            store_prefix,
            route_prefix,
            shipping_charge: 0,
            total_discount: 0,
            discount_on_product: 0,
            total_paid: 0,
            total_discount_percent: 0,
            search_key: '',
            account_id: 2,
            account_number_id: 1,
            trx_id: null,

            selected_products: [],
        }
    },
    created: async function () {
        this.clear_selected_admin_products(false);
        document.querySelector('html').classList.add('nav-hide');
        this.clear_cart();
        await this.fetch_payment_accounts();
        await this.fetch_branch_product_for_order();
        await this.fetch_category();
        await this[`fetch_${store_prefix}`]({id: this.$route.params.id});

        this.$watch('p_search_key',(n,o)=>{
            this.fetch_branch_product_for_order();
        })
        this.$watch('total_discount_percent',(n,o)=>{
            let total_payable_amount = +this.tota_order_price + +this.shipping_charge;
            let discount_amount = (total_payable_amount * parseInt(n) / 100 );
            this.total_discount = discount_amount;
        })
        this.$watch('order_carts',(n,o)=>{
            // console.log(n);
            this.discount_on_product = n.reduce((total,i)=>total+=((i.sales_price-i.current_price)*i.qty),0);
        })
    },
    watch: {
        "data" : {
            handler: function(order){
                // console.log(order);

                let temp = [];
                order.order_details.forEach(item => {
                    let product = {...item.product};
                    product.qty = item.qty;

                    product.product_price = item.product_price;
                    product.sales_price = item.sales_price;
                    product.discount_price = item.discount_price;
                    product.discount_parcent = item.discount_percent;

                    temp.push(product);
                    this.set_selected_admin_products(product);
                });

                this.selected_products = temp;
                this.shipping_charge = order.delivery_charge;
                this.total_paid = order.total_paid;

                if(order.payment_records && order.payment_records.length){
                    if(order.payment_records[0].payment_method == 'cash'){
                        this.account_id = 1;
                    }else{
                        this.account_id = 2;
                        this.trx_id = order.payment_records[0].trx_id;
                    }
                }
            },
            deep: true,
        },
        "tota_order_price": {
            handler: function(v){
                if(this.data.delivery_charge){
                    this.shipping_charge = this.data.delivery_charge;
                }
                else if(v <= 10000){
                    this.shipping_charge = this.shipping_charge?this.shipping_charge:this.data.delivery_charge;
                }else{
                    let mod_price = ((v - 10000 )/ 5000) * 50;
                    this.shipping_charge = 100 + mod_price;
                }
                // console.log(v);
            }
        }
    },
    methods: {
        ...mapActions([
            `update_${store_prefix}`,
            `fetch_${store_prefix}`,
            `fetch_payment_accounts`,
        ]),
        ...mapActions([
            `store_${store_prefix}`,
            `fetch_branch_product_for_order`,
        ]),
        ...mapActions({
            add_to_cart: 'admin_oder_cart_add',
            remove_cart: "remove_product_from_cart",
            store_order: "store_admin_order",
            update_order: "update_admin_order",
            fetch_category: "fetch_category_all_json",
            clear_cart: "clear_cart",
        }),
        ...mapMutations({
            set_p_search_key: "set_branch_p_search_key",
            set_branch_product_category: "set_branch_product_category",
            set_admin_order_discount: "set_admin_order_discount",
            set_admin_paid_amount: "set_admin_paid_amount",
            set_selected_admin_products: "set_selected_admin_products",
            clear_selected_admin_products: "set_clear_selected_admin_products",
        }),
        call_store: function(name, params=null){
            this[name](params)
        },
        bn_price: function(price){
            return price.toString().enToBn();
        },
        balance_fraction: function(){
            this.shipping_charge = 100 - +this.tota_order_price % 1;
            this.shipping_charge = +(this.shipping_charge.toFixed(2));

            if(this.total_paid > this.total_due){
                this.shipping_charge += Math.abs(this.total_due);
            }
        },
        update_discount: function(product){
            let discount_price = product.cost * product.discount_parcent / 100;
            product.sales_price = product.cost - discount_price;
            product.discount_flat = discount_price
        }
    },
    computed: {
        ...mapGetters({
            'products': "get_branch_product_for_order",
            'p_search_key': "get_branch_p_search_key",
            "order_carts": "get_admin_oder_cart",
            "tota_order_price": "get_admin_oder_cart_total",
            "all_categories": "get_category_all_json",
            "discount": "get_admin_order_discount",
            "admin_cart_total": "get_admin_cart_total",
            "admin_due_amount": "get_admin_due_amount",
            'data': `get_${store_prefix}`,
        }),
        "total_due": function(){
            return +this.tota_order_price + +this.shipping_charge  - +this.total_discount - +this.total_paid;
        },

        tota_order_price: function(){
            let total = this.selected_products.reduce((t,i)=>{
                t = t + (+i.qty * +i.sales_price);
                return t;
            }, 0)
            return total;
        },

        ...mapGetters({
            accounts: `get_payment_accounts`,
            selected_order_products: `get_admin_product_selected`,
        }),
    }
};
</script>

<style>
</style>
