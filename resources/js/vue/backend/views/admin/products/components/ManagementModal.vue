<template>
    <div class="multiple_select_body" :id="`writer_id`">
        <div class="multiple_select_data"
            @click="call_store(`set_${store_prefix}_show_management_modal`,true)">
            <div v-for="item in selected" :key="item.id" class="item">
                {{ item.product_name }}
            </div>
            <div class="btn btn-sm btn-outline-danger" v-if="!this[`get_${store_prefix}_selected`].length">
                no data selected
            </div>
        </div>

        <div class="multiple_select_modal" v-if="this[`get_${store_prefix}_show_management_modal`]">
            <div class="multiple_select_modal_backdrop" @click="call_store(`set_${store_prefix}_show_management_modal`,false)"></div>
            <div class="multiple_select_modal_body custom_scroll">
                <div class="header">
                    <div class="search">
                        <input @keyup="call_store(`set_${store_prefix}_search_key`,($event.target.value))" type="text" class="rounded-pill form-control">
                    </div>
                    <div class="action_btns">
                        <a @click.prevent="call_store(`set_clear_selected_${store_prefix}s`)" v-if="this[`get_${store_prefix}_selected`].length" href="#" class="btn rounded-pill btn-outline-danger me-2"><i class="fa fa-trash"></i> remove selected</a>
                        <!-- <a @click.prevent="call_store(`set_${store_prefix}_show_create_canvas`,true)" href="#" class="btn rounded-pill btn-outline-primary"><i class="fa fa-pencil"></i> create</a> -->
                    </div>
                </div>
                <div class="selected">
                    <div class="item" v-for="item in this[`get_${store_prefix}_selected`]" :key="item.id">
                        <button @click.prevent="call_store(`set_selected_${store_prefix}s`,item)" class="btn rounded-pill btn-outline-secondary" type="button">
                            <span>
                                {{ item.product_name }}
                            </span>
                            <span>|</span>
                            <i class="fa fa-times"></i>
                        </button>
                    </div>
                </div>
                <div class="data_list custom_scroll table-responsive text-nowrap">
                    <table class="table table-hover table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th><input @click="call_store(`set_select_all_admin_products`)" type="checkbox" class="form-check-input"></th>
                                <table-th :sort="true" :ariaLable="'id'" :tkey="'id'" :title="'ID'" />
                                <table-th :sort="false" :tkey="'thumb_image'" :title="'Image'" />
                                <table-th :sort="true" :tkey="'product_name'" :title="'Product Name'" />
                                <table-th :sort="true" :tkey="'sales_price'" :title="'Price'" />
                                <!-- <table-th :sort="false" :tkey="''" :title="'Current Price'" /> -->
                                <table-th :sort="false" :tkey="''" :title="'Stock'" />
                                <!-- <th aria-label="actions">Actions</th> -->
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            <tr v-for="item in this[`get_${store_prefix}s`].data" :key="item.id">
                                <td>
                                    <input v-if="check_if_data_is_selected(item)" :data-id="item.id" checked @change="call_store(`set_selected_${store_prefix}s`,item)" type="checkbox" class="form-check-input">
                                    <input v-else @change="call_store(`set_selected_${store_prefix}s`,item)" type="checkbox" class="form-check-input">
                                </td>
                                <td>{{ item.id }}</td>
                                <td>
                                    <img :src="item.thumb_image_url" style="width: 30px;" alt="">
                                </td>
                                <td>
                                    <span class="text-warning cursor_pointer" @click.prevent="call_store(`set_${store_prefix}`,item)">
                                        {{ item.product_name }}
                                    </span>
                                </td>
                                <td>{{ item.sales_price }} ৳</td>
                                <!-- <td>
                                    <b class="text-info">
                                        {{ item.discount_info.discount_price ? item.discount_info.discount_price: item.sales_price.toFixed(2) }} ৳
                                    </b>
                                </td> -->
                                <td>{{ item.stock }}</td>
                                <!-- <td>
                                    <div class="table_actions">
                                        <a href="#" @click.prevent="()=>''" class="btn btn-sm btn-outline-secondary">
                                            <i class="fa fa-gears"></i>
                                        </a>
                                        <ul>
                                            <li>
                                                <a href="" @click.prevent="
                                                        call_store(`set_${store_prefix}`,item),
                                                        call_store(`set_${store_prefix}_show_edit_canvas`,true)
                                                    ">
                                                    <i class="fa text-info fa-edit"></i>
                                                    Edit
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </td> -->
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="footer_modal d-flex justify-content-between align-items-start">
                    <pagination :data="this[`get_${store_prefix}s`]" :limit="5" :size="'small'" :show-disabled="true" :align="'left'"
                        @pagination-change-page="handle_pagination">
                        <span slot="prev-nav"><i class="fa fa-angle-left"></i> Previous</span>
                        <span slot="next-nav">Next <i class="fa fa-angle-right"></i></span>
                    </pagination>
                    <button type="button" @click.prevent="call_store(`set_${store_prefix}_show_management_modal`,(false))" href="#" class="btn rounded-pill btn-outline-secondary">
                        <i class="fa fa-floppy-disk"></i>
                        Save & Close
                    </button>
                </div>
            </div>
        </div>

        <!-- <create-canvas></create-canvas> -->
        <!-- <edit-canvas></edit-canvas> -->
    </div>
</template>

<script>
import { mapActions, mapGetters, mapMutations } from 'vuex'
import CreateCanvas from '../CreateCanvas.vue';
import EditCanvas from '../EditCanvas.vue';
import TableTh from './TableTh.vue';

/** store and route prefix for export object use */
import PageSetup from '../PageSetup';
const {route_prefix, store_prefix} = PageSetup;

export default {
    components: { TableTh, CreateCanvas, EditCanvas },
    props: {
        'select_qty': Number,
    },
    data: function(){
        return {
            /** store prefix for JSX */
            store_prefix,
            route_prefix,
        }
    },
    created: function(){
        this[`set_${this.store_prefix}_paginate`](9);
        if(this.select_qty){
            this[`set_${this.store_prefix}_modal_selected_qty`](this.select_qty)
        }
    },
    methods: {
        ...mapActions([`fetch_${store_prefix}s`]),
        ...mapMutations([
            `set_${store_prefix}_paginate`,
            `set_${store_prefix}_page`,
            `set_${store_prefix}_search_key`,
            `set_${store_prefix}_orderByCol`,
            `set_${store_prefix}`,
            `set_selected_${store_prefix}s`,
            `set_select_all_${store_prefix}s`,
            `set_clear_selected_${store_prefix}s`,
            `set_${store_prefix}_show_selected`,
            `set_${store_prefix}_modal_selected_qty`,

            `set_${store_prefix}_show_create_canvas`,
            `set_${store_prefix}_show_edit_canvas`,
            `set_${store_prefix}_show_management_modal`,
            `set_select_all_admin_products`,
        ]),

        call_store: function(name, params=null){
            this[name](params)
        },
        handle_pagination: function(page=1){
            return this[`set_${store_prefix}_page`](page);
        },
        check_if_data_is_selected: function(user){
            let check_index = this[`get_${store_prefix}_selected`].findIndex((i) => i.id == user.id);
            if(check_index >= 0){
                return true;
            }else{
                return false;
            }
        },
    },
    computed: {
        ...mapGetters([
            `get_${store_prefix}s`,
            `get_${store_prefix}_selected`,
            `get_${store_prefix}_show_management_modal`,
            `get_${store_prefix}_show_create_canvas`,
        ]),
        ...mapGetters({
            data: `get_${store_prefix}s`,
            selected:`get_${store_prefix}_selected`,
        }),
    }
};
</script>

<style>
</style>
