<template>
    <div class="container-fluid">
        <div class="py-2">
            <div class="mt-2 d-flex gap-3 align-items-end">
                <div for="">
                    <date-field :label="'From'" :keyup_handler="(v)=>start_date=v"></date-field>
                </div>
                <div>
                    <date-field :label="'To'" :keyup_handler="(v)=>end_date=v"></date-field>
                </div>
                <div>
                    <button class="btn btn-outline-info mb-1" @click.prevent="get_branch_product_report">Submit</button>
                </div>
            </div>
        </div>
        <div class="card list_card">
            <div class="card-header">
                <h4>
                    Sales Statement Report
                </h4>
                <div class="search">

                </div>
                <div class="btns d-flex gap-2 align-items-center">
                    <button class="btn btn-outline-info"
                        @click.prevent="export_data({
                            data: report,
                            name:'sales_statements',
                            cols: [
                                'invoice_date',
                                'branch_name',
                                'products_count',
                                'total_price',
                            ]
                        })">
                        Export
                    </button>
                </div>
            </div>
            <div class="table-responsive card-body text-nowrap" style="max-height: calc(100vh - 440px);">
                <table class="table table-hover table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Sales Id</th>
                            <th>Branch</th>
                            <th>Qty</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        <tr v-for="(item, index) in report" :key="item.id">
                            <td>
                                {{ item.sales_id }}
                            </td>
                            <td>
                                <div class="text-start">
                                    {{ item.user.first_name }}
                                </div>
                            </td>
                            <td>
                                {{ item.products_count }}
                            </td>
                            <td>
                                {{ item.total_price }}
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td> {{ total_qty }} </td>
                            <td> {{ total_price }} </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="card-footer py-1 border-top-0">

            </div>
        </div>

    </div>
</template>

<script>
import { mapActions, mapGetters, mapMutations } from 'vuex';
import UserManagementModal from "../users/components/UserManagementModal.vue"

export default {
    components: { UserManagementModal },
    data: function(){
        return {
            report: [],
            total_qty: 0,
            total_price: 0,
            start_date: moment().format('YYYY-MM-DD'),
            end_date: moment().format('YYYY-MM-DD'),
        }
    },
    created: function(){
        // this.get_branch_product_report();
    },
    methods: {
        ...mapActions([
            'export_data',
        ]),
        get_branch_product_report: function(){
            axios.post('/common/sales-statements',{
                'from': this.start_date,
                'to': this.end_date,
            }).then(res=>{
                this.report = res.data.orders;
                this.total_qty = res.data.total_qty;
                this.total_price = res.data.total_price;
            })
        }
    },
    computed: {
        ...mapGetters({
        }),
    }
}
</script>

<style>

</style>

