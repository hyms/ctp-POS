<script setup>
import { ref } from "vue";
import Layout from "@/Layouts/Authenticated.vue";
import ExportBtn from "@/Components/buttons/ExportBtn.vue";
import { router } from "@inertiajs/vue3";
import helper from "@/helpers";
import labels from "@/labels";

const props = defineProps({
    report: Object,
    warehouses: Object,
});
const loading = ref(false);
const menu = ref(false);
const search = ref("");
const formFilter = ref({
    warehouse_id: "",
});

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
    router.get(
        "report/stock",
        {
            page: page,
            SortField: "",
            SortType: "",
            warehouse_id: formFilter.value.warehouse_id,
            search: "",
            limit: "",
        },
        {
            only: ["report"],
            onStart: () => {
                loading.value = true;
            },
            onFinish: () => {
                loading.value = false;
            },
        }
    );
}
</script>
<template>
    <layout>
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
                    name-file="Clientes"
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
                        icon="mdi-eye"
                        size="x-small"
                        variant="outlined"
                        @click="
                            helper.linkVisit('/report/stock_detail/' + item.id)
                        "
                    >
                    </v-btn>
                </template>
            </v-data-table>
        </v-card>
    </layout>
</template>
