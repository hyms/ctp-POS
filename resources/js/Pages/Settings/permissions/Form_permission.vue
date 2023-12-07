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
const permissions = ref([]);
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
        if (editmode.value === false) {
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
    permissions.value = [];
    role.value = {
        name: "",
        description: "",
    };
}

watch(
    () => [props.roleItem],
    () => {
        if (props.roleItem != null) {
            console.log(props.roleItem);
            console.log(props.permissionsItems);
            editmode.value = true;
        } else {
            resetForm();
            editmode.value = false;
        }
    }
);
onMounted(() => {
    if (props.roleItem != null) {
        console.log(props.permissionsItem);
        role.value = props.roleItem;
        permissions.value = props.permissionsItem;
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
                <v-col cols="12" md="4">
                    <v-expansion-panels>
                        <v-expansion-panel>
                            <v-expansion-panel-title
                                >{{ labelsNew.dashboard }}
                            </v-expansion-panel-title>
                            <v-expansion-panel-text>
                                <v-row>
                                    <!--dashboard -->
                                    <v-col cols="12" md="6">
                                        <v-checkbox
                                            color="primary"
                                            v-model="permissions['dashboard']"
                                            :label="labelsNew.dashboard"
                                            hide-details="auto"
                                        >
                                            <v-tooltip
                                                activator="parent"
                                                location="top"
                                            >
                                                Si no está marcado, solo se
                                                mostrará un mensaje de
                                                bienvenida en el panel de
                                                control.
                                            </v-tooltip>
                                        </v-checkbox>
                                    </v-col>
                                </v-row>
                            </v-expansion-panel-text>
                        </v-expansion-panel>
                    </v-expansion-panels>
                </v-col>

                <!--Users -->
                <v-col md="4">
                    <v-expansion-panels>
                        <v-expansion-panel>
                            <v-expansion-panel-title
                                >{{ labelsNew.UserManagement }}
                            </v-expansion-panel-title>
                            <v-expansion-panel-text>
                                <v-row>
                                    <!--Users View -->
                                    <v-col cols="12" md="6">
                                        <v-checkbox
                                            color="primary"
                                            v-model="permissions['users_view']"
                                            :label="labelsNew.View"
                                            hide-details="auto"
                                        ></v-checkbox>
                                    </v-col>
                                    <!--Users ADD -->
                                    <v-col cols="12" md="6">
                                        <v-checkbox
                                            color="primary"
                                            v-model="permissions['users_add']"
                                            :label="labelsNew.Add"
                                            hide-details="auto"
                                        ></v-checkbox>
                                    </v-col>
                                    <!--Users Edit -->
                                    <v-col cols="12" md="6">
                                        <v-checkbox
                                            color="primary"
                                            v-model="permissions['users_edit']"
                                            :label="labelsNew.Edit"
                                            hide-details="auto"
                                        ></v-checkbox>
                                    </v-col>
                                    <!--Users Delete -->
                                    <v-col cols="12" md="6">
                                        <v-checkbox
                                            color="primary"
                                            v-model="
                                                permissions['users_delete']
                                            "
                                            :label="labelsNew.Del"
                                            hide-details="auto"
                                        ></v-checkbox>
                                    </v-col>
                                    <!--Users record view -->
                                    <v-col cols="12">
                                        <v-checkbox
                                            color="primary"
                                            v-model="permissions['record_view']"
                                            :label="labelsNew.ShowAll"
                                            hide-details="auto"
                                        ></v-checkbox>
                                    </v-col>
                                </v-row>
                            </v-expansion-panel-text>
                        </v-expansion-panel>
                    </v-expansion-panels>
                </v-col>

                <!--  Permissions -->
                <v-col md="4">
                    <v-expansion-panels>
                        <v-expansion-panel>
                            <v-expansion-panel-title>
                                {{ labelsNew.UserPermissions }}
                            </v-expansion-panel-title>
                            <v-expansion-panel-text>
                                <v-row>
                                    <!--Permissions View -->
                                    <v-col cols="12" md="6">
                                        <v-checkbox
                                            color="primary"
                                            v-model="
                                                permissions['permissions_view']
                                            "
                                            :label="labelsNew.View"
                                            hide-details="auto"
                                        ></v-checkbox>
                                    </v-col>
                                    <!--Permissions ADD -->
                                    <v-col cols="12" md="6">
                                        <v-checkbox
                                            color="primary"
                                            v-model="
                                                permissions['permissions_add']
                                            "
                                            :label="labelsNew.Add"
                                            hide-details="auto"
                                        ></v-checkbox>
                                    </v-col>
                                    <!--Permissions Edit -->
                                    <v-col cols="12" md="6">
                                        <v-checkbox
                                            color="primary"
                                            v-model="
                                                permissions['permissions_edit']
                                            "
                                            :label="labelsNew.Edit"
                                            hide-details="auto"
                                        ></v-checkbox>
                                    </v-col>
                                    <!--Permissions Delete -->
                                    <v-col cols="12" md="6">
                                        <v-checkbox
                                            color="primary"
                                            v-model="
                                                permissions[
                                                    'permissions_delete'
                                                ]
                                            "
                                            :label="labelsNew.Del"
                                            hide-details="auto"
                                        ></v-checkbox>
                                    </v-col>
                                </v-row>
                            </v-expansion-panel-text>
                        </v-expansion-panel>
                    </v-expansion-panels>
                </v-col>

                <!--  Products -->
                <!--<v-col md="4">-->
                <!--    <v-card no-body class="ul-card__border-radius">-->
                <!--        <v-card-header-->
                <!--                            header-tag="header"-->
                <!--                            class="p-1"-->
                <!--                            role="tab"-->
                <!--                        >-->
                <!--                            <v-button-->
                <!--                                class="card-title mv-0"-->
                <!--                                block-->
                <!--                                href="#"-->
                <!--                                v-v-toggle.panel-Products-->
                <!--                                variant="transparent"-->
                <!--                                >{{ $t("Products") }}-->
                <!--                            </v-button>-->
                <!--                        </v-card-header>-->
                <!--                        <v-collapse-->
                <!--                            id="panel-Products"-->
                <!--                            :visible="true"-->
                <!--                            accordion="my-accordion3"-->
                <!--                            role="tabpanel"-->
                <!--                        >-->
                <!--                            <v-card-body>-->
                <!--                                <v-card-text>-->
                <!--                                    <v-row>-->
                <!--                                        &lt;!&ndash;Products View &ndash;&gt;-->
                <!--                                         <v-col cols="12" md="6">-->
                <!--                                            <label-->
                <!--                                                class="checkbox checkbox-outline-primary"-->
                <!--                                            >-->
                <!--                                                <input-->
                <!--                                                    type="checkbox"-->
                <!--                                                    checked-->
                <!--                                                    v-model="permissions"-->
                <!--                                                    value="products_view"-->
                <!--                                                />-->
                <!--                                                <span>{{ $t("View") }}</span>-->
                <!--                                                <span class="checkmark"></span>-->
                <!--                                            </label>-->
                <!--                                        </v-col>-->
                <!--                                        &lt;!&ndash;Products ADD &ndash;&gt;-->
                <!--                                         <v-col cols="12" md="6">-->
                <!--                                            <label-->
                <!--                                                class="checkbox checkbox-outline-primary"-->
                <!--                                            >-->
                <!--                                                <input-->
                <!--                                                    type="checkbox"-->
                <!--                                                    checked-->
                <!--                                                    v-model="permissions"-->
                <!--                                                    value="products_add"-->
                <!--                                                />-->
                <!--                                                <span>{{ $t("Add") }}</span>-->
                <!--                                                <span class="checkmark"></span>-->
                <!--                                            </label>-->
                <!--                                        </v-col>-->
                <!--                                        &lt;!&ndash;Products Edit &ndash;&gt;-->
                <!--                                         <v-col cols="12" md="6">-->
                <!--                                            <label-->
                <!--                                                class="checkbox checkbox-outline-primary"-->
                <!--                                            >-->
                <!--                                                <input-->
                <!--                                                    type="checkbox"-->
                <!--                                                    checked-->
                <!--                                                    v-model="permissions"-->
                <!--                                                    value="products_edit"-->
                <!--                                                />-->
                <!--                                                <span>{{ $t("Edit") }}</span>-->
                <!--                                                <span class="checkmark"></span>-->
                <!--                                            </label>-->
                <!--                                        </v-col>-->
                <!--                                        &lt;!&ndash;Products Delete &ndash;&gt;-->
                <!--                                         <v-col cols="12" md="6">-->
                <!--                                            <label-->
                <!--                                                class="checkbox checkbox-outline-primary"-->
                <!--                                            >-->
                <!--                                                <input-->
                <!--                                                    type="checkbox"-->
                <!--                                                    checked-->
                <!--                                                    v-model="permissions"-->
                <!--                                                    value="products_delete"-->
                <!--                                                />-->
                <!--                                                <span>{{ $t("Del") }}</span>-->
                <!--                                                <span class="checkmark"></span>-->
                <!--                                            </label>-->
                <!--                                        </v-col>-->
                <!--                                        &lt;!&ndash;Products Barcode &ndash;&gt;-->
                <!--                                         <v-col cols="12" md="6">-->
                <!--                                            <label-->
                <!--                                                class="checkbox checkbox-outline-primary"-->
                <!--                                            >-->
                <!--                                                <input-->
                <!--                                                    type="checkbox"-->
                <!--                                                    checked-->
                <!--                                                    v-model="permissions"-->
                <!--                                                    value="barcode_view"-->
                <!--                                                />-->
                <!--                                                <span>{{ $t("Barcode") }}</span>-->
                <!--                                                <span class="checkmark"></span>-->
                <!--                                            </label>-->
                <!--                                        </v-col>-->

                <!--                                        &lt;!&ndash;Products Import &ndash;&gt;-->
                <!--                                         <v-col cols="12" md="6">-->
                <!--                                            <label-->
                <!--                                                class="checkbox checkbox-outline-primary"-->
                <!--                                            >-->
                <!--                                                <input-->
                <!--                                                    type="checkbox"-->
                <!--                                                    checked-->
                <!--                                                    v-model="permissions"-->
                <!--                                                    value="product_import"-->
                <!--                                                />-->
                <!--                                                <span>{{-->
                <!--                                                    $t("import_products")-->
                <!--                                                }}</span>-->
                <!--                                                <span class="checkmark"></span>-->
                <!--                                            </label>-->
                <!--                                        </v-col>-->
                <!--                                        &lt;!&ndash;Category &ndash;&gt;-->
                <!--                                         <v-col cols="12" md="6">-->
                <!--                                            <label-->
                <!--                                                class="checkbox checkbox-outline-primary"-->
                <!--                                            >-->
                <!--                                                <input-->
                <!--                                                    type="checkbox"-->
                <!--                                                    checked-->
                <!--                                                    v-model="permissions"-->
                <!--                                                    value="category"-->
                <!--                                                />-->
                <!--                                                <span>{{-->
                <!--                                                    $t("Categories")-->
                <!--                                                }}</span>-->
                <!--                                                <span class="checkmark"></span>-->
                <!--                                            </label>-->
                <!--                                        </v-col>-->
                <!--                                        &lt;!&ndash;Brand  &ndash;&gt;-->
                <!--                                         <v-col cols="12" md="6">-->
                <!--                                            <label-->
                <!--                                                class="checkbox checkbox-outline-primary"-->
                <!--                                            >-->
                <!--                                                <input-->
                <!--                                                    type="checkbox"-->
                <!--                                                    checked-->
                <!--                                                    v-model="permissions"-->
                <!--                                                    value="brand"-->
                <!--                                                />-->
                <!--                                                <span>{{ $t("Brand") }}</span>-->
                <!--                                                <span class="checkmark"></span>-->
                <!--                                            </label>-->
                <!--                                        </v-col>-->
                <!--                                        &lt;!&ndash;Unit  &ndash;&gt;-->
                <!--                                         <v-col cols="12" md="6">-->
                <!--                                            <label-->
                <!--                                                class="checkbox checkbox-outline-primary"-->
                <!--                                            >-->
                <!--                                                <input-->
                <!--                                                    type="checkbox"-->
                <!--                                                    checked-->
                <!--                                                    v-model="permissions"-->
                <!--                                                    value="unit"-->
                <!--                                                />-->
                <!--                                                <span>{{ $t("Units") }}</span>-->
                <!--                                                <span class="checkmark"></span>-->
                <!--                                            </label>-->
                <!--                                        </v-col>-->
                <!--                                    </v-row>-->
                <!--                                </v-card-text>-->
                <!--                            </v-card-body>-->
                <!--                        </v-collapse>-->
                <!--                    </v-card>-->
                <!--                </v-col>-->

                <!--                &lt;!&ndash;  Adjustment &ndash;&gt;-->
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
                <!--                                v-v-toggle.panel-Adjustment-->
                <!--                                variant="transparent"-->
                <!--                                >{{ $t("StockAdjustement") }}-->
                <!--                            </v-button>-->
                <!--                        </v-card-header>-->
                <!--                        <v-collapse-->
                <!--                            id="panel-Adjustment"-->
                <!--                            :visible="true"-->
                <!--                            accordion="my-accordion4"-->
                <!--                            role="tabpanel"-->
                <!--                        >-->
                <!--                            <v-card-body>-->
                <!--                                <v-card-text>-->
                <!--                                    <v-row>-->
                <!--                                        &lt;!&ndash;Adjustment View &ndash;&gt;-->
                <!--                                         <v-col cols="12" md="6">-->
                <!--                                            <label-->
                <!--                                                class="checkbox checkbox-outline-primary"-->
                <!--                                            >-->
                <!--                                                <input-->
                <!--                                                    type="checkbox"-->
                <!--                                                    checked-->
                <!--                                                    v-model="permissions"-->
                <!--                                                    value="adjustment_view"-->
                <!--                                                />-->
                <!--                                                <span>{{ $t("View") }}</span>-->
                <!--                                                <span class="checkmark"></span>-->
                <!--                                            </label>-->
                <!--                                        </v-col>-->
                <!--                                        &lt;!&ndash;Adjustment ADD &ndash;&gt;-->
                <!--                                         <v-col cols="12" md="6">-->
                <!--                                            <label-->
                <!--                                                class="checkbox checkbox-outline-primary"-->
                <!--                                            >-->
                <!--                                                <input-->
                <!--                                                    type="checkbox"-->
                <!--                                                    checked-->
                <!--                                                    v-model="permissions"-->
                <!--                                                    value="adjustment_add"-->
                <!--                                                />-->
                <!--                                                <span>{{ $t("Add") }}</span>-->
                <!--                                                <span class="checkmark"></span>-->
                <!--                                            </label>-->
                <!--                                        </v-col>-->
                <!--                                        &lt;!&ndash;Adjustment Edit &ndash;&gt;-->
                <!--                                         <v-col cols="12" md="6">-->
                <!--                                            <label-->
                <!--                                                class="checkbox checkbox-outline-primary"-->
                <!--                                            >-->
                <!--                                                <input-->
                <!--                                                    type="checkbox"-->
                <!--                                                    checked-->
                <!--                                                    v-model="permissions"-->
                <!--                                                    value="adjustment_edit"-->
                <!--                                                />-->
                <!--                                                <span>{{ $t("Edit") }}</span>-->
                <!--                                                <span class="checkmark"></span>-->
                <!--                                            </label>-->
                <!--                                        </v-col>-->
                <!--                                        &lt;!&ndash;Adjustment Delete &ndash;&gt;-->
                <!--                                         <v-col cols="12" md="6">-->
                <!--                                            <label-->
                <!--                                                class="checkbox checkbox-outline-primary"-->
                <!--                                            >-->
                <!--                                                <input-->
                <!--                                                    type="checkbox"-->
                <!--                                                    checked-->
                <!--                                                    v-model="permissions"-->
                <!--                                                    value="adjustment_delete"-->
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

                <!--                &lt;!&ndash;  Transfer &ndash;&gt;-->
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
                <!--                                v-v-toggle.panel-Transfer-->
                <!--                                variant="transparent"-->
                <!--                                >{{ $t("StockTransfers") }}-->
                <!--                            </v-button>-->
                <!--                        </v-card-header>-->
                <!--                        <v-collapse-->
                <!--                            id="panel-Transfer"-->
                <!--                            :visible="true"-->
                <!--                            accordion="my-accordion5"-->
                <!--                            role="tabpanel"-->
                <!--                        >-->
                <!--                            <v-card-body>-->
                <!--                                <v-card-text>-->
                <!--                                    <v-row>-->
                <!--                                        &lt;!&ndash;Transfer View &ndash;&gt;-->
                <!--                                         <v-col cols="12" md="6">-->
                <!--                                            <label-->
                <!--                                                class="checkbox checkbox-outline-primary"-->
                <!--                                            >-->
                <!--                                                <input-->
                <!--                                                    type="checkbox"-->
                <!--                                                    checked-->
                <!--                                                    v-model="permissions"-->
                <!--                                                    value="transfer_view"-->
                <!--                                                />-->
                <!--                                                <span>{{ $t("View") }}</span>-->
                <!--                                                <span class="checkmark"></span>-->
                <!--                                            </label>-->
                <!--                                        </v-col>-->
                <!--                                        &lt;!&ndash;Transfer ADD &ndash;&gt;-->
                <!--                                         <v-col cols="12" md="6">-->
                <!--                                            <label-->
                <!--                                                class="checkbox checkbox-outline-primary"-->
                <!--                                            >-->
                <!--                                                <input-->
                <!--                                                    type="checkbox"-->
                <!--                                                    checked-->
                <!--                                                    v-model="permissions"-->
                <!--                                                    value="transfer_add"-->
                <!--                                                />-->
                <!--                                                <span>{{ $t("Add") }}</span>-->
                <!--                                                <span class="checkmark"></span>-->
                <!--                                            </label>-->
                <!--                                        </v-col>-->
                <!--                                        &lt;!&ndash;Transfer Edit &ndash;&gt;-->
                <!--                                         <v-col cols="12" md="6">-->
                <!--                                            <label-->
                <!--                                                class="checkbox checkbox-outline-primary"-->
                <!--                                            >-->
                <!--                                                <input-->
                <!--                                                    type="checkbox"-->
                <!--                                                    checked-->
                <!--                                                    v-model="permissions"-->
                <!--                                                    value="transfer_edit"-->
                <!--                                                />-->
                <!--                                                <span>{{ $t("Edit") }}</span>-->
                <!--                                                <span class="checkmark"></span>-->
                <!--                                            </label>-->
                <!--                                        </v-col>-->
                <!--                                        &lt;!&ndash;Transfer Delete &ndash;&gt;-->
                <!--                                         <v-col cols="12" md="6">-->
                <!--                                            <label-->
                <!--                                                class="checkbox checkbox-outline-primary"-->
                <!--                                            >-->
                <!--                                                <input-->
                <!--                                                    type="checkbox"-->
                <!--                                                    checked-->
                <!--                                                    v-model="permissions"-->
                <!--                                                    value="transfer_delete"-->
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

                <!--                &lt;!&ndash;  Expense &ndash;&gt;-->
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
                <!--                                v-v-toggle.panel-Expense-->
                <!--                                variant="transparent"-->
                <!--                                >{{ $t("Expenses") }}-->
                <!--                            </v-button>-->
                <!--                        </v-card-header>-->
                <!--                        <v-collapse-->
                <!--                            id="panel-Expense"-->
                <!--                            :visible="true"-->
                <!--                            accordion="my-accordion6"-->
                <!--                            role="tabpanel"-->
                <!--                        >-->
                <!--                            <v-card-body>-->
                <!--                                <v-card-text>-->
                <!--                                    <v-row>-->
                <!--                                        &lt;!&ndash;Expense View &ndash;&gt;-->
                <!--                                         <v-col cols="12" md="6">-->
                <!--                                            <label-->
                <!--                                                class="checkbox checkbox-outline-primary"-->
                <!--                                            >-->
                <!--                                                <input-->
                <!--                                                    type="checkbox"-->
                <!--                                                    checked-->
                <!--                                                    v-model="permissions"-->
                <!--                                                    value="expense_view"-->
                <!--                                                />-->
                <!--                                                <span>{{ $t("View") }}</span>-->
                <!--                                                <span class="checkmark"></span>-->
                <!--                                            </label>-->
                <!--                                        </v-col>-->
                <!--                                        &lt;!&ndash;Expense ADD &ndash;&gt;-->
                <!--                                         <v-col cols="12" md="6">-->
                <!--                                            <label-->
                <!--                                                class="checkbox checkbox-outline-primary"-->
                <!--                                            >-->
                <!--                                                <input-->
                <!--                                                    type="checkbox"-->
                <!--                                                    checked-->
                <!--                                                    v-model="permissions"-->
                <!--                                                    value="expense_add"-->
                <!--                                                />-->
                <!--                                                <span>{{ $t("Add") }}</span>-->
                <!--                                                <span class="checkmark"></span>-->
                <!--                                            </label>-->
                <!--                                        </v-col>-->
                <!--                                        &lt;!&ndash;Expense Edit &ndash;&gt;-->
                <!--                                         <v-col cols="12" md="6">-->
                <!--                                            <label-->
                <!--                                                class="checkbox checkbox-outline-primary"-->
                <!--                                            >-->
                <!--                                                <input-->
                <!--                                                    type="checkbox"-->
                <!--                                                    checked-->
                <!--                                                    v-model="permissions"-->
                <!--                                                    value="expense_edit"-->
                <!--                                                />-->
                <!--                                                <span>{{ $t("Edit") }}</span>-->
                <!--                                                <span class="checkmark"></span>-->
                <!--                                            </label>-->
                <!--                                        </v-col>-->
                <!--                                        &lt;!&ndash;Expense Delete &ndash;&gt;-->
                <!--                                         <v-col cols="12" md="6">-->
                <!--                                            <label-->
                <!--                                                class="checkbox checkbox-outline-primary"-->
                <!--                                            >-->
                <!--                                                <input-->
                <!--                                                    type="checkbox"-->
                <!--                                                    checked-->
                <!--                                                    v-model="permissions"-->
                <!--                                                    value="expense_delete"-->
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

                <!--                &lt;!&ndash; Sales &ndash;&gt;-->
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
                <!--                                v-v-toggle.panel-Sales-->
                <!--                                variant="transparent"-->
                <!--                                >{{ $t("Sales") }}-->
                <!--                            </v-button>-->
                <!--                        </v-card-header>-->
                <!--                        <v-collapse-->
                <!--                            id="panel-Sales"-->
                <!--                            :visible="true"-->
                <!--                            accordion="my-accordion7"-->
                <!--                            role="tabpanel"-->
                <!--                        >-->
                <!--                            <v-card-body>-->
                <!--                                <v-card-text>-->
                <!--                                    <v-row>-->
                <!--                                        &lt;!&ndash;Sales View &ndash;&gt;-->
                <!--                                         <v-col cols="12" md="6">-->
                <!--                                            <label-->
                <!--                                                class="checkbox checkbox-outline-primary"-->
                <!--                                            >-->
                <!--                                                <input-->
                <!--                                                    type="checkbox"-->
                <!--                                                    checked-->
                <!--                                                    v-model="permissions"-->
                <!--                                                    value="Sales_view"-->
                <!--                                                />-->
                <!--                                                <span>{{ $t("View") }}</span>-->
                <!--                                                <span class="checkmark"></span>-->
                <!--                                            </label>-->
                <!--                                        </v-col>-->
                <!--                                        &lt;!&ndash;Sales ADD &ndash;&gt;-->
                <!--                                         <v-col cols="12" md="6">-->
                <!--                                            <label-->
                <!--                                                class="checkbox checkbox-outline-primary"-->
                <!--                                            >-->
                <!--                                                <input-->
                <!--                                                    type="checkbox"-->
                <!--                                                    checked-->
                <!--                                                    v-model="permissions"-->
                <!--                                                    value="Sales_add"-->
                <!--                                                />-->
                <!--                                                <span>{{ $t("Add") }}</span>-->
                <!--                                                <span class="checkmark"></span>-->
                <!--                                            </label>-->
                <!--                                        </v-col>-->
                <!--                                        &lt;!&ndash;Sales Edit &ndash;&gt;-->
                <!--                                         <v-col cols="12" md="6">-->
                <!--                                            <label-->
                <!--                                                class="checkbox checkbox-outline-primary"-->
                <!--                                            >-->
                <!--                                                <input-->
                <!--                                                    type="checkbox"-->
                <!--                                                    checked-->
                <!--                                                    v-model="permissions"-->
                <!--                                                    value="Sales_edit"-->
                <!--                                                />-->
                <!--                                                <span>{{ $t("Edit") }}</span>-->
                <!--                                                <span class="checkmark"></span>-->
                <!--                                            </label>-->
                <!--                                        </v-col>-->
                <!--                                        &lt;!&ndash;Sales Delete &ndash;&gt;-->
                <!--                                         <v-col cols="12" md="6">-->
                <!--                                            <label-->
                <!--                                                class="checkbox checkbox-outline-primary"-->
                <!--                                            >-->
                <!--                                                <input-->
                <!--                                                    type="checkbox"-->
                <!--                                                    checked-->
                <!--                                                    v-model="permissions"-->
                <!--                                                    value="Sales_delete"-->
                <!--                                                />-->
                <!--                                                <span>{{ $t("Del") }}</span>-->
                <!--                                                <span class="checkmark"></span>-->
                <!--                                            </label>-->
                <!--                                        </v-col>-->
                <!--                                        &lt;!&ndash;Sales POS &ndash;&gt;-->
                <!--                                         <v-col cols="12">-->
                <!--                                            <label-->
                <!--                                                class="checkbox checkbox-outline-primary"-->
                <!--                                            >-->
                <!--                                                <input-->
                <!--                                                    type="checkbox"-->
                <!--                                                    checked-->
                <!--                                                    v-model="permissions"-->
                <!--                                                    value="Pos_view"-->
                <!--                                                />-->
                <!--                                                <span>{{-->
                <!--                                                    $t("pointofsales")-->
                <!--                                                }}</span>-->
                <!--                                                <span class="checkmark"></span>-->
                <!--                                            </label>-->
                <!--                                        </v-col>-->

                <!--                                        &lt;!&ndash;shipment &ndash;&gt;-->
                <!--                                         <v-col cols="12">-->
                <!--                                            <label-->
                <!--                                                class="checkbox checkbox-outline-primary"-->
                <!--                                            >-->
                <!--                                                <input-->
                <!--                                                    type="checkbox"-->
                <!--                                                    checked-->
                <!--                                                    v-model="permissions"-->
                <!--                                                    value="shipment"-->
                <!--                                                />-->
                <!--                                                <span>{{-->
                <!--                                                    $t("Shipments")-->
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

                <!--                &lt;!&ndash; Payment Sales &ndash;&gt;-->
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
                <!--                                v-v-toggle.panel-Payment-Sales-->
                <!--                                variant="transparent"-->
                <!--                                >{{ $t("PaymentsSales") }}-->
                <!--                            </v-button>-->
                <!--                        </v-card-header>-->
                <!--                        <v-collapse-->
                <!--                            id="panel-Payment-Sales"-->
                <!--                            :visible="true"-->
                <!--                            accordion="my-accordion12"-->
                <!--                            role="tabpanel"-->
                <!--                        >-->
                <!--                            <v-card-body>-->
                <!--                                <v-card-text>-->
                <!--                                    <v-row>-->
                <!--                                        &lt;!&ndash;Payment Sales View &ndash;&gt;-->
                <!--                                         <v-col cols="12" md="6">-->
                <!--                                            <label-->
                <!--                                                class="checkbox checkbox-outline-primary"-->
                <!--                                            >-->
                <!--                                                <input-->
                <!--                                                    type="checkbox"-->
                <!--                                                    checked-->
                <!--                                                    v-model="permissions"-->
                <!--                                                    value="payment_sales_view"-->
                <!--                                                />-->
                <!--                                                <span>{{ $t("View") }}</span>-->
                <!--                                                <span class="checkmark"></span>-->
                <!--                                            </label>-->
                <!--                                        </v-col>-->
                <!--                                        &lt;!&ndash;Payment Sales ADD &ndash;&gt;-->
                <!--                                         <v-col cols="12" md="6">-->
                <!--                                            <label-->
                <!--                                                class="checkbox checkbox-outline-primary"-->
                <!--                                            >-->
                <!--                                                <input-->
                <!--                                                    type="checkbox"-->
                <!--                                                    checked-->
                <!--                                                    v-model="permissions"-->
                <!--                                                    value="payment_sales_add"-->
                <!--                                                />-->
                <!--                                                <span>{{ $t("Add") }}</span>-->
                <!--                                                <span class="checkmark"></span>-->
                <!--                                            </label>-->
                <!--                                        </v-col>-->
                <!--                                        &lt;!&ndash;Payment Sales Edit &ndash;&gt;-->
                <!--                                         <v-col cols="12" md="6">-->
                <!--                                            <label-->
                <!--                                                class="checkbox checkbox-outline-primary"-->
                <!--                                            >-->
                <!--                                                <input-->
                <!--                                                    type="checkbox"-->
                <!--                                                    checked-->
                <!--                                                    v-model="permissions"-->
                <!--                                                    value="payment_sales_edit"-->
                <!--                                                />-->
                <!--                                                <span>{{ $t("Edit") }}</span>-->
                <!--                                                <span class="checkmark"></span>-->
                <!--                                            </label>-->
                <!--                                        </v-col>-->
                <!--                                        &lt;!&ndash;Payment Sales Delete &ndash;&gt;-->
                <!--                                         <v-col cols="12" md="6">-->
                <!--                                            <label-->
                <!--                                                class="checkbox checkbox-outline-primary"-->
                <!--                                            >-->
                <!--                                                <input-->
                <!--                                                    type="checkbox"-->
                <!--                                                    checked-->
                <!--                                                    v-model="permissions"-->
                <!--                                                    value="payment_sales_delete"-->
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
                <!--                                        &lt;!&ndash;Customers View &ndash;&gt;-->
                <!--                                         <v-col cols="12" md="6">-->
                <!--                                            <label-->
                <!--                                                class="checkbox checkbox-outline-primary"-->
                <!--                                            >-->
                <!--                                                <input-->
                <!--                                                    type="checkbox"-->
                <!--                                                    checked-->
                <!--                                                    v-model="permissions"-->
                <!--                                                    value="Customers_view"-->
                <!--                                                />-->
                <!--                                                <span>{{ $t("View") }}</span>-->
                <!--                                                <span class="checkmark"></span>-->
                <!--                                            </label>-->
                <!--                                        </v-col>-->
                <!--                                        &lt;!&ndash;Customers ADD &ndash;&gt;-->
                <!--                                         <v-col cols="12" md="6">-->
                <!--                                            <label-->
                <!--                                                class="checkbox checkbox-outline-primary"-->
                <!--                                            >-->
                <!--                                                <input-->
                <!--                                                    type="checkbox"-->
                <!--                                                    checked-->
                <!--                                                    v-model="permissions"-->
                <!--                                                    value="Customers_add"-->
                <!--                                                />-->
                <!--                                                <span>{{ $t("Add") }}</span>-->
                <!--                                                <span class="checkmark"></span>-->
                <!--                                            </label>-->
                <!--                                        </v-col>-->
                <!--                                        &lt;!&ndash;Customers Edit &ndash;&gt;-->
                <!--                                         <v-col cols="12" md="6">-->
                <!--                                            <label-->
                <!--                                                class="checkbox checkbox-outline-primary"-->
                <!--                                            >-->
                <!--                                                <input-->
                <!--                                                    type="checkbox"-->
                <!--                                                    checked-->
                <!--                                                    v-model="permissions"-->
                <!--                                                    value="Customers_edit"-->
                <!--                                                />-->
                <!--                                                <span>{{ $t("Edit") }}</span>-->
                <!--                                                <span class="checkmark"></span>-->
                <!--                                            </label>-->
                <!--                                        </v-col>-->
                <!--                                        &lt;!&ndash;Customers Delete &ndash;&gt;-->
                <!--                                         <v-col cols="12" md="6">-->
                <!--                                            <label-->
                <!--                                                class="checkbox checkbox-outline-primary"-->
                <!--                                            >-->
                <!--                                                <input-->
                <!--                                                    type="checkbox"-->
                <!--                                                    checked-->
                <!--                                                    v-model="permissions"-->
                <!--                                                    value="Customers_delete"-->
                <!--                                                />-->
                <!--                                                <span>{{ $t("Del") }}</span>-->
                <!--                                                <span class="checkmark"></span>-->
                <!--                                            </label>-->
                <!--                                        </v-col>-->

                <!--                                        &lt;!&ndash;Customers Import &ndash;&gt;-->
                <!--                                         <v-col cols="12" md="6">-->
                <!--                                            <label-->
                <!--                                                class="checkbox checkbox-outline-primary"-->
                <!--                                            >-->
                <!--                                                <input-->
                <!--                                                    type="checkbox"-->
                <!--                                                    checked-->
                <!--                                                    v-model="permissions"-->
                <!--                                                    value="customers_import"-->
                <!--                                                />-->
                <!--                                                <span>{{-->
                <!--                                                    $t("Import_Customers")-->
                <!--                                                }}</span>-->
                <!--                                                <span class="checkmark"></span>-->
                <!--                                            </label>-->
                <!--                                        </v-col>-->

                <!--                                        &lt;!&ndash;pay_all_sell_due_at_a_time &ndash;&gt;-->
                <!--                                         <v-col cols="12">-->
                <!--                                            <label-->
                <!--                                                class="checkbox checkbox-outline-primary"-->
                <!--                                            >-->
                <!--                                                <input-->
                <!--                                                    type="checkbox"-->
                <!--                                                    checked-->
                <!--                                                    v-model="permissions"-->
                <!--                                                    value="pay_due"-->
                <!--                                                />-->
                <!--                                                <span>{{-->
                <!--                                                    $t(-->
                <!--                                                        "pay_all_sell_due_at_a_time"-->
                <!--                                                    )-->
                <!--                                                }}</span>-->
                <!--                                                <span class="checkmark"></span>-->
                <!--                                            </label>-->
                <!--                                        </v-col>-->

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
                <!--                                         <v-col cols="12">-->
                <!--                                            <label-->
                <!--                                                class="checkbox checkbox-outline-primary"-->
                <!--                                            >-->
                <!--                                                <input-->
                <!--                                                    type="checkbox"-->
                <!--                                                    checked-->
                <!--                                                    v-model="permissions"-->
                <!--                                                    value="Reports_payments_Sales"-->
                <!--                                                />-->
                <!--                                                <span>{{-->
                <!--                                                    $t("Reports_payments_Sales")-->
                <!--                                                }}</span>-->
                <!--                                                <span class="checkmark"></span>-->
                <!--                                            </label>-->
                <!--                                        </v-col>-->
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
                <!--                                        &lt;!&ndash; Sales Reports &ndash;&gt;-->
                <!--                                         <v-col cols="12">-->
                <!--                                            <label-->
                <!--                                                class="checkbox checkbox-outline-primary"-->
                <!--                                            >-->
                <!--                                                <input-->
                <!--                                                    type="checkbox"-->
                <!--                                                    checked-->
                <!--                                                    v-model="permissions"-->
                <!--                                                    value="Reports_sales"-->
                <!--                                                />-->
                <!--                                                <span>{{-->
                <!--                                                    $t("SalesReport")-->
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
                <!--                                        &lt;!&ndash; Customers Reports &ndash;&gt;-->
                <!--                                         <v-col cols="12">-->
                <!--                                            <label-->
                <!--                                                class="checkbox checkbox-outline-primary"-->
                <!--                                            >-->
                <!--                                                <input-->
                <!--                                                    type="checkbox"-->
                <!--                                                    checked-->
                <!--                                                    v-model="permissions"-->
                <!--                                                    value="Reports_customers"-->
                <!--                                                />-->
                <!--                                                <span>{{-->
                <!--                                                    $t("CustomersReport")-->
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
                <!--                                        &lt;!&ndash;Proft and Loss &ndash;&gt;-->
                <!--                                         <v-col cols="12">-->
                <!--                                            <label-->
                <!--                                                class="checkbox checkbox-outline-primary"-->
                <!--                                            >-->
                <!--                                                <input-->
                <!--                                                    type="checkbox"-->
                <!--                                                    checked-->
                <!--                                                    v-model="permissions"-->
                <!--                                                    value="Reports_profit"-->
                <!--                                                />-->
                <!--                                                <span>{{-->
                <!--                                                    $t("ProfitandLoss")-->
                <!--                                                }}</span>-->
                <!--                                                <span class="checkmark"></span>-->
                <!--                                            </label>-->
                <!--                                        </v-col>-->
                <!--                                        &lt;!&ndash;Product Quantity Alerts &ndash;&gt;-->
                <!--                                         <v-col cols="12">-->
                <!--                                            <label-->
                <!--                                                class="checkbox checkbox-outline-primary"-->
                <!--                                            >-->
                <!--                                                <input-->
                <!--                                                    type="checkbox"-->
                <!--                                                    checked-->
                <!--                                                    v-model="permissions"-->
                <!--                                                    value="Reports_quantity_alerts"-->
                <!--                                                />-->
                <!--                                                <span>{{-->
                <!--                                                    $t("ProductQuantityAlerts")-->
                <!--                                                }}</span>-->
                <!--                                                <span class="checkmark"></span>-->
                <!--                                            </label>-->
                <!--                                        </v-col>-->

                <!--                                        &lt;!&ndash;Warehouse Stock Chart&ndash;&gt;-->
                <!--                                         <v-col cols="12">-->
                <!--                                            <label-->
                <!--                                                class="checkbox checkbox-outline-primary"-->
                <!--                                            >-->
                <!--                                                <input-->
                <!--                                                    type="checkbox"-->
                <!--                                                    checked-->
                <!--                                                    v-model="permissions"-->
                <!--                                                    value="Warehouse_report"-->
                <!--                                                />-->
                <!--                                                <span>{{-->
                <!--                                                    $t("WarehouseStockChart")-->
                <!--                                                }}</span>-->
                <!--                                                <span class="checkmark"></span>-->
                <!--                                            </label>-->
                <!--                                        </v-col>-->

                <!--                                        &lt;!&ndash;Top_Selling_Products&ndash;&gt;-->
                <!--                                         <v-col cols="12">-->
                <!--                                            <label-->
                <!--                                                class="checkbox checkbox-outline-primary"-->
                <!--                                            >-->
                <!--                                                <input-->
                <!--                                                    type="checkbox"-->
                <!--                                                    checked-->
                <!--                                                    v-model="permissions"-->
                <!--                                                    value="Top_products"-->
                <!--                                                />-->
                <!--                                                <span>{{-->
                <!--                                                    $t("Top_Selling_Products")-->
                <!--                                                }}</span>-->
                <!--                                                <span class="checkmark"></span>-->
                <!--                                            </label>-->
                <!--                                        </v-col>-->

                <!--                                        &lt;!&ndash;Top_customers&ndash;&gt;-->
                <!--                                         <v-col cols="12">-->
                <!--                                            <label-->
                <!--                                                class="checkbox checkbox-outline-primary"-->
                <!--                                            >-->
                <!--                                                <input-->
                <!--                                                    type="checkbox"-->
                <!--                                                    checked-->
                <!--                                                    v-model="permissions"-->
                <!--                                                    value="Top_customers"-->
                <!--                                                />-->
                <!--                                                <span>{{-->
                <!--                                                    $t("Top_customers")-->
                <!--                                                }}</span>-->
                <!--                                                <span class="checkmark"></span>-->
                <!--                                            </label>-->
                <!--                                        </v-col>-->

                <!--                                        &lt;!&ndash;users_report&ndash;&gt;-->
                <!--                                         <v-col cols="12">-->
                <!--                                            <label-->
                <!--                                                class="checkbox checkbox-outline-primary"-->
                <!--                                            >-->
                <!--                                                <input-->
                <!--                                                    type="checkbox"-->
                <!--                                                    checked-->
                <!--                                                    v-model="permissions"-->
                <!--                                                    value="users_report"-->
                <!--                                                />-->
                <!--                                                <span>{{-->
                <!--                                                    $t("Users_Report")-->
                <!--                                                }}</span>-->
                <!--                                                <span class="checkmark"></span>-->
                <!--                                            </label>-->
                <!--                                        </v-col>-->

                <!--                                        &lt;!&ndash;stock_report&ndash;&gt;-->
                <!--                                         <v-col cols="12">-->
                <!--                                            <label-->
                <!--                                                class="checkbox checkbox-outline-primary"-->
                <!--                                            >-->
                <!--                                                <input-->
                <!--                                                    type="checkbox"-->
                <!--                                                    checked-->
                <!--                                                    v-model="permissions"-->
                <!--                                                    value="stock_report"-->
                <!--                                                />-->
                <!--                                                <span>{{-->
                <!--                                                    $t("stock_report")-->
                <!--                                                }}</span>-->
                <!--                                                <span class="checkmark"></span>-->
                <!--                                            </label>-->
                <!--                                        </v-col>-->

                <!--                                        &lt;!&ndash;product_report&ndash;&gt;-->
                <!--                                         <v-col cols="12">-->
                <!--                                            <label-->
                <!--                                                class="checkbox checkbox-outline-primary"-->
                <!--                                            >-->
                <!--                                                <input-->
                <!--                                                    type="checkbox"-->
                <!--                                                    checked-->
                <!--                                                    v-model="permissions"-->
                <!--                                                    value="product_report"-->
                <!--                                                />-->
                <!--                                                <span>{{-->
                <!--                                                    $t("product_report")-->
                <!--                                                }}</span>-->
                <!--                                                <span class="checkmark"></span>-->
                <!--                                            </label>-->
                <!--                                        </v-col>-->

                <!--                                        &lt;!&ndash;product_sales_report&ndash;&gt;-->
                <!--                                         <v-col cols="12">-->
                <!--                                            <label-->
                <!--                                                class="checkbox checkbox-outline-primary"-->
                <!--                                            >-->
                <!--                                                <input-->
                <!--                                                    type="checkbox"-->
                <!--                                                    checked-->
                <!--                                                    v-model="permissions"-->
                <!--                                                    value="product_sales_report"-->
                <!--                                                />-->
                <!--                                                <span>{{-->
                <!--                                                    $t("product_sales_report")-->
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
                <!--                                        &lt;!&ndash;Warehouse  &ndash;&gt;-->
                <!--                                         <v-col cols="12" md="6">-->
                <!--                                            <label-->
                <!--                                                class="checkbox checkbox-outline-primary"-->
                <!--                                            >-->
                <!--                                                <input-->
                <!--                                                    type="checkbox"-->
                <!--                                                    checked-->
                <!--                                                    v-model="permissions"-->
                <!--                                                    value="warehouse"-->
                <!--                                                />-->
                <!--                                                <span>{{-->
                <!--                                                    $t("Warehouses")-->
                <!--                                                }}</span>-->
                <!--                                                <span class="checkmark"></span>-->
                <!--                                            </label>-->
                <!--                                        </v-col>-->
                <!--                                        &lt;!&ndash;Backup&ndash;&gt;-->
                <!--                                         <v-col cols="12" md="6">-->
                <!--                                            <label-->
                <!--                                                class="checkbox checkbox-outline-primary"-->
                <!--                                            >-->
                <!--                                                <input-->
                <!--                                                    type="checkbox"-->
                <!--                                                    checked-->
                <!--                                                    v-model="permissions"-->
                <!--                                                    value="backup"-->
                <!--                                                />-->
                <!--                                                <span>{{ $t("Backup") }}</span>-->
                <!--                                                <span class="checkmark"></span>-->
                <!--                                            </label>-->
                <!--                                        </v-col>-->
                <!--                                    </v-row>-->
                <!--                                </v-card-text>-->
                <!--                            </v-card-body>-->
                <!--                        </v-collapse>-->
                <!--                    </v-card>-->
                <!--                </v-col>-->
            </v-row>
            <!-- End row -->
        </form-view>
    </Layout>
</template>
