import axios from "axios";
import management_router from "../../../router/router";
import StoreModule from "../schema/StoreModule";

let test_module = new StoreModule('admin_product', 'admin/product', 'AdminProduct');
const { store_prefix, api_prefix, route_prefix } = test_module;

// state list
const state = {
    ...test_module.states(),
    orderByAsc: true,
};

// get state
const getters = {
    ...test_module.getters(),
};

// actions
const actions = {
    ...test_module.actions(),

    /** override store */
    [`store_${store_prefix}`]: function ({ state, getters, commit, rootState }) {

        const { form_values, form_inputs, form_data } = window.get_form_data('.create_form');
        const { get_category_selected: category } = getters;
        const { get_admin_writer_selected: writer } = getters;
        const { get_admin_translator_selected: translator } = getters;

        category.forEach(i => form_data.append('category_id[]', i.id));
        writer.forEach(i => form_data.append('writer_id[]', i.id));
        translator.forEach(i => form_data.append('translator_id[]', i.id));

        axios.post(`/${api_prefix}/store`, form_data)
            .then(res => {
                $('.create_form input').val('');

                rootState.admin_writer_modules.admin_writer_selected = [];
                rootState.admin_translator_modules.admin_translator_selected = [];
                rootState.production_product_category_modules.category_selected = [];

                if (res.data.status == 'success') {
                    window.s_alert('new product has been created');
                } else {
                    window.s_alert(res.data.message, 'error');
                    console.log(res.data.error);
                }
                management_router.push({ name: `Edit${route_prefix}`, params: { id: res.data.product.id } })
            })
            .catch(error => {

            })
    },

    /** override update */
    [`update_${store_prefix}`]: function ({ state, getters, commit, rootState }) {
        const { form_values, form_inputs, form_data } = window.get_form_data('.create_form');
        const { get_category_selected: category } = getters;
        const { get_admin_writer_selected: writer } = getters;
        const { get_admin_translator_selected: translator } = getters;

        form_data.append('id', state.admin_product.id);
        category.forEach(i => form_data.append('category_id[]', i.id));
        writer.forEach(i => form_data.append('writer_id[]', i.id));
        translator.forEach(i => form_data.append('translator_id[]', i.id));

        axios.post(`/${api_prefix}/update`, form_data)
            .then(res => {

                if (res.data.status == 'success') {
                    window.s_alert('product has been updated');
                } else {
                    window.s_alert(res.data.message, 'error');
                    console.log(res.data.error);
                }
            })
            .catch(error => {

            })
    },

    [`fetch_${store_prefix}`]: async function ({ state, rootState }, { id, render_to_form }) {
        let url = `/${api_prefix}/${id}`;
        let res = await axios.get(url);
        let data = res.data;

        this.commit(`set_${store_prefix}`, data);

        window.set_form_data(".admin_form", data);

        rootState.admin_writer_modules.admin_writer_selected = data.writers;
        rootState.admin_translator_modules.admin_translator_selected = data.translators;
        rootState.production_product_category_modules.category_selected = data.categories;

        setTimeout(() => {
            data.categories.forEach(i => {
                if(document.querySelector(`input[data-id="${i.id}"]`)){
                    document.querySelector(`input[data-id="${i.id}"]`).checked = true;
                }
            });
        }, 500);
    },

    /** store discount */
    [`store_discount_${store_prefix}`]: async function ({ state, dispatch, getters, commit, rootState }) {

        const { form_values, form_inputs, form_data } = window.get_form_data('.create_form');
        let { get_admin_product_selected: product } = getters;
        product = product[0] || {};
        form_data.append('product_id', (product.id) || '');

        let cconfirm = await window.s_confirm("confirm to upload.");
        if (cconfirm) {
            axios.post(`/${api_prefix}/store-discount`, form_data)
                .then(async (res) => {
                    // $('.create_form input').val('');
                    window.s_alert('product discount has been set.');
                    await dispatch('fetch_admin_products');
                    await dispatch('fetch_admin_product',{id: res.data.product_id});
                    rootState.admin_product_modules.admin_product_selected = [product];
                    // management_router.push({name:`Edit${route_prefix}`,params:{id:res.data.product.id}})
                })
                .catch(error => {

                })
        }
    },

    /** store stock */
    [`store_stock_${store_prefix}`]: async function ({ state, getters, commit, rootState }) {

        const { form_values, form_inputs, form_data } = window.get_form_data('.create_form');
        let { get_admin_product_selected: product } = getters;
        product = product[0] || {};
        form_data.append('product_id', (product.id) || '');
        form_data.append('main_price', (product.sales_price) || '');

        let cconfirm = await window.s_confirm("confirm to upload.");
        if (cconfirm) {
            axios.post(`/${api_prefix}/store-stock`, form_data)
                .then(res => {
                    $('.create_form input').val('');
                    rootState.admin_product_modules.admin_product_selected = [];

                    if (res.data.status == 'success') {
                        window.s_alert('product stock has been updated.');
                    }
                    // management_router.push({name:`Edit${route_prefix}`,params:{id:res.data.product.id}})
                })
                .catch(error => {

                })
        }
    },

    [`admin_store_production_cost`]: function (
        { commit, dispatch, getters, rootGetters, rootState, state },
        target
    ) {
        let { get_admin_product_selected: products } = getters;
        let formData = new FormData(target);
        formData.append("product_id", products[0]?.id);

        console.log(products);
        axios.post(`/${api_prefix}/store-cost`,formData)
            .then(res=>{
                $('.create_form input').val('');
                rootState.production_product_modules.production_product_selected = [];
                window.s_alert('product price updated.');
                management_router.push({name:`All${route_prefix}`})
            })
            .catch(error=>{

            })
    },

    [`admin_add_to_top_product`]: function ({ commit, dispatch, getters, rootGetters, rootState, state }, {id, value}) {

        axios.post(`/${api_prefix}/add-to-top-product`,{id, value})
            .then(res=>{
                window.s_alert('product set as top');
            })
            .catch(error=>{

            })
    },

    [`admin_add_to_public`]: function ({ commit, dispatch, getters, rootGetters, rootState, state }, {id, value}) {

        axios.post(`/${api_prefix}/add-to-public`,{id, value})
            .then(res=>{
                window.s_alert('product added to public');
            })
            .catch(error=>{

            })
    },

}

// mutators
const mutations = {
    ...test_module.mutations(),
};

export default {
    state,
    getters,
    actions,
    mutations,
};
