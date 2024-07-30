import Layout from "../../../views/admin/banner/Layout";
import All from "../../../views/admin/banner/All";
import Create from "../../../views/admin/banner/Create";
import Edit from "../../../views/admin/banner/Edit";
import Details from "../../../views/admin/banner/Details";
import Import from "../../../views/admin/banner/Import";

let prefix = `AdminBanner`
export default {
    path: "banner",
    component: Layout,
    props: {
        role_permissions: ["admin"],
        layout_title: "Banner Management",
    },
    children: [
        {
            path: ``,
            name: `All${prefix}`,
            component: All,
        },
        {
            path: `import`,
            name: `Import${prefix}`,
            component: Import,
        },
        {
            path: `create`,
            name: `Create${prefix}`,
            component: Create,
        },
        {
            path: `edit/:id`,
            name: `Edit${prefix}`,
            component: Edit,
        },
        {
            path: `details/:id`,
            name: `Details${prefix}`,
            component: Details,
        },
    ],
};
