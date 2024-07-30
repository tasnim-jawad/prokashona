import axios from "axios";
import management_router from "../../../router/router";
import StoreModule from "../schema/StoreModule";
import moment from "moment";

let test_module = new StoreModule('admin_order', 'admin/order', 'AdminOrder');
const { store_prefix, api_prefix, route_prefix } = test_module;

// state list
const state = {
    ...test_module.states(),
    orderByAsc: false,
    order_type: 'id',
    order_date: null,

    admin_product_for_order: {},
    admin_p_search_key: '',
    admin_oder_cart: [],

    admin_order_discount: 0,
    admin_cart_total: 0,
    admin_paid_amount: 0,
    admin_due_amount: 0,
};

// get state
const getters = {
    ...test_module.getters(),
    get_order_date: (state) => state.order_date,
    get_admin_product_for_order: (state) => state.admin_product_for_order,
    get_admin_p_search_key: (state) => state.admin_p_search_key,
    get_admin_oder_cart: (state) => state.admin_oder_cart,
    get_admin_oder_cart_total: (state) => state.admin_oder_cart.reduce((t, i) => t += i.total_price, 0),
    get_admin_order_discount: (state) => state.admin_order_discount,
    get_admin_cart_total: (state) => state.admin_cart_total,
    get_admin_paid_amount: (state) => state.admin_paid_amount,
    get_admin_due_amount: (state) => state.admin_due_amount,
};

// actions
const actions = {
    ...test_module.actions(),

    [`fetch_${store_prefix}s`]: async function ({ state }) {
        let url = `/${api_prefix}/all?page=${state[`${store_prefix}_page`]}&order_type=${state.order_type}&status=${state[`${store_prefix}_show_active_data`]}&paginate=${state[`${store_prefix}_paginate`]}`;
        url += `&orderBy=${state.orderByCol}`;
        if (state.orderByAsc) {
            url += `&orderByType=ASC`;
        } else {
            url += `&orderByType=DESC`;
        }
        if (state[`${store_prefix}_search_key`]) {
            url += `&search_key=${state[`${store_prefix}_search_key`]}`;
        }
        if (state[`order_date`]) {
            url += `&order_date=${state[`order_date`]}`;
        }
        await axios.get(url).then((res) => {
            this.commit(`set_${store_prefix}s`, res.data);
        });
    },

    // [`fetch_admin_product_for_order`]: async ({commit,dispatch,getters,rootGetters,rootState,state},page=1) => {
    //     let s_keys = state.admin_p_search_key.length ? `&search_key=${state.admin_p_search_key}`:'';
    //     let products = await axios.get('/all-products?page='+page+s_keys);
    //     commit('set_get_admin_product_for_order', products.data);
    // },

    [`fetch_${store_prefix}`]: async function ({ state }, { id, render_to_form }) {
        let url = `/${api_prefix}/${id}`;
        await axios.get(url).then((res) => {
            // console.log(res.data);
            res.data.order_details.forEach(el => {
                // console.log(el);
                el.total_price = el.discount_price ? el.discount_price * el.qty : el.sales_price * el.qty;
                el.current_price = el.discount_price ? el.discount_price : el.product_price;
                el.id = el.product_id;

                if (el.product.discount_info.discount_percent) {
                    el.product.discount_info.discount_percent = el.discount_percent;
                }
                // el.discount_percent = el.discount_percent? el.discount_percent : 0;
                // if (!el.discount_percent && el.product.discount_info && el.product.discount_info.discount_price > 0) {
                //     el.current_price = el.product.discount_info?.discount_price;
                //     el.discount_percent = el.product.discount_info?.discount_percent
                // }

            });
            state.admin_oder_cart = res.data.order_details;
            state.admin_order = res.data;
        });

        if (render_to_form) {
            window.set_form_data(".admin_form", data);
        }
    },

    [`store_admin_order`]: async function ({ state, rootGetters, dispatch, commit }, {
        trx_id, account_number_id, shipping_charge, total_discount, total_paid, account_id,
        selected_products
    }) {
        let customer_id = rootGetters.get_user_selected[0]?.id;
        let carts = [...selected_products];
        carts = carts.map(i => {
            return {
                id: i.id,
                product_name: i.product_name,
                qty: i.qty,
                current_price: i.cost,
                sales_price: i.sales_price,
                total_price: i.total_price * i.qty,
                discount_percent: i.discount_parcent,
            }
        });
        // console.log(carts);

        let cconfirm = await window.s_confirm("submit order");
        if (cconfirm) {
            axios.post('/admin/order/store-order', {
                carts,
                customer_id,
                type: 'create',
                order_id: state.admin_order?.id,
                discount: total_discount,
                shipping_charge,
                total_paid,
                trx_id,
                account_id,
                account_number_id,
            })
                .then(res => {
                    // console.log(res.data);
                    dispatch('clear_cart');
                    commit('set_clear_selected_admin_products');
                    state.admin_oder_cart = [];
                    window.s_alert(`order created successfully.`);
                    setTimeout(() => {
                        management_router.push({ name: `Details${route_prefix}`, params: { id: res.data.id } })
                    }, 200);
                })
        }
    },

    [`update_admin_order`]: async function ({ state, dispatch }, {
            account_id, account_number_id, trx_id,
            shipping_charge, total_discount, total_paid, selected_products
        })
    {
        let carts = [...selected_products];
        carts = carts.map(i => {
            return {
                id: i.id,
                product_name: i.product_name,
                qty: i.qty,
                current_price: i.cost,
                sales_price: i.sales_price,
                total_price: i.total_price * i.qty,
                discount_percent: i.discount_parcent,
            }
        });
        // console.log(carts);
        let cconfirm = await window.s_confirm("update order");
        if (cconfirm) {
            axios.post('/admin/order/update-order', {
                order_id: state.admin_order.id,
                carts,
                shipping_charge,
                discount: total_discount,
                total_paid,
                total_discount,
                trx_id,
                account_id,
                account_number_id,
            })
                .then(res => {
                    // console.log(res.data);
                    dispatch('clear_cart');
                    state.admin_oder_cart = [];
                    window.s_alert(`order updated successfully.`);
                    setTimeout(() => {
                        management_router.push({ name: `Details${route_prefix}`, params: { id: res.data.id } })
                    }, 200);
                })
        }
    },

    [`update_order_status`]: async function ({ state, dispatch, rootState }, { status, order_id }) {
        let cconfirm = await window.s_confirm("update status into " + status);
        if (state.admin_order?.id) {
            order_id = state.admin_order.id;
        }
        if (cconfirm) {
            axios.post('/admin/order/update-order-status', { status, order_id })
                .then(res => {
                    let orders = rootState.admin_order_modules.admin_orders;
                    let product = orders?.data?.find(i => i.id == res.data.id);
                    // console.log(product, status);
                    if (product) {
                        product.order_status = status;
                        dispatch('fetch_admin_order', { id: product.id })
                    }
                    window.s_alert(`order status updated successfully.`);
                })
        }
    },

    [`admin_delete_branch_payment`]: async function ({ state, dispatch }, { payment }) {
        console.log(payment);

        let cconfirm = await window.s_confirm("delete payment");
        if (cconfirm) {
            axios.post('/admin/order/delete-payment', { payment_id: payment.id })
                .then(res => {
                    console.log(res.data);
                    // state.admin_oder_cart  = [];
                    window.s_alert(`payement deleted successfully.`);
                    dispatch("fetch_admin_order", { id: state.admin_order.id, });
                })
        }

    },

    [`admin_approve_branch_payment`]: async function ({ state, dispatch }, { payment }) {
        console.log(payment);
        if (!payment.approved) {
            let cconfirm = await window.s_confirm("apporve payment");
            if (cconfirm) {
                axios.post('/admin/order/approve-payment', { payment_id: payment.id })
                    .then(res => {
                        console.log(res.data);
                        // state.branch_oder_cart  = [];
                        window.s_alert(`payement approved successfully.`);
                        dispatch("fetch_admin_order", { id: state.admin_order.id, });
                    })
            }
        } else {
            window.s_alert(`This transaction is approved and cannot be dismissed.`, 'warning');
        }
    },

    [`admin_oder_cart_add`]: function ({ state, commit }, { product, qty, commission }) {
        let products = [...state.admin_oder_cart];
        let cart_product = products.find((p) => p.product_id == product.id);
        if (cart_product) {
            if (+qty > 0) {
                cart_product.qty = qty;
            } else {
                cart_product.qty++;
            }
        } else {
            product.qty = 1;
            product.product_id = product.id;
            product.product = product;
            product.discount_percent = product.discount_info.discount_percent;
            cart_product = product;
            products.push(cart_product);
        }

        cart_product.current_price = product.sales_price;
        cart_product.total_price = product.sales_price * cart_product.qty;

        if (commission) {
            cart_product.discount_percent = commission;
            let d_price = product.sales_price - (cart_product.current_price * commission / 100);
            cart_product.total_price = cart_product.qty * d_price;
            cart_product.current_price = d_price;
        }
        else if (cart_product.discount_percent) {
            let d_price = product.sales_price - (cart_product.current_price * cart_product.discount_percent / 100);
            cart_product.total_price = cart_product.qty * d_price;
            cart_product.current_price = d_price;
        }

        state.admin_oder_cart = products;
        commit('set_admin_cart_total')
        commit('calc_discount_amount')
    },

    [`generete_sales_id`]: async function ({ state, dispatch }, order) {
        if (!order.sales_id) {
            let cconfirm = await window.s_confirm("Generate Sales Id");
            if (cconfirm) {
                axios.post('/admin/order/generate-sales-id', { order_id: order.id })
                    .then(res => {
                        window.s_alert(`sales id created successfully.`);
                        dispatch("fetch_admin_order", { id: state.admin_order.id, });
                    })
            }
        } else {
            window.s_alert(`Sales id exists.`, 'warning');
        }
    },

    [`remove_product_from_cart`]: function ({ state, commit }, { product }) {
        let products = [...state.admin_oder_cart];
        products = products.filter(i => i.id != product.id);
        state.admin_oder_cart = products;
        commit('set_admin_cart_total');
        commit('calc_discount_amount');
    },

    [`clear_cart`]: function ({ state, commit }) {
        state.admin_oder_cart = [];
    },

    [`admin_receive_due`]: async function ({ state, dispatch }, { form }) {
        let form_data = new FormData(event.target);
        form_data.append('order_id', state.admin_order.id);
        let cconfirm = await window.s_confirm("Submit payment");
        if (cconfirm) {
            axios.post('/admin/order/receive-due', form_data)
                .then(res => {
                    console.log(res.data);
                    state.admin_oder_cart = [];
                    window.s_alert(`Transaction completed.`);
                    dispatch("fetch_admin_order", { id: state.admin_order.id, });
                })
        }
    },

    ['delete_order']: async function ({ state, dispatch }, order) {
        let cconfirm = await window.s_confirm("Delete Order");
        if (cconfirm) {
            await axios.post('/admin/order/destroy', { id: order.id })
                .then(res => {
                    state.order = null;
                    window.s_alert(`Order Deleted.`);
                    management_router.push({ name: 'BranchOrder' })
                })
        }
    }

}

// mutators
const mutations = {
    ...test_module.mutations(),
    set_admin_order_order_date: function (state, data) {
        state.order_date = data;
        this.dispatch(`fetch_${store_prefix}s`);
    },
    set_get_admin_product_for_order: (state, data) => {
        state.admin_product_for_order = data;
    },
    set_admin_p_search_key: (state, data) => {
        state.admin_p_search_key = data;
    },
    set_order_type: (state, order_type) => state.order_type = order_type,
    set_admin_paid_amount: function (state, admin_paid_amount) {
        state.admin_paid_amount = admin_paid_amount;
        this.commit('set_admin_due_amount');
    },
    set_admin_due_amount: function () {
        state.admin_due_amount = state.admin_cart_total - state.admin_paid_amount;
    },
    set_admin_cart_total: function () {
        state.admin_cart_total = state.admin_oder_cart.reduce((t, i) => t += i.total_price, 0);
        this.commit('set_admin_due_amount');
    },
    set_admin_order_discount: function (state, event) {
        let admin_order_discount = event.target.value;
        if (admin_order_discount > 100) {
            admin_order_discount = 100;
        }
        if (admin_order_discount < 0) {
            admin_order_discount = 0;
        }
        event.target.value = admin_order_discount;
        state.admin_order_discount = admin_order_discount;
        this.commit('set_admin_cart_total')
        this.commit('calc_discount_amount')
    },
    calc_discount_amount: function (state) {
        let total = state.admin_cart_total;
        let admin_order_discount = state.admin_order_discount
        if (admin_order_discount > 0) {
            state.admin_cart_total = Math.round(total - ((total * admin_order_discount) / 100));
        } else {
            state.admin_cart_total = Math.round(total);
        }
        this.commit('set_admin_due_amount');
    }
};

export default {
    state,
    getters,
    actions,
    mutations,
};
