import Layout from "../../../views/accountant/reports/Layout";

import IncomeLedger from "../../../views/accountant/reports/IncomeLedger.vue";
import ExpenseLedger from "../../../views/accountant/reports/ExpenseLedger.vue";
import Statements from "../../../views/accountant/reports/Statements.vue";
import ProductReport from "../../../views/superadmin/reports/ProductReport.vue";
import AllincomeExpense from "../../../views/common_pages/AllincomeExpense.vue";
import AllInStockProducts from "../../../views/common_pages/AllInStockProducts.vue";
import AllOutStockProducts from "../../../views/common_pages/AllOutStockProducts.vue";
// import BranchProductReport from "../../../views/common_pages/BranchProductReport.vue";

let prefix = "Super Admin Report"
export default {
    path: "report",
    component: Layout,
    props: {
        role_permissions: ["super_admin"],
        layout_title: `${prefix} Management`,
    },
    children: [
        {
            path: `income-ledger`,
            name: `SuperAdminIncomeLedger`,
            component: IncomeLedger,
        },
        {
            path: `expense-ledger`,
            name: `SuperAdminExpenseLedger`,
            component: ExpenseLedger,
        },
        {
            path: `statements`,
            name: `SuperAdminStatements`,
            component: Statements,
        },
        {
            path: `all-income-expense`,
            name: `SuperAllincomeExpense`,
            component: AllincomeExpense,
        },
        {
            path: `product-in-stocks`,
            name: `SuperAllInStockProducts`,
            component: AllInStockProducts,
        },
        {
            path: `product-out-stocks`,
            name: `SuperAllOutStockProducts`,
            component: AllOutStockProducts,
        },
        {
            path: `product-report`,
            name: `SuperProductReport`,
            component: ProductReport,
        },
        // {
        //     path: `branch-product-report`,
        //     name: `BranchProductReport`,
        //     component: BranchProductReport,
        // },

    ],
};
