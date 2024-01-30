<script setup>
import Layout from "@/Layouts/Authenticated.vue";
import { onMounted, ref, watch } from "vue";
import { api, labelsNew, rules } from "@/helpers";
import Snackbar from "@/Components/snackbar.vue";
import FormView from "@/Components/dialogs/FormView.vue";

const snackbar = ref({ view: false, color: "", text: "" });

const props = defineProps({
    roleItem: { type: Object, default: null },
    permissionsItem: { type: Object, default: null },
    errors: Object,
});

const loading = ref(false);
const editmode = ref(false);
const permissions = ref({});
const permissionsList = ref([]);
const role = ref({
    name: "",
    description: "",
});

//------------- Submit Validation Create Permissions
function Submit_Permission(success) {
    if (!success) {
        snackbar.value.text = labelsNew.Please_fill_the_form_correctly;
        snackbar.value.color = "error";
        snackbar.value.view = true;
    } else {
        if (editmode.value === true) {
            Update_Permission(role.value.id);
        } else {
            Create_Permission();
        }
    }
}

//------------------------ Update Permissions -------------------\\
function Update_Permission(id) {
    api.put({
        url: "/roles/" + id,
        params: {
            role: role.value,
            permissions: permissions.value,
        },
        snackbar,
        loadingItem: loading,
        onSuccess: () => {
            snackbar.value.text = labelsNew.Update.TitleRole;
        },
    });
}

//------------------------ Create Permissions -------------------\\
function Create_Permission() {
    api.post({
        url: "/roles",
        params: {
            role: role.value,
            permissions: permissions.value,
        },
        snackbar,
        loadingItem: loading,
        onSuccess: () => {
            snackbar.value.text = labelsNew.Create.TitleRole;
        },
    });
}

function resetForm() {
    permissions.value = {};
    role.value = {
        name: "",
        description: "",
    };
}

function loadPermissions() {
    permissionsList.value = [
        {
            title: labelsNew.dashboard,
            items: [
                {
                    cols: "12",
                    label: labelsNew.dashboard,
                    permission: "dashboard",
                    tooltip:
                        "Si no está marcado, solo se mostrará un mensaje de bienvenida en el panel de control.",
                },
            ],
        },
        {
            title: labelsNew.UserManagement,
            items: [
                {
                    cols: "6",
                    label: labelsNew.View,
                    permission: "users_view",
                },
                {
                    cols: "6",
                    label: labelsNew.Add,
                    permission: "users_add",
                },
                {
                    cols: "6",
                    label: labelsNew.Edit,
                    permission: "users_edit",
                },
                {
                    cols: "6",
                    label: labelsNew.Del,
                    permission: "users_delete",
                },
                {
                    cols: "12",
                    label: labelsNew.ShowAll,
                    permission: "record_view",
                },
            ],
        },
        {
            title: labelsNew.UserPermissions,
            items: [
                {
                    cols: "6",
                    label: labelsNew.View,
                    permission: "permissions_view",
                },
                {
                    cols: "6",
                    label: labelsNew.Add,
                    permission: "permissions_add",
                },
                {
                    cols: "6",
                    label: labelsNew.Edit,
                    permission: "permissions_edit",
                },
                {
                    cols: "6",
                    label: labelsNew.Del,
                    permission: "permissions_delete",
                },
            ],
        },
        {
            title: labelsNew.Products,
            items: [
                {
                    cols: "6",
                    label: labelsNew.View,
                    permission: "products_view",
                },
                {
                    cols: "6",
                    label: labelsNew.Add,
                    permission: "products_add",
                },
                {
                    cols: "6",
                    label: labelsNew.Edit,
                    permission: "products_edit",
                },
                {
                    cols: "6",
                    label: labelsNew.Del,
                    permission: "products_delete",
                },
                {
                    cols: "6",
                    label: labelsNew.Categories,
                    permission: "category",
                },
                {
                    cols: "6",
                    label: labelsNew.Units,
                    permission: "unit",
                },
            ],
        },
        {
            title: labelsNew.StockAdjustement,
            items: [
                {
                    cols: "6",
                    label: labelsNew.View,
                    permission: "adjustment_view",
                },
                {
                    cols: "6",
                    label: labelsNew.Add,
                    permission: "adjustment_add",
                },
                {
                    cols: "6",
                    label: labelsNew.Edit,
                    permission: "adjustment_edit",
                },
                {
                    cols: "6",
                    label: labelsNew.Del,
                    permission: "adjustment_delete",
                },
            ],
        },
        {
            title: labelsNew.StockTransfers,
            items: [
                {
                    cols: "6",
                    label: labelsNew.View,
                    permission: "transfer_view",
                },
                {
                    cols: "6",
                    label: labelsNew.Add,
                    permission: "transfer_add",
                },
                {
                    cols: "6",
                    label: labelsNew.Edit,
                    permission: "transfer_edit",
                },
                {
                    cols: "6",
                    label: labelsNew.Del,
                    permission: "transfer_delete",
                },
            ],
        },
        {
            title: labelsNew.Expenses,
            items: [
                {
                    cols: "6",
                    label: labelsNew.View,
                    permission: "expense_view",
                },
                {
                    cols: "6",
                    label: labelsNew.Add,
                    permission: "expense_add",
                },
                {
                    cols: "6",
                    label: labelsNew.Edit,
                    permission: "expense_edit",
                },
                {
                    cols: "6",
                    label: labelsNew.Del,
                    permission: "expense_delete",
                },
            ],
        },
        {
            title: labelsNew.Expense_Category,
            items: [
                {
                    cols: "6",
                    label: labelsNew.View,
                    permission: "expense_category_view",
                },
                {
                    cols: "6",
                    label: labelsNew.Add,
                    permission: "expense_category_add",
                },
                {
                    cols: "6",
                    label: labelsNew.Edit,
                    permission: "expense_category_edit",
                },
                {
                    cols: "6",
                    label: labelsNew.Del,
                    permission: "expense_category_delete",
                },
            ],
        },
        {
            title: labelsNew.Sales,
            items: [
                {
                    cols: "6",
                    label: labelsNew.View,
                    permission: "Sales_view",
                },
                {
                    cols: "6",
                    label: labelsNew.Add,
                    permission: "Sales_add",
                },
                {
                    cols: "6",
                    label: labelsNew.Edit,
                    permission: "Sales_edit",
                },
                {
                    cols: "6",
                    label: labelsNew.Del,
                    permission: "Sales_delete",
                },
                {
                    cols: "6",
                    // label: labelsNew.pointofsales,
                    label: "Ver Tienda",
                    permission: "Pos_view",
                },
                {
                    cols: "6",
                    // label: labelsNew.pointofsales,
                    label: "Ver Pantalla",
                    permission: "screen_view",
                },
            ],
        },
        {
            title: labelsNew.PaymentsSales,
            items: [
                {
                    cols: "6",
                    label: labelsNew.View,
                    permission: "payment_sales_view",
                },
                {
                    cols: "6",
                    label: labelsNew.Add,
                    permission: "payment_sales_add",
                },
                {
                    cols: "6",
                    label: labelsNew.Edit,
                    permission: "payment_sales_edit",
                },
                {
                    cols: "6",
                    label: labelsNew.Del,
                    permission: "payment_sales_delete",
                },
            ],
        },
        {
            title: labelsNew.Customers,
            items: [
                {
                    cols: "6",
                    label: labelsNew.View,
                    permission: "Customers_view",
                },
                {
                    cols: "6",
                    label: labelsNew.Add,
                    permission: "Customers_add",
                },
                {
                    cols: "6",
                    label: labelsNew.Edit,
                    permission: "Customers_edit",
                },
                {
                    cols: "6",
                    label: labelsNew.Del,
                    permission: "Customers_delete",
                },
                {
                    cols: "12",
                    label: labelsNew.pay_all_sell_due_at_a_time,
                    permission: "pay_due",
                },
            ],
        },
        {
            title: labelsNew.Reports,
            items: [
                {
                    cols: "12",
                    label: labelsNew.Reports_payments_Sales,
                    permission: "Reports_payments_Sales",
                },
                {
                    cols: "12",
                    label: labelsNew.SalesReport,
                    permission: "Reports_sales",
                },
                // {
                //     cols: "12",
                //     label: labelsNew.ProfitandLoss,
                //     permission: "Reports_profit",
                // }
                // ,{
                //     cols: "12",
                //     label: labelsNew.ProductQuantityAlerts,
                //     permission: "Reports_quantity_alerts",
                // },
                {
                    cols: "12",
                    label: labelsNew.Warehouse_report,
                    permission: "Warehouse_report",
                },
                // {
                //     cols: "12",
                //     label: labelsNew.Top_Selling_Products,
                //     permission: "Top_products",
                // },
                {
                    cols: "12",
                    label: labelsNew.Users_Report,
                    permission: "users_report",
                },
                {
                    cols: "12",
                    label: labelsNew.CustomersReport,
                    permission: "Reports_customers",
                },
                {
                    cols: "12",
                    label: labelsNew.stock_report,
                    permission: "stock_report",
                },
                {
                    cols: "12",
                    label: labelsNew.product_report,
                    permission: "product_report",
                },
                // {
                //     cols: "12",
                //     label: labelsNew.product_sales_report,
                //     permission: "product_sales_report",
                // },
            ],
        },
        {
            title: labelsNew.Settings,
            items: [
                {
                    cols: "12",
                    label: labelsNew.SystemSettings,
                    permission: "setting_system",
                },
                {
                    cols: "12",
                    label: labelsNew.Warehouses,
                    permission: "warehouse",
                },
                {
                    cols: "12",
                    label: labelsNew.sales_type,
                    permission: "sales_type",
                },
                // {
                //     cols: "12",
                //     label: labelsNew.pos_settings,
                //     permission: "pos_settings",
                // },
                // {
                //     cols: "12",
                //     label: labelsNew.Backup,
                //     permission: "backup",
                // },
            ],
        },
    ];
}

watch(
    () => [props.roleItem],
    () => {
        if (props.roleItem != null) {
            editmode.value = true;
        } else {
            resetForm();
            editmode.value = false;
        }
    }
);
onMounted(() => {
    loadPermissions();
    if (props.roleItem != null) {
        role.value = props.roleItem;
        permissions.value = props.permissionsItem;
        editmode.value = true;
    }
});
</script>
<template>
    <Layout>
        <snackbar
            :text="snackbar.text"
            v-model="snackbar.view"
            :color="snackbar.color"
        ></snackbar>
        <form-view :on-save="Submit_Permission">
            <v-row>
                <!-- Role Name -->
                <v-col cols="12" md="6">
                    <v-text-field
                        :label="labelsNew.RoleName"
                        :rules="rules.required"
                        v-model="role.name"
                    ></v-text-field>
                </v-col>

                <!-- Role Description -->
                <v-col cols="12" md="6">
                    <v-text-field
                        :label="labelsNew.RoleDescription"
                        v-model="role.description"
                    ></v-text-field>
                </v-col>
            </v-row>
            <!---->
            <v-row class="mt-2">
                <!--dashboard -->
                <v-col
                    cols="12"
                    sm="6"
                    md="4"
                    v-for="permissionsItem in permissionsList"
                >
                    <v-expansion-panels>
                        <v-expansion-panel>
                            <v-expansion-panel-title
                                >{{ permissionsItem.title }}
                            </v-expansion-panel-title>
                            <v-expansion-panel-text>
                                <v-row>
                                    <!--dashboard -->
                                    <v-col
                                        v-for="item in permissionsItem.items"
                                        cols="12"
                                        :sm="item.cols"
                                    >
                                        <v-checkbox
                                            color="primary"
                                            v-model="
                                                permissions[item.permission]
                                            "
                                            :label="item.label"
                                            hide-details="auto"
                                        >
                                            <v-tooltip
                                                v-if="item.tooltip != undefined"
                                                activator="parent"
                                                location="top"
                                            >
                                                {{ item.tooltip }}
                                            </v-tooltip>
                                        </v-checkbox>
                                    </v-col>
                                </v-row>
                            </v-expansion-panel-text>
                        </v-expansion-panel>
                    </v-expansion-panels>
                </v-col>

                <!--                &lt;!&ndash; Purchases &ndash;&gt;-->
                <!--                <v-col md="4">-->
                <!--                    <v-card no-body class="ul-card__border-radius">-->
                <!--                        <v-card-header-->
                <!--                            header-tag="header"-->
                <!--                            class="p-1"-->
                <!--                            role="tab"-->
                <!--                        >-->
                <!--                            <v-button-->
                <!--                                class="card-title mv-0"-->
                <!--                                block-->
                <!--                                href="#"-->
                <!--                                v-v-toggle.panel-Purchases-->
                <!--                                variant="transparent"-->
                <!--                                >{{ $t("Purchases") }}-->
                <!--                            </v-button>-->
                <!--                        </v-card-header>-->
                <!--                        <v-collapse-->
                <!--                            id="panel-Purchases"-->
                <!--                            :visible="true"-->
                <!--                            accordion="my-accordion8"-->
                <!--                            role="tabpanel"-->
                <!--                        >-->
                <!--                            <v-card-body>-->
                <!--                                <v-card-text>-->
                <!--                                    <v-row>-->
                <!--                                        &lt;!&ndash;Purchases View &ndash;&gt;-->
                <!--                                         <v-col cols="12" md="6">-->
                <!--                                            <label-->
                <!--                                                class="checkbox checkbox-outline-primary"-->
                <!--                                            >-->
                <!--                                                <input-->
                <!--                                                    type="checkbox"-->
                <!--                                                    checked-->
                <!--                                                    v-model="permissions"-->
                <!--                                                    value="Purchases_view"-->
                <!--                                                />-->
                <!--                                                <span>{{ $t("View") }}</span>-->
                <!--                                                <span class="checkmark"></span>-->
                <!--                                            </label>-->
                <!--                                        </v-col>-->
                <!--                                        &lt;!&ndash;Purchases ADD &ndash;&gt;-->
                <!--                                         <v-col cols="12" md="6">-->
                <!--                                            <label-->
                <!--                                                class="checkbox checkbox-outline-primary"-->
                <!--                                            >-->
                <!--                                                <input-->
                <!--                                                    type="checkbox"-->
                <!--                                                    checked-->
                <!--                                                    v-model="permissions"-->
                <!--                                                    value="Purchases_add"-->
                <!--                                                />-->
                <!--                                                <span>{{ $t("Add") }}</span>-->
                <!--                                                <span class="checkmark"></span>-->
                <!--                                            </label>-->
                <!--                                        </v-col>-->
                <!--                                        &lt;!&ndash;Purchases Edit &ndash;&gt;-->
                <!--                                         <v-col cols="12" md="6">-->
                <!--                                            <label-->
                <!--                                                class="checkbox checkbox-outline-primary"-->
                <!--                                            >-->
                <!--                                                <input-->
                <!--                                                    type="checkbox"-->
                <!--                                                    checked-->
                <!--                                                    v-model="permissions"-->
                <!--                                                    value="Purchases_edit"-->
                <!--                                                />-->
                <!--                                                <span>{{ $t("Edit") }}</span>-->
                <!--                                                <span class="checkmark"></span>-->
                <!--                                            </label>-->
                <!--                                        </v-col>-->
                <!--                                        &lt;!&ndash;Purchases Delete &ndash;&gt;-->
                <!--                                         <v-col cols="12" md="6">-->
                <!--                                            <label-->
                <!--                                                class="checkbox checkbox-outline-primary"-->
                <!--                                            >-->
                <!--                                                <input-->
                <!--                                                    type="checkbox"-->
                <!--                                                    checked-->
                <!--                                                    v-model="permissions"-->
                <!--                                                    value="Purchases_delete"-->
                <!--                                                />-->
                <!--                                                <span>{{ $t("Del") }}</span>-->
                <!--                                                <span class="checkmark"></span>-->
                <!--                                            </label>-->
                <!--                                        </v-col>-->
                <!--                                    </v-row>-->
                <!--                                </v-card-text>-->
                <!--                            </v-card-body>-->
                <!--                        </v-collapse>-->
                <!--                    </v-card>-->
                <!--                </v-col>-->

                <!--                &lt;!&ndash; Quotations &ndash;&gt;-->
                <!--                <v-col md="4">-->
                <!--                    <v-card no-body class="ul-card__border-radius">-->
                <!--                        <v-card-header-->
                <!--                            header-tag="header"-->
                <!--                            class="p-1"-->
                <!--                            role="tab"-->
                <!--                        >-->
                <!--                            <v-button-->
                <!--                                class="card-title mv-0"-->
                <!--                                block-->
                <!--                                href="#"-->
                <!--                                v-v-toggle.panel-Quotations-->
                <!--                                variant="transparent"-->
                <!--                                >{{ $t("Quotations") }}-->
                <!--                            </v-button>-->
                <!--                        </v-card-header>-->
                <!--                        <v-collapse-->
                <!--                            id="panel-Quotations"-->
                <!--                            :visible="true"-->
                <!--                            accordion="my-accordion9"-->
                <!--                            role="tabpanel"-->
                <!--                        >-->
                <!--                            <v-card-body>-->
                <!--                                <v-card-text>-->
                <!--                                    <v-row>-->
                <!--                                        &lt;!&ndash;Quotations View &ndash;&gt;-->
                <!--                                         <v-col cols="12" md="6">-->
                <!--                                            <label-->
                <!--                                                class="checkbox checkbox-outline-primary"-->
                <!--                                            >-->
                <!--                                                <input-->
                <!--                                                    type="checkbox"-->
                <!--                                                    checked-->
                <!--                                                    v-model="permissions"-->
                <!--                                                    value="Quotations_view"-->
                <!--                                                />-->
                <!--                                                <span>{{ $t("View") }}</span>-->
                <!--                                                <span class="checkmark"></span>-->
                <!--                                            </label>-->
                <!--                                        </v-col>-->
                <!--                                        &lt;!&ndash;Quotations ADD &ndash;&gt;-->
                <!--                                         <v-col cols="12" md="6">-->
                <!--                                            <label-->
                <!--                                                class="checkbox checkbox-outline-primary"-->
                <!--                                            >-->
                <!--                                                <input-->
                <!--                                                    type="checkbox"-->
                <!--                                                    checked-->
                <!--                                                    v-model="permissions"-->
                <!--                                                    value="Quotations_add"-->
                <!--                                                />-->
                <!--                                                <span>{{ $t("Add") }}</span>-->
                <!--                                                <span class="checkmark"></span>-->
                <!--                                            </label>-->
                <!--                                        </v-col>-->
                <!--                                        &lt;!&ndash;Quotations Edit &ndash;&gt;-->
                <!--                                         <v-col cols="12" md="6">-->
                <!--                                            <label-->
                <!--                                                class="checkbox checkbox-outline-primary"-->
                <!--                                            >-->
                <!--                                                <input-->
                <!--                                                    type="checkbox"-->
                <!--                                                    checked-->
                <!--                                                    v-model="permissions"-->
                <!--                                                    value="Quotations_edit"-->
                <!--                                                />-->
                <!--                                                <span>{{ $t("Edit") }}</span>-->
                <!--                                                <span class="checkmark"></span>-->
                <!--                                            </label>-->
                <!--                                        </v-col>-->
                <!--                                        &lt;!&ndash;Quotations Delete &ndash;&gt;-->
                <!--                                         <v-col cols="12" md="6">-->
                <!--                                            <label-->
                <!--                                                class="checkbox checkbox-outline-primary"-->
                <!--                                            >-->
                <!--                                                <input-->
                <!--                                                    type="checkbox"-->
                <!--                                                    checked-->
                <!--                                                    v-model="permissions"-->
                <!--                                                    value="Quotations_delete"-->
                <!--                                                />-->
                <!--                                                <span>{{ $t("Del") }}</span>-->
                <!--                                                <span class="checkmark"></span>-->
                <!--                                            </label>-->
                <!--                                        </v-col>-->
                <!--                                    </v-row>-->
                <!--                                </v-card-text>-->
                <!--                            </v-card-body>-->
                <!--                        </v-collapse>-->
                <!--                    </v-card>-->
                <!--                </v-col>-->

                <!--                &lt;!&ndash; Sale Returns &ndash;&gt;-->
                <!--                <v-col md="4">-->
                <!--                    <v-card no-body class="ul-card__border-radius">-->
                <!--                        <v-card-header-->
                <!--                            header-tag="header"-->
                <!--                            class="p-1"-->
                <!--                            role="tab"-->
                <!--                        >-->
                <!--                            <v-button-->
                <!--                                class="card-title mv-0"-->
                <!--                                block-->
                <!--                                href="#"-->
                <!--                                v-v-toggle.panel-Return-Sale-->
                <!--                                variant="transparent"-->
                <!--                                >{{ $t("SalesReturn") }}-->
                <!--                            </v-button>-->
                <!--                        </v-card-header>-->
                <!--                        <v-collapse-->
                <!--                            id="panel-Return-Sale"-->
                <!--                            :visible="true"-->
                <!--                            accordion="my-accordion10"-->
                <!--                            role="tabpanel"-->
                <!--                        >-->
                <!--                            <v-card-body>-->
                <!--                                <v-card-text>-->
                <!--                                    <v-row>-->
                <!--                                        &lt;!&ndash;Return Sale View &ndash;&gt;-->
                <!--                                         <v-col cols="12" md="6">-->
                <!--                                            <label-->
                <!--                                                class="checkbox checkbox-outline-primary"-->
                <!--                                            >-->
                <!--                                                <input-->
                <!--                                                    type="checkbox"-->
                <!--                                                    checked-->
                <!--                                                    v-model="permissions"-->
                <!--                                                    value="Sale_Returns_view"-->
                <!--                                                />-->
                <!--                                                <span>{{ $t("View") }}</span>-->
                <!--                                                <span class="checkmark"></span>-->
                <!--                                            </label>-->
                <!--                                        </v-col>-->
                <!--                                        &lt;!&ndash;Return Sale ADD &ndash;&gt;-->
                <!--                                         <v-col cols="12" md="6">-->
                <!--                                            <label-->
                <!--                                                class="checkbox checkbox-outline-primary"-->
                <!--                                            >-->
                <!--                                                <input-->
                <!--                                                    type="checkbox"-->
                <!--                                                    checked-->
                <!--                                                    v-model="permissions"-->
                <!--                                                    value="Sale_Returns_add"-->
                <!--                                                />-->
                <!--                                                <span>{{ $t("Add") }}</span>-->
                <!--                                                <span class="checkmark"></span>-->
                <!--                                            </label>-->
                <!--                                        </v-col>-->
                <!--                                        &lt;!&ndash;Return Sale Edit &ndash;&gt;-->
                <!--                                         <v-col cols="12" md="6">-->
                <!--                                            <label-->
                <!--                                                class="checkbox checkbox-outline-primary"-->
                <!--                                            >-->
                <!--                                                <input-->
                <!--                                                    type="checkbox"-->
                <!--                                                    checked-->
                <!--                                                    v-model="permissions"-->
                <!--                                                    value="Sale_Returns_edit"-->
                <!--                                                />-->
                <!--                                                <span>{{ $t("Edit") }}</span>-->
                <!--                                                <span class="checkmark"></span>-->
                <!--                                            </label>-->
                <!--                                        </v-col>-->
                <!--                                        &lt;!&ndash;Return Sale Delete &ndash;&gt;-->
                <!--                                         <v-col cols="12" md="6">-->
                <!--                                            <label-->
                <!--                                                class="checkbox checkbox-outline-primary"-->
                <!--                                            >-->
                <!--                                                <input-->
                <!--                                                    type="checkbox"-->
                <!--                                                    checked-->
                <!--                                                    v-model="permissions"-->
                <!--                                                    value="Sale_Returns_delete"-->
                <!--                                                />-->
                <!--                                                <span>{{ $t("Del") }}</span>-->
                <!--                                                <span class="checkmark"></span>-->
                <!--                                            </label>-->
                <!--                                        </v-col>-->
                <!--                                    </v-row>-->
                <!--                                </v-card-text>-->
                <!--                            </v-card-body>-->
                <!--                        </v-collapse>-->
                <!--                    </v-card>-->
                <!--                </v-col>-->

                <!--                &lt;!&ndash; Purchase Return &ndash;&gt;-->
                <!--                <v-col md="4">-->
                <!--                    <v-card no-body class="ul-card__border-radius">-->
                <!--                        <v-card-header-->
                <!--                            header-tag="header"-->
                <!--                            class="p-1"-->
                <!--                            role="tab"-->
                <!--                        >-->
                <!--                            <v-button-->
                <!--                                class="card-title mv-0"-->
                <!--                                block-->
                <!--                                href="#"-->
                <!--                                v-v-toggle.panel-Return-Purchase-->
                <!--                                variant="transparent"-->
                <!--                                >{{ $t("PurchasesReturn") }}-->
                <!--                            </v-button>-->
                <!--                        </v-card-header>-->
                <!--                        <v-collapse-->
                <!--                            id="panel-Return-Purchase"-->
                <!--                            :visible="true"-->
                <!--                            accordion="my-accordion11"-->
                <!--                            role="tabpanel"-->
                <!--                        >-->
                <!--                            <v-card-body>-->
                <!--                                <v-card-text>-->
                <!--                                    <v-row>-->
                <!--                                        &lt;!&ndash;Return Purchase View &ndash;&gt;-->
                <!--                                         <v-col cols="12" md="6">-->
                <!--                                            <label-->
                <!--                                                class="checkbox checkbox-outline-primary"-->
                <!--                                            >-->
                <!--                                                <input-->
                <!--                                                    type="checkbox"-->
                <!--                                                    checked-->
                <!--                                                    v-model="permissions"-->
                <!--                                                    value="Purchase_Returns_view"-->
                <!--                                                />-->
                <!--                                                <span>{{ $t("View") }}</span>-->
                <!--                                                <span class="checkmark"></span>-->
                <!--                                            </label>-->
                <!--                                        </v-col>-->
                <!--                                        &lt;!&ndash;Return Purchase ADD &ndash;&gt;-->
                <!--                                         <v-col cols="12" md="6">-->
                <!--                                            <label-->
                <!--                                                class="checkbox checkbox-outline-primary"-->
                <!--                                            >-->
                <!--                                                <input-->
                <!--                                                    type="checkbox"-->
                <!--                                                    checked-->
                <!--                                                    v-model="permissions"-->
                <!--                                                    value="Purchase_Returns_add"-->
                <!--                                                />-->
                <!--                                                <span>{{ $t("Add") }}</span>-->
                <!--                                                <span class="checkmark"></span>-->
                <!--                                            </label>-->
                <!--                                        </v-col>-->
                <!--                                        &lt;!&ndash;Return Purchase Edit &ndash;&gt;-->
                <!--                                         <v-col cols="12" md="6">-->
                <!--                                            <label-->
                <!--                                                class="checkbox checkbox-outline-primary"-->
                <!--                                            >-->
                <!--                                                <input-->
                <!--                                                    type="checkbox"-->
                <!--                                                    checked-->
                <!--                                                    v-model="permissions"-->
                <!--                                                    value="Purchase_Returns_edit"-->
                <!--                                                />-->
                <!--                                                <span>{{ $t("Edit") }}</span>-->
                <!--                                                <span class="checkmark"></span>-->
                <!--                                            </label>-->
                <!--                                        </v-col>-->
                <!--                                        &lt;!&ndash;Return Purchase Delete &ndash;&gt;-->
                <!--                                         <v-col cols="12" md="6">-->
                <!--                                            <label-->
                <!--                                                class="checkbox checkbox-outline-primary"-->
                <!--                                            >-->
                <!--                                                <input-->
                <!--                                                    type="checkbox"-->
                <!--                                                    checked-->
                <!--                                                    v-model="permissions"-->
                <!--                                                    value="Purchase_Returns_delete"-->
                <!--                                                />-->
                <!--                                                <span>{{ $t("Del") }}</span>-->
                <!--                                                <span class="checkmark"></span>-->
                <!--                                            </label>-->
                <!--                                        </v-col>-->
                <!--                                    </v-row>-->
                <!--                                </v-card-text>-->
                <!--                            </v-card-body>-->
                <!--                        </v-collapse>-->
                <!--                    </v-card>-->
                <!--                </v-col>-->

                <!--                &lt;!&ndash; Payment Purchases &ndash;&gt;-->
                <!--                <v-col md="4">-->
                <!--                    <v-card no-body class="ul-card__border-radius">-->
                <!--                        <v-card-header-->
                <!--                            header-tag="header"-->
                <!--                            class="p-1"-->
                <!--                            role="tab"-->
                <!--                        >-->
                <!--                            <v-button-->
                <!--                                class="card-title mv-0"-->
                <!--                                block-->
                <!--                                href="#"-->
                <!--                                v-v-toggle.panel-Payment-Purchases-->
                <!--                                variant="transparent"-->
                <!--                                >{{ $t("PaymentsPurchases") }}-->
                <!--                            </v-button>-->
                <!--                        </v-card-header>-->
                <!--                        <v-collapse-->
                <!--                            id="panel-Payment-Purchases"-->
                <!--                            :visible="true"-->
                <!--                            accordion="my-accordion13"-->
                <!--                            role="tabpanel"-->
                <!--                        >-->
                <!--                            <v-card-body>-->
                <!--                                <v-card-text>-->
                <!--                                    <v-row>-->
                <!--                                        &lt;!&ndash;Payment Purchases View &ndash;&gt;-->
                <!--                                         <v-col cols="12" md="6">-->
                <!--                                            <label-->
                <!--                                                class="checkbox checkbox-outline-primary"-->
                <!--                                            >-->
                <!--                                                <input-->
                <!--                                                    type="checkbox"-->
                <!--                                                    checked-->
                <!--                                                    v-model="permissions"-->
                <!--                                                    value="payment_purchases_view"-->
                <!--                                                />-->
                <!--                                                <span>{{ $t("View") }}</span>-->
                <!--                                                <span class="checkmark"></span>-->
                <!--                                            </label>-->
                <!--                                        </v-col>-->
                <!--                                        &lt;!&ndash;Payment Purchases ADD &ndash;&gt;-->
                <!--                                         <v-col cols="12" md="6">-->
                <!--                                            <label-->
                <!--                                                class="checkbox checkbox-outline-primary"-->
                <!--                                            >-->
                <!--                                                <input-->
                <!--                                                    type="checkbox"-->
                <!--                                                    checked-->
                <!--                                                    v-model="permissions"-->
                <!--                                                    value="payment_purchases_add"-->
                <!--                                                />-->
                <!--                                                <span>{{ $t("Add") }}</span>-->
                <!--                                                <span class="checkmark"></span>-->
                <!--                                            </label>-->
                <!--                                        </v-col>-->
                <!--                                        &lt;!&ndash;Payment Purchases Edit &ndash;&gt;-->
                <!--                                         <v-col cols="12" md="6">-->
                <!--                                            <label-->
                <!--                                                class="checkbox checkbox-outline-primary"-->
                <!--                                            >-->
                <!--                                                <input-->
                <!--                                                    type="checkbox"-->
                <!--                                                    checked-->
                <!--                                                    v-model="permissions"-->
                <!--                                                    value="payment_purchases_edit"-->
                <!--                                                />-->
                <!--                                                <span>{{ $t("Edit") }}</span>-->
                <!--                                                <span class="checkmark"></span>-->
                <!--                                            </label>-->
                <!--                                        </v-col>-->
                <!--                                        &lt;!&ndash;Payment Purchases Delete &ndash;&gt;-->
                <!--                                         <v-col cols="12" md="6">-->
                <!--                                            <label-->
                <!--                                                class="checkbox checkbox-outline-primary"-->
                <!--                                            >-->
                <!--                                                <input-->
                <!--                                                    type="checkbox"-->
                <!--                                                    checked-->
                <!--                                                    v-model="permissions"-->
                <!--                                                    value="payment_purchases_delete"-->
                <!--                                                />-->
                <!--                                                <span>{{ $t("Del") }}</span>-->
                <!--                                                <span class="checkmark"></span>-->
                <!--                                            </label>-->
                <!--                                        </v-col>-->
                <!--                                    </v-row>-->
                <!--                                </v-card-text>-->
                <!--                            </v-card-body>-->
                <!--                        </v-collapse>-->
                <!--                    </v-card>-->
                <!--                </v-col>-->

                <!--                &lt;!&ndash; Payment Returns &ndash;&gt;-->
                <!--                <v-col md="4">-->
                <!--                    <v-card no-body class="ul-card__border-radius">-->
                <!--                        <v-card-header-->
                <!--                            header-tag="header"-->
                <!--                            class="p-1"-->
                <!--                            role="tab"-->
                <!--                        >-->
                <!--                            <v-button-->
                <!--                                class="card-title mv-0"-->
                <!--                                block-->
                <!--                                href="#"-->
                <!--                                v-v-toggle.panel-Payment-Returns-->
                <!--                                variant="transparent"-->
                <!--                                >{{ $t("PaymentsReturns") }}-->
                <!--                            </v-button>-->
                <!--                        </v-card-header>-->
                <!--                        <v-collapse-->
                <!--                            id="panel-Payment-Returns"-->
                <!--                            :visible="true"-->
                <!--                            accordion="my-accordion14"-->
                <!--                            role="tabpanel"-->
                <!--                        >-->
                <!--                            <v-card-body>-->
                <!--                                <v-card-text>-->
                <!--                                    <v-row>-->
                <!--                                        &lt;!&ndash;Payment Returns View &ndash;&gt;-->
                <!--                                         <v-col cols="12" md="6">-->
                <!--                                            <label-->
                <!--                                                class="checkbox checkbox-outline-primary"-->
                <!--                                            >-->
                <!--                                                <input-->
                <!--                                                    type="checkbox"-->
                <!--                                                    checked-->
                <!--                                                    v-model="permissions"-->
                <!--                                                    value="payment_returns_view"-->
                <!--                                                />-->
                <!--                                                <span>{{ $t("View") }}</span>-->
                <!--                                                <span class="checkmark"></span>-->
                <!--                                            </label>-->
                <!--                                        </v-col>-->
                <!--                                        &lt;!&ndash;Payment Returns ADD &ndash;&gt;-->
                <!--                                         <v-col cols="12" md="6">-->
                <!--                                            <label-->
                <!--                                                class="checkbox checkbox-outline-primary"-->
                <!--                                            >-->
                <!--                                                <input-->
                <!--                                                    type="checkbox"-->
                <!--                                                    checked-->
                <!--                                                    v-model="permissions"-->
                <!--                                                    value="payment_returns_add"-->
                <!--                                                />-->
                <!--                                                <span>{{ $t("Add") }}</span>-->
                <!--                                                <span class="checkmark"></span>-->
                <!--                                            </label>-->
                <!--                                        </v-col>-->
                <!--                                        &lt;!&ndash;Payment Returns Edit &ndash;&gt;-->
                <!--                                         <v-col cols="12" md="6">-->
                <!--                                            <label-->
                <!--                                                class="checkbox checkbox-outline-primary"-->
                <!--                                            >-->
                <!--                                                <input-->
                <!--                                                    type="checkbox"-->
                <!--                                                    checked-->
                <!--                                                    v-model="permissions"-->
                <!--                                                    value="payment_returns_edit"-->
                <!--                                                />-->
                <!--                                                <span>{{ $t("Edit") }}</span>-->
                <!--                                                <span class="checkmark"></span>-->
                <!--                                            </label>-->
                <!--                                        </v-col>-->
                <!--                                        &lt;!&ndash;Payment Returns Delete &ndash;&gt;-->
                <!--                                         <v-col cols="12" md="6">-->
                <!--                                            <label-->
                <!--                                                class="checkbox checkbox-outline-primary"-->
                <!--                                            >-->
                <!--                                                <input-->
                <!--                                                    type="checkbox"-->
                <!--                                                    checked-->
                <!--                                                    v-model="permissions"-->
                <!--                                                    value="payment_returns_delete"-->
                <!--                                                />-->
                <!--                                                <span>{{ $t("Del") }}</span>-->
                <!--                                                <span class="checkmark"></span>-->
                <!--                                            </label>-->
                <!--                                        </v-col>-->
                <!--                                    </v-row>-->
                <!--                                </v-card-text>-->
                <!--                            </v-card-body>-->
                <!--                        </v-collapse>-->
                <!--                    </v-card>-->
                <!--                </v-col>-->

                <!--                &lt;!&ndash; Customers &ndash;&gt;-->
                <!--                <v-col md="4">-->
                <!--                    <v-card no-body class="ul-card__border-radius">-->
                <!--                        <v-card-header-->
                <!--                            header-tag="header"-->
                <!--                            class="p-1"-->
                <!--                            role="tab"-->
                <!--                        >-->
                <!--                            <v-button-->
                <!--                                class="card-title mv-0"-->
                <!--                                block-->
                <!--                                href="#"-->
                <!--                                v-v-toggle.panel-Customers-->
                <!--                                variant="transparent"-->
                <!--                                >{{ $t("Customers") }}-->
                <!--                            </v-button>-->
                <!--                        </v-card-header>-->
                <!--                        <v-collapse-->
                <!--                            id="panel-Customers"-->
                <!--                            :visible="true"-->
                <!--                            accordion="my-accordion15"-->
                <!--                            role="tabpanel"-->
                <!--                        >-->
                <!--                            <v-card-body>-->
                <!--                                <v-card-text>-->
                <!--                                    <v-row>-->
                <!--                                        &lt;!&ndash;pay_sale_return_due &ndash;&gt;-->
                <!--                                         <v-col cols="12">-->
                <!--                                            <label-->
                <!--                                                class="checkbox checkbox-outline-primary"-->
                <!--                                            >-->
                <!--                                                <input-->
                <!--                                                    type="checkbox"-->
                <!--                                                    checked-->
                <!--                                                    v-model="permissions"-->
                <!--                                                    value="pay_sale_return_due"-->
                <!--                                                />-->
                <!--                                                <span>{{-->
                <!--                                                    $t(-->
                <!--                                                        "pay_all_sell_return_due_at_a_time"-->
                <!--                                                    )-->
                <!--                                                }}</span>-->
                <!--                                                <span class="checkmark"></span>-->
                <!--                                            </label>-->
                <!--                                        </v-col>-->
                <!--                                    </v-row>-->
                <!--                                </v-card-text>-->
                <!--                            </v-card-body>-->
                <!--                        </v-collapse>-->
                <!--                    </v-card>-->
                <!--                </v-col>-->

                <!--                &lt;!&ndash; Suppliers &ndash;&gt;-->
                <!--                <v-col md="4">-->
                <!--                    <v-card no-body class="ul-card__border-radius">-->
                <!--                        <v-card-header-->
                <!--                            header-tag="header"-->
                <!--                            class="p-1"-->
                <!--                            role="tab"-->
                <!--                        >-->
                <!--                            <v-button-->
                <!--                                class="card-title mv-0"-->
                <!--                                block-->
                <!--                                href="#"-->
                <!--                                v-v-toggle.panel-Suppliers-->
                <!--                                variant="transparent"-->
                <!--                                >{{ $t("Suppliers") }}-->
                <!--                            </v-button>-->
                <!--                        </v-card-header>-->
                <!--                        <v-collapse-->
                <!--                            id="panel-Suppliers"-->
                <!--                            :visible="true"-->
                <!--                            accordion="my-accordion16"-->
                <!--                            role="tabpanel"-->
                <!--                        >-->
                <!--                            <v-card-body>-->
                <!--                                <v-card-text>-->
                <!--                                    <v-row>-->
                <!--                                        &lt;!&ndash;Suppliers View &ndash;&gt;-->
                <!--                                         <v-col cols="12" md="6">-->
                <!--                                            <label-->
                <!--                                                class="checkbox checkbox-outline-primary"-->
                <!--                                            >-->
                <!--                                                <input-->
                <!--                                                    type="checkbox"-->
                <!--                                                    checked-->
                <!--                                                    v-model="permissions"-->
                <!--                                                    value="Suppliers_view"-->
                <!--                                                />-->
                <!--                                                <span>{{ $t("View") }}</span>-->
                <!--                                                <span class="checkmark"></span>-->
                <!--                                            </label>-->
                <!--                                        </v-col>-->
                <!--                                        &lt;!&ndash;Suppliers ADD &ndash;&gt;-->
                <!--                                         <v-col cols="12" md="6">-->
                <!--                                            <label-->
                <!--                                                class="checkbox checkbox-outline-primary"-->
                <!--                                            >-->
                <!--                                                <input-->
                <!--                                                    type="checkbox"-->
                <!--                                                    checked-->
                <!--                                                    v-model="permissions"-->
                <!--                                                    value="Suppliers_add"-->
                <!--                                                />-->
                <!--                                                <span>{{ $t("Add") }}</span>-->
                <!--                                                <span class="checkmark"></span>-->
                <!--                                            </label>-->
                <!--                                        </v-col>-->
                <!--                                        &lt;!&ndash;Suppliers Edit &ndash;&gt;-->
                <!--                                         <v-col cols="12" md="6">-->
                <!--                                            <label-->
                <!--                                                class="checkbox checkbox-outline-primary"-->
                <!--                                            >-->
                <!--                                                <input-->
                <!--                                                    type="checkbox"-->
                <!--                                                    checked-->
                <!--                                                    v-model="permissions"-->
                <!--                                                    value="Suppliers_edit"-->
                <!--                                                />-->
                <!--                                                <span>{{ $t("Edit") }}</span>-->
                <!--                                                <span class="checkmark"></span>-->
                <!--                                            </label>-->
                <!--                                        </v-col>-->
                <!--                                        &lt;!&ndash;Suppliers Delete &ndash;&gt;-->
                <!--                                         <v-col cols="12" md="6">-->
                <!--                                            <label-->
                <!--                                                class="checkbox checkbox-outline-primary"-->
                <!--                                            >-->
                <!--                                                <input-->
                <!--                                                    type="checkbox"-->
                <!--                                                    checked-->
                <!--                                                    v-model="permissions"-->
                <!--                                                    value="Suppliers_delete"-->
                <!--                                                />-->
                <!--                                                <span>{{ $t("Del") }}</span>-->
                <!--                                                <span class="checkmark"></span>-->
                <!--                                            </label>-->
                <!--                                        </v-col>-->

                <!--                                        &lt;!&ndash;Suppliers Import &ndash;&gt;-->
                <!--                                         <v-col cols="12" md="6">-->
                <!--                                            <label-->
                <!--                                                class="checkbox checkbox-outline-primary"-->
                <!--                                            >-->
                <!--                                                <input-->
                <!--                                                    type="checkbox"-->
                <!--                                                    checked-->
                <!--                                                    v-model="permissions"-->
                <!--                                                    value="Suppliers_import"-->
                <!--                                                />-->
                <!--                                                <span>{{-->
                <!--                                                    $t("Import_Suppliers")-->
                <!--                                                }}</span>-->
                <!--                                                <span class="checkmark"></span>-->
                <!--                                            </label>-->
                <!--                                        </v-col>-->

                <!--                                        &lt;!&ndash;pay_all_purchase_due_at_a_time &ndash;&gt;-->
                <!--                                         <v-col cols="12">-->
                <!--                                            <label-->
                <!--                                                class="checkbox checkbox-outline-primary"-->
                <!--                                            >-->
                <!--                                                <input-->
                <!--                                                    type="checkbox"-->
                <!--                                                    checked-->
                <!--                                                    v-model="permissions"-->
                <!--                                                    value="pay_supplier_due"-->
                <!--                                                />-->
                <!--                                                <span>{{-->
                <!--                                                    $t(-->
                <!--                                                        "pay_all_purchase_due_at_a_time"-->
                <!--                                                    )-->
                <!--                                                }}</span>-->
                <!--                                                <span class="checkmark"></span>-->
                <!--                                            </label>-->
                <!--                                        </v-col>-->

                <!--                                        &lt;!&ndash;pay_all_purchase_return_due_at_a_time &ndash;&gt;-->
                <!--                                         <v-col cols="12">-->
                <!--                                            <label-->
                <!--                                                class="checkbox checkbox-outline-primary"-->
                <!--                                            >-->
                <!--                                                <input-->
                <!--                                                    type="checkbox"-->
                <!--                                                    checked-->
                <!--                                                    v-model="permissions"-->
                <!--                                                    value="pay_purchase_return_due"-->
                <!--                                                />-->
                <!--                                                <span>{{-->
                <!--                                                    $t(-->
                <!--                                                        "pay_all_purchase_return_due_at_a_time"-->
                <!--                                                    )-->
                <!--                                                }}</span>-->
                <!--                                                <span class="checkmark"></span>-->
                <!--                                            </label>-->
                <!--                                        </v-col>-->
                <!--                                    </v-row>-->
                <!--                                </v-card-text>-->
                <!--                            </v-card-body>-->
                <!--                        </v-collapse>-->
                <!--                    </v-card>-->
                <!--                </v-col>-->

                <!--                &lt;!&ndash; Reports &ndash;&gt;-->
                <!--                <v-col md="4">-->
                <!--                    <v-card no-body class="ul-card__border-radius">-->
                <!--                        <v-card-header-->
                <!--                            header-tag="header"-->
                <!--                            class="p-1"-->
                <!--                            role="tab"-->
                <!--                        >-->
                <!--                            <v-button-->
                <!--                                class="card-title mv-0"-->
                <!--                                block-->
                <!--                                href="#"-->
                <!--                                v-v-toggle.panel-Reports-->
                <!--                                variant="transparent"-->
                <!--                                >{{ $t("Reports") }}-->
                <!--                            </v-button>-->
                <!--                        </v-card-header>-->
                <!--                        <v-collapse-->
                <!--                            id="panel-Reports"-->
                <!--                            :visible="true"-->
                <!--                            accordion="my-accordion17"-->
                <!--                            role="tabpanel"-->
                <!--                        >-->
                <!--                            <v-card-body>-->
                <!--                                <v-card-text>-->
                <!--                                    <v-row>-->
                <!--                                        &lt;!&ndash;Reports_payments_Sales  &ndash;&gt;-->
                <!--                                        &lt;!&ndash;Reports_payments_Purchases  &ndash;&gt;-->
                <!--                                         <v-col cols="12">-->
                <!--                                            <label-->
                <!--                                                class="checkbox checkbox-outline-primary"-->
                <!--                                            >-->
                <!--                                                <input-->
                <!--                                                    type="checkbox"-->
                <!--                                                    checked-->
                <!--                                                    v-model="permissions"-->
                <!--                                                    value="Reports_payments_Purchases"-->
                <!--                                                />-->
                <!--                                                <span>{{-->
                <!--                                                    $t(-->
                <!--                                                        "Reports_payments_Purchases"-->
                <!--                                                    )-->
                <!--                                                }}</span>-->
                <!--                                                <span class="checkmark"></span>-->
                <!--                                            </label>-->
                <!--                                        </v-col>-->
                <!--                                        &lt;!&ndash;Reports_payments_Sale_Returns  &ndash;&gt;-->
                <!--                                         <v-col cols="12">-->
                <!--                                            <label-->
                <!--                                                class="checkbox checkbox-outline-primary"-->
                <!--                                            >-->
                <!--                                                <input-->
                <!--                                                    type="checkbox"-->
                <!--                                                    checked-->
                <!--                                                    v-model="permissions"-->
                <!--                                                    value="Reports_payments_Sale_Returns"-->
                <!--                                                />-->
                <!--                                                <span>{{-->
                <!--                                                    $t(-->
                <!--                                                        "Reports_payments_Sale_Return"-->
                <!--                                                    )-->
                <!--                                                }}</span>-->
                <!--                                                <span class="checkmark"></span>-->
                <!--                                            </label>-->
                <!--                                        </v-col>-->
                <!--                                        &lt;!&ndash;Reports_payments_purchase_Return  &ndash;&gt;-->
                <!--                                         <v-col cols="12">-->
                <!--                                            <label-->
                <!--                                                class="checkbox checkbox-outline-primary"-->
                <!--                                            >-->
                <!--                                                <input-->
                <!--                                                    type="checkbox"-->
                <!--                                                    checked-->
                <!--                                                    v-model="permissions"-->
                <!--                                                    value="Reports_payments_purchase_Return"-->
                <!--                                                />-->
                <!--                                                <span>{{-->
                <!--                                                    $t(-->
                <!--                                                        "Reports_payments_Purchase_Return"-->
                <!--                                                    )-->
                <!--                                                }}</span>-->
                <!--                                                <span class="checkmark"></span>-->
                <!--                                            </label>-->
                <!--                                        </v-col>-->
                <!--                                        &lt;!&ndash;Purchases Reports &ndash;&gt;-->
                <!--                                         <v-col cols="12">-->
                <!--                                            <label-->
                <!--                                                class="checkbox checkbox-outline-primary"-->
                <!--                                            >-->
                <!--                                                <input-->
                <!--                                                    type="checkbox"-->
                <!--                                                    checked-->
                <!--                                                    v-model="permissions"-->
                <!--                                                    value="Reports_purchase"-->
                <!--                                                />-->
                <!--                                                <span>{{-->
                <!--                                                    $t("PurchasesReport")-->
                <!--                                                }}</span>-->
                <!--                                                <span class="checkmark"></span>-->
                <!--                                            </label>-->
                <!--                                        </v-col>-->
                <!--                                        &lt;!&ndash;Suppliers Reports &ndash;&gt;-->
                <!--                                         <v-col cols="12">-->
                <!--                                            <label-->
                <!--                                                class="checkbox checkbox-outline-primary"-->
                <!--                                            >-->
                <!--                                                <input-->
                <!--                                                    type="checkbox"-->
                <!--                                                    checked-->
                <!--                                                    v-model="permissions"-->
                <!--                                                    value="Reports_suppliers"-->
                <!--                                                />-->
                <!--                                                <span>{{-->
                <!--                                                    $t("SuppliersReport")-->
                <!--                                                }}</span>-->
                <!--                                                <span class="checkmark"></span>-->
                <!--                                            </label>-->
                <!--                                        </v-col>-->

                <!--                                        &lt;!&ndash;product_purchases_report&ndash;&gt;-->
                <!--                                         <v-col cols="12">-->
                <!--                                            <label-->
                <!--                                                class="checkbox checkbox-outline-primary"-->
                <!--                                            >-->
                <!--                                                <input-->
                <!--                                                    type="checkbox"-->
                <!--                                                    checked-->
                <!--                                                    v-model="permissions"-->
                <!--                                                    value="product_purchases_report"-->
                <!--                                                />-->
                <!--                                                <span>{{-->
                <!--                                                    $t(-->
                <!--                                                        "Product_purchases_report"-->
                <!--                                                    )-->
                <!--                                                }}</span>-->
                <!--                                                <span class="checkmark"></span>-->
                <!--                                            </label>-->
                <!--                                        </v-col>-->
                <!--                                    </v-row>-->
                <!--                                </v-card-text>-->
                <!--                            </v-card-body>-->
                <!--                        </v-collapse>-->
                <!--                    </v-card>-->
                <!--                </v-col>-->

                <!-- Settings -->
                <!--                 <v-col cols="12" md="6">-->
                <!--                    <v-card no-body class="ul-card__border-radius">-->
                <!--                        <v-card-header-->
                <!--                            header-tag="header"-->
                <!--                            class="p-1"-->
                <!--                            role="tab"-->
                <!--                        >-->
                <!--                            <v-button-->
                <!--                                class="card-title mv-0"-->
                <!--                                block-->
                <!--                                href="#"-->
                <!--                                v-v-toggle.panel-Settings-->
                <!--                                variant="transparent"-->
                <!--                                >{{ $t("Settings") }}-->
                <!--                            </v-button>-->
                <!--                        </v-card-header>-->
                <!--                        <v-collapse-->
                <!--                            id="panel-Settings"-->
                <!--                            :visible="true"-->
                <!--                            accordion="my-accordion18"-->
                <!--                            role="tabpanel"-->
                <!--                        >-->
                <!--                            <v-card-body>-->
                <!--                                <v-card-text>-->
                <!--                                    <v-row>-->
                <!--                                        &lt;!&ndash;Settings System &ndash;&gt;-->
                <!--                                         <v-col cols="12" md="6">-->
                <!--                                            <label-->
                <!--                                                class="checkbox checkbox-outline-primary"-->
                <!--                                            >-->
                <!--                                                <input-->
                <!--                                                    type="checkbox"-->
                <!--                                                    checked-->
                <!--                                                    v-model="permissions"-->
                <!--                                                    value="setting_system"-->
                <!--                                                />-->
                <!--                                                <span-->
                <!--                                                    >{{ $t("SystemSettings") }}-->
                <!--                                                    +-->
                <!--                                                    {{-->
                <!--                                                        $t("update_settings")-->
                <!--                                                    }}</span-->
                <!--                                                >-->
                <!--                                                <span class="checkmark"></span>-->
                <!--                                            </label>-->
                <!--                                        </v-col>-->

                <!--                                        &lt;!&ndash;sms_settings  &ndash;&gt;-->
                <!--                                         <v-col cols="12" md="6">-->
                <!--                                            <label-->
                <!--                                                class="checkbox checkbox-outline-primary"-->
                <!--                                            >-->
                <!--                                                <input-->
                <!--                                                    type="checkbox"-->
                <!--                                                    checked-->
                <!--                                                    v-model="permissions"-->
                <!--                                                    value="sms_settings"-->
                <!--                                                />-->
                <!--                                                <span>{{-->
                <!--                                                    $t("sms_settings")-->
                <!--                                                }}</span>-->
                <!--                                                <span class="checkmark"></span>-->
                <!--                                            </label>-->
                <!--                                        </v-col>-->

                <!--                                        &lt;!&ndash;pos_settings  &ndash;&gt;-->
                <!--                                         <v-col cols="12" md="6">-->
                <!--                                            <label-->
                <!--                                                class="checkbox checkbox-outline-primary"-->
                <!--                                            >-->
                <!--                                                <input-->
                <!--                                                    type="checkbox"-->
                <!--                                                    checked-->
                <!--                                                    v-model="permissions"-->
                <!--                                                    value="pos_settings"-->
                <!--                                                />-->
                <!--                                                <span>{{-->
                <!--                                                    $t("pos_settings")-->
                <!--                                                }}</span>-->
                <!--                                                <span class="checkmark"></span>-->
                <!--                                            </label>-->
                <!--                                        </v-col>-->

                <!--                                        &lt;!&ndash;payment_gateway  &ndash;&gt;-->
                <!--                                         <v-col cols="12" md="6">-->
                <!--                                            <label-->
                <!--                                                class="checkbox checkbox-outline-primary"-->
                <!--                                            >-->
                <!--                                                <input-->
                <!--                                                    type="checkbox"-->
                <!--                                                    checked-->
                <!--                                                    v-model="permissions"-->
                <!--                                                    value="payment_gateway"-->
                <!--                                                />-->
                <!--                                                <span>{{-->
                <!--                                                    $t("payment_gateway")-->
                <!--                                                }}</span>-->
                <!--                                                <span class="checkmark"></span>-->
                <!--                                            </label>-->
                <!--                                        </v-col>-->

                <!--                                        &lt;!&ndash;mail_settings  &ndash;&gt;-->
                <!--                                         <v-col cols="12" md="6">-->
                <!--                                            <label-->
                <!--                                                class="checkbox checkbox-outline-primary"-->
                <!--                                            >-->
                <!--                                                <input-->
                <!--                                                    type="checkbox"-->
                <!--                                                    checked-->
                <!--                                                    v-model="permissions"-->
                <!--                                                    value="mail_settings"-->
                <!--                                                />-->
                <!--                                                <span>{{-->
                <!--                                                    $t("mail_settings")-->
                <!--                                                }}</span>-->
                <!--                                                <span class="checkmark"></span>-->
                <!--                                            </label>-->
                <!--                                        </v-col>-->

                <!--                                        &lt;!&ndash;Currency  &ndash;&gt;-->
                <!--                                         <v-col cols="12" md="6">-->
                <!--                                            <label-->
                <!--                                                class="checkbox checkbox-outline-primary"-->
                <!--                                            >-->
                <!--                                                <input-->
                <!--                                                    type="checkbox"-->
                <!--                                                    checked-->
                <!--                                                    v-model="permissions"-->
                <!--                                                    value="currency"-->
                <!--                                                />-->
                <!--                                                <span>{{-->
                <!--                                                    $t("Currencies")-->
                <!--                                                }}</span>-->
                <!--                                                <span class="checkmark"></span>-->
                <!--                                            </label>-->
                <!--                                        </v-col>-->
            </v-row>
            <!-- End row -->
        </form-view>
    </Layout>
</template>
