import axios from "axios";
import StoreModule from "../schema/StoreModule";

let test_module = new StoreModule("category", "category", "Category");
const { store_prefix, api_prefix, route_prefix } = test_module;

// state list
const state = {
    ...test_module.states(),
    [`${store_prefix}_show_nested_category_modal`]: false,
    [`${store_prefix}_all_json`]: [],
    [`${store_prefix}_all_json_nested`]: [],
};

// get state
const getters = {
    ...test_module.getters(),
    [`get_${store_prefix}_show_nested_category_modal`]: (state) => state[`${store_prefix}_show_nested_category_modal`],
    [`get_${store_prefix}_all_json`]: (state) => state[`${store_prefix}_all_json`],
    [`get_${store_prefix}_all_json_nested`]: (state) => state[`${store_prefix}_all_json_nested`],
};

// actions

const actions = {
    ...test_module.actions(),
    [`fetch_${store_prefix}_all_json`]: async function ({ commit }, payload) {
        await axios.get("/category/all-json").then((res) => {
            commit(`set_${store_prefix}_all_json`, res.data);
        });
    },
    [`fetch_${store_prefix}_check_exists`]: async function ({state, commit }, url) {
        let form_data = {
            url,
        };
        let category = state[`${store_prefix}`];
        if(category){
            form_data.category = category;
        }

        let res = await axios.post("/category/check-exists",form_data);
        return res.data;
    },
    [`fetch_${store_prefix}_add_to_top_cat`]: async function ({state, commit }, id) {
        let res = await axios.post("/category/add-to-top-cat",{id})
        if(res.data){
            window.s_alert("added to top category");
        }else{
            window.s_alert("removed from top category");

        }
        return res.data;
    },
    [`fetch_${store_prefix}_add_to_public`]: async function ({state, commit }, id) {
        let res = await axios.post("/category/add-to-is-public",{id})
        if(res.data){
            window.s_alert("added to public");
        }else{
            window.s_alert("removed from public");

        }
        return res.data;
    },
    generateSlug: function(context, str_data="") {
        return str_data
          .toLowerCase() // convert to lowercase
          .replace(/[^a-z0-9]+/g, '-') // replace all non-alphanumeric characters with hyphens
          .replace(/(^-|-$)+/g, '') // remove hyphens from the beginning or end of the string
    }
};

// mutators
// console.log(test_module.mutations());
const mutations = {
    ...test_module.mutations(),
    [`set_${store_prefix}_show_nested_category_modal`]: (state, data) => {
        state[`${store_prefix}_show_nested_category_modal`] = data;
    },
    [`set_${store_prefix}_all_json`]: function(state, category){
        state[`${store_prefix}_all_json`] = category;
        // console.log(category,this);
        this.commit(`set_${store_prefix}_create_nested`);
    },
    [`set_${store_prefix}_create_nested`]: (state) => {
        let category = state[`${store_prefix}_all_json`];

        category.forEach(i=>(i.parent_id = parseInt(i.parent_id)) );
        let parents = category.filter((i) => !i.parent_id > 0);
        let childs = category.filter((i) => i.parent_id > 0);

        // console.log('all',category);
        state[`${store_prefix}_all_json_nested`] = parents
            .filter((i) => i.parent_id == 0)
            .map((p) => {
                return {
                    ...p,
                    child: find_childs(childs, p),
                };
            });

        // console.log('nested', state[`${store_prefix}_all_json_nested`]);

        function find_childs(arr, item) {
            let temp = arr
                .filter((i) => i.parent_id == item.id)
                .map((i) => {
                    return {
                        ...i,
                        child: find_childs(
                            arr.filter((i) => i.parent_id != item.id),
                            i
                        ),
                    };
                });
            return temp;
        }
    },
};

export default {
    state,
    getters,
    actions,
    mutations,
};
