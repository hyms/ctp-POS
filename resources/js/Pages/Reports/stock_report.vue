<script setup>
import { onMounted, ref } from "vue";
import Layout from "@/Layouts/Authenticated.vue";
import ExportBtn from "@/Components/buttons/ExportBtn.vue";
import { api, helpers, labels } from "@/helpers";
import Snackbar from "@/Components/snackbar.vue";

const props = defineProps({
    errors: Object,
});
const snackbar = ref({
    view: false,
    color: "",
    text: "",
});
const report = ref([]);
const warehouses = ref([]);
const loading = ref(false);
const menu = ref(false);
const search = ref("");
const warehouse_id = ref("");

const fields = ref([
    { title: labels.product.code, key: "code" },
    { title: labels.product.name, key: "name" },
    { title: labels.product.category_id, key: "category" },
    { title: labels.product.price, key: "price" },
    { title: labels.product.qty, key: "quantity" },
    { title: labels.actions, key: "actions" },
]);
const jsonFields = ref({
    "Codigo de producto": "code",
    "Nombre de producto": "name",
    Categoria: "category",
    precio: "price",
    cantidad: "quantity",
});

//--------------------------- Get Customer Report -------------\\

function Get_Stock_Report(page = 1) {
    api.get({
        url: "/report/stock/list",
        params: {
            page: page,
            SortField: "",
            SortType: "",
            warehouse_id: warehouse_id.value,
            search: "",
            limit: "",
        },
        loadingItem: loading,
        snackbar,
        onSuccess: (data) => {
            report.value = data.report;
            warehouses.value = data.warehouses;
        },
    });
}
function Selected_Warehouse(value) {
    warehouse_id.value = value;
    if (value === null) {
        warehouse_id.value = "";
    }
    Get_Stock_Report(1);
}
onMounted(() => {
    Get_Stock_Report();
});
</script>
<template>
    <layout>
        <snackbar
            v-model="snackbar.view"
            :text="snackbar.text"
            :color="snackbar.color"
        ></snackbar>
        <v-row>
            <v-col>
                <v-col lg="3" md="6" cols="12">
                    <v-select
                        @update:modelValue="Selected_Warehouse"
                        :model-value="warehouse_id"
                        :items="helpers.toArraySelect(warehouses)"
                        :label="labels.filter_by_warehouse"
                        hide-details="auto"
                        clearable
                    ></v-select>
                </v-col>
            </v-col>
        </v-row>
        <v-row align="center" class="mb-3">
            <v-col cols="12" sm="4">
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
                <ExportBtn
                    :data="report"
                    :fields="jsonFields"
                    name-file="reporte_stock"
                ></ExportBtn>
            </v-col>
        </v-row>
        <v-card>
            <v-data-table
                :headers="fields"
                :items="report"
                :search="search"
                hover
                :no-data-text="labels.no_data_table"
                :loading="loading"
            >
                <template v-slot:item.actions="{ item }">
                    <v-btn
                        class="ma-1"
                        color="primary"
                        icon="fas fa-eye"
                        size="x-small"
                        variant="outlined"
                        @click="
                            helpers.linkVisit('/report/stock_detail/' + item.id)
                        "
                    >
                    </v-btn>
                </template>
            </v-data-table>
        </v-card>
    </layout>
</template>
