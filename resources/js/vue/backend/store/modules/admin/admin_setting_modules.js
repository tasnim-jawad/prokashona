import axios from "axios";
import { debounce } from "debounce";
import StoreModule from "../schema/StoreModule";

let test_module = new StoreModule('admin_setting','admin/writer','AdminSetting');
const {store_prefix, api_prefix, route_prefix} = test_module;

// state list
const state = {
    ...test_module.states(),
    settings_keys: [],
    settings_values: {},
    payment_accounts: [],
};

// get state
const getters = {
    ...test_module.getters(),
    get_settings_values: (state)=>state.settings_values,
    get_settings_keys: (state)=>state.settings_keys,
    get_payment_accounts: (state)=>state.payment_accounts,
};

// actions
const actions = {
    ...test_module.actions(),

    get_settings: async function({state}){
        let res = await axios.post('/admin/settings/get-by-keys',{keys: state.settings_keys});
        let data = res.data;
        state.settings_values = data;
        // console.log(data);
    },

    fetch_payment_accounts: async function({state}){
        let res = await axios.get('/admin/settings/get-payment-accounts');
        let data = res.data;
        state.payment_accounts = data;
        // console.log(data);
    },

    set_settings: async function({state}, {id, value, index}){
        let form_data = new FormData();
        form_data.append('value',value);
        let res = await axios.post('/admin/settings/set/'+id,form_data);
        let data = res.data;
        window.s_alert(`setting updated`);
        state.settings_values[index]?.forEach(i => {
            if(i.id == data.id){
                i.setting_value = data.setting_value;
            }
        });
        // console.log(data);
    },
    set_payment_settings: async function({state}, {id, value, index}){
        let form_data = new FormData();
        form_data.append('value',value);
        form_data.append('id',id);
        let res = await axios.post('/admin/settings/payment-account-set',form_data);
        let data = res.data;
        window.s_alert(`setting updated`);
        state.payment_accounts.find(i=>i.id==data.account_id).numbers.find(i=>i.id==data.id).value = data.value;
        // state.settings_values[index]?.forEach(i => {
        //     if(i.id == data.id){
        //         i.setting_value = data.setting_value;
        //     }
        // });
        // console.log(data);
    },
}

// mutators
const mutations = {
    ...test_module.mutations(),
    set_settings_keys: function(state, settings_keys){
        state.settings_keys = settings_keys
    }
};

export default {
    state,
    getters,
    actions,
    mutations,
};
