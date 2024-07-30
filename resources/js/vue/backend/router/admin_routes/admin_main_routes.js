import AdminDashboard from "../../views/AdminDashboard.vue"
import AdminLayout from "../../views/AdminLayout.vue"
import BranchProductReport from "../../views/common_pages/BranchProductReport.vue"
import order from "./sub_routes/order";
import account from "./sub_routes/account";
import account_category from "./sub_routes/account_category";
import account_bank from "./sub_routes/account_bank";
import product from "./sub_routes/product";
import product_category from "./sub_routes/product_category";
import translator from "./sub_routes/translator";
import writer from "./sub_routes/writer";
import payment_request from "./sub_routes/payment_request";
import settings from "./sub_routes/settings";
import banner from "./sub_routes/banner";

export default {
    path: "/admin",
    component: AdminLayout,
    children: [
        {
            path: "",
            name: "AdminDashboard",
            component: AdminDashboard,
        },
        order,
        account,
        account_category,
        account_bank,
        product,
        product_category,
        translator,
        writer,
        payment_request,
        settings,
        banner,
        {
            path: 'branch-report',
            name: "BranchProductReport",
            component: BranchProductReport,
        }
    ],
};
