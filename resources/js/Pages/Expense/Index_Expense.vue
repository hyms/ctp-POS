<script setup>
import { onMounted, ref } from "vue";
import Layout from "@/Layouts/Authenticated.vue";
import ExportBtn from "@/Components/buttons/ExportBtn.vue";
import { router } from "@inertiajs/vue3";
import DeleteDialog from "@/Components/dialogs/DeleteDialog.vue";
import Snackbar from "@/Components/snackbar.vue";
import { api, globals, helpers, labels } from "@/helpers";

const props = defineProps({
    filter_form: Object,
    errors: Object,
});
const expenses = ref([]);
const Expenses_category = ref([]);
const warehouses = ref([]);
const enableDays = globals.oldDay();
//declare variable
const form = ref(null);
const search = ref("");
const loading = ref(false);
const menu = ref(false);
const snackbar = ref({
    view: false,
    color: "",
    text: "",
});
const dialogDelete = ref(false);

const fields = ref([
    { title: "Fecha", key: "date" },
    { title: "Codigo", key: "Ref" },
    { title: "Detalle", key: "details" },
    { title: "Monto", key: "amount" },
    { title: "Categoria", key: "category_name" },
    { title: "Sucursal", key: "warehouse_name" },
    { title: "Acciones", key: "actions" },
]);
const jsonFields = ref({
    Fecha: "date",
    Codigo: "code",
    Detalle: "details",
    Monto: "amount",
    Categoria: "category_name",
    Sucursal: "warehouse_name",
});
const expense = ref({
    id: "",
});
const form_filter = ref({
    start_date: "",
    end_date: "",
    ref: "",
    warehouse: "",
    category: "",
});

//------------------------------- Remove Expense -------------------------\\

function Remove_Expense() {
    api.delete({
        url: "/expenses/" + expense.value.id,
        loadingItem: loading,
        snackbar,
        onSuccess: (data) => {
            snackbar.value.text = labels.delete_message;
            loadData();
            dialogDelete.value = false;
        },
    });
}

function onCloseDelete() {
    dialogDelete.value = false;
}

function Delete_Expense(id) {
    expense.value.id = id;
    dialogDelete.value = true;
}

function loadData() {
    api.get({
        url: "/expenses/list",
        loadingItem: loading,
        snackbar,
        onSuccess: (data) => {
            expenses.value = data.expenses;
            Expenses_category.value = data.Expenses_category;
            warehouses.value = data.warehouses;
        },
    });
}

function searchFilter() {
    api.get({
        url: "/expenses/list",
        params: {
            filter: form_filter.value,
        },
        loadingItem: loading,
        snackbar,
        onSuccess: (data) => {
            menu.value = false;
            expenses.value = data.expenses;
            Expenses_category.value = data.Expenses_category;
            warehouses.value = data.warehouses;
        },
    });
}

onMounted(() => {
    loadData();
});
</script>
<template>
    <Layout>
        <snackbar
            v-model="snackbar.view"
            :text="snackbar.text"
            :color="snackbar.color"
        ></snackbar>
        <!-- Modal Remove Expense -->
        <delete-dialog
            v-model="dialogDelete"
            :on-save="Remove_Expense"
            :on-close="onCloseDelete"
        ></delete-dialog>
        <v-row align="center">
            <v-col>
                <v-text-field
                    v-model="search"
                    prepend-icon="fas fa-search"
                    hide-details
                    :label="labels.search"
                    single-line
                    variant="underlined"
                ></v-text-field>
            </v-col>
            <v-spacer></v-spacer>
            <v-col cols="auto" class="text-right">
                <v-menu
                    v-model="menu"
                    :close-on-content-click="false"
                    location="bottom"
                >
                    <template v-slot:activator="{ props }">
                        <v-btn
                            color="primary"
                            variant="outlined"
                            elevation="1"
                            class="mr-2 my-1"
                            v-bind="props"
                            append-icon="fas fa-search"
                        >
                            {{ labels.filters }}
                        </v-btn>
                    </template>

                    <v-card max-width="500">
                        <v-form>
                            <v-card-text>
                                <v-row>
                                    <v-col cols="12" sm="6">
                                        <v-text-field
                                            v-model="form_filter.start_date"
                                            clearable
                                            hide-details="auto"
                                            type="date"
                                            :label="labels.start_date"
                                        ></v-text-field>
                                    </v-col>
                                    <v-col cols="12" sm="6">
                                        <v-text-field
                                            v-model="form_filter.end_date"
                                            clearable
                                            hide-details="auto"
                                            type="date"
                                            :label="labels.end_date"
                                        ></v-text-field>
                                    </v-col>
                                    <v-col cols="12" sm="6">
                                        <v-text-field
                                            v-model="form_filter.ref"
                                            clearable
                                            hide-details="auto"
                                            type="text"
                                            :label="labels.expense.ref"
                                        ></v-text-field>
                                    </v-col>
                                    <v-col cols="12" sm="6">
                                        <v-autocomplete
                                            v-model="form_filter.category"
                                            clearable
                                            hide-details="auto"
                                            :items="
                                                helpers.toArraySelect(
                                                    Expenses_category
                                                )
                                            "
                                            :label="labels.expense.category_id"
                                        ></v-autocomplete>
                                    </v-col>
                                    <v-col cols="12" sm="6">
                                        <v-select
                                            v-model="form_filter.warehouse"
                                            clearable
                                            hide-details="auto"
                                            :items="
                                                helpers.toArraySelect(
                                                    warehouses
                                                )
                                            "
                                            :label="labels.expense.warehouse_id"
                                        ></v-select>
                                    </v-col>
                                </v-row>
                            </v-card-text>
                            <v-card-actions>
                                <v-spacer></v-spacer>
                                <v-btn
                                    variant="text"
                                    color="error"
                                    @click="menu = false"
                                >
                                    {{ labels.cancel }}
                                </v-btn>
                                <v-btn
                                    variant="tonal"
                                    color="primary"
                                    @click="searchFilter"
                                >
                                    {{ labels.search }}
                                </v-btn>
                            </v-card-actions>
                        </v-form>
                    </v-card>
                </v-menu>
                <ExportBtn
                    :data="expenses"
                    :fields="jsonFields"
                    name-file="Gastos"
                ></ExportBtn>
                <v-btn
                    v-if="globals.userPermision(['expense_add'])"
                    color="primary"
                    class="ma-1"
                    prepend-icon="fas fa-plus"
                    @click="router.visit('/expenses/create')"
                >
                    {{ labels.add }}
                </v-btn>
            </v-col>
        </v-row>
        <v-row>
            <v-col>
                <v-data-table
                    :headers="fields"
                    :items="expenses"
                    :search="search"
                    hover
                    class="elevation-2"
                    :no-data-text="labels.no_data_table"
                    :loading="loading"
                >
                    <template v-slot:item.actions="{ item }">
                        <v-btn
                            v-if="globals.userPermision(['expense_edit'])"
                            class="ma-1"
                            color="primary"
                            icon="fas fa-pen"
                            size="x-small"
                            variant="outlined"
                            :disabled="
                                helpers.maxEnableButtons(
                                    item.updated_at,
                                    enableDays
                                )
                            "
                            @click="router.visit('/expenses/edit/' + item.id)"
                        >
                        </v-btn>
                        <v-btn
                            v-if="globals.userPermision(['expense_delete'])"
                            class="ma-1"
                            color="error"
                            icon="fas fa-trash"
                            size="x-small"
                            variant="outlined"
                            :disabled="
                                helpers.maxEnableButtons(
                                    item.updated_at,
                                    enableDays
                                )
                            "
                            @click="Delete_Expense(item.id)"
                        >
                        </v-btn>
                    </template>
                </v-data-table>
            </v-col>
        </v-row>
    </Layout>
</template>
