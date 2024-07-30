import axios from "axios";
import management_router from "../../../router/router";
import StoreModule from "../schema/StoreModule";
import moment from "moment";

let test_module = new StoreModule('admin_banner', 'admin/banner', 'AdminBanner');
const { store_prefix, api_prefix, route_prefix } = test_module;

// state list
const state = {
    ...test_module.states(),
    orderByAsc: false,
    order_type: 'id',
};

// get state
const getters = {
    ...test_module.getters(),
};

// actions
const actions = {
    ...test_module.actions(),
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
