<script setup>
import { inject, onMounted, ref } from "vue";
import Layout from "@/Layouts/Authenticated.vue";
import ExportBtn from "@/Components/buttons/ExportBtn.vue";
import Snackbar from "@/Components/snackbar.vue";
import { api, helpers, labels } from "@/helpers";

const moment = inject("moment");
const props = defineProps({
    errors: Object,
});
const report = ref([]);
const loading = ref(false);
const menu = ref(false);
const search = ref("");
// const clients = ref([]);
const snackbar = ref({
    view: false,
    color: "",
    text: "",
});
const fields = ref([
    { title: "Nombre", key: "name" },
    { title: "Telefono", key: "phone" },
    { title: "Total Ventas", key: "total_sales" },
    { title: "Monto Total", key: "total_amount" },
    { title: "Total de Pago", key: "total_paid" },
    { title: "Deuda", key: "due" },
    // {title: "Deuda Retorno", key: "Total_Sell_Return_Due"},
    { title: "Actions", key: "actions" },
]);
const jsonFields = ref({
    Nombre: "name",
    Telefono: "phone",
    "Total Ventas": "total_sales",
    "Monto Total": "total_amount",
    "Total de Pago": "total_paid",
    Deuda: "due",
    // {title: "Deuda Retorno", key: "Total_Sell_Return_Due"},
});

function sumCount(rowObj) {
    let sum = 0;
    for (let i = 0; i < rowObj.length; i++) {
        sum += rowObj[i].total_amount;
    }
    return sum;
}

function sumCount2(rowObj) {
    let sum = 0;
    for (let i = 0; i < rowObj.length; i++) {
        sum += rowObj[i].total_paid;
    }
    return sum;
}

function sumCount3(rowObj) {
    let sum = 0;
    for (let i = 0; i < rowObj.length; i++) {
        sum += rowObj[i].due;
    }
    return sum;
}

function sumCount4(rowObj) {
    let sum = 0;
    for (let i = 0; i < rowObj.length; i++) {
        sum += rowObj[i].return_Due;
    }
    return sum;
}

//--------------------------- Get Customer Report -------------\\

function Get_Client_Report(page) {
    api.get({
        url: "/report/client/list",
        loadingItem: loading,
        snackbar,
        onSuccess: (data) => {
            report.value = data.report;
        },
    });
}
onMounted(() => {
    Get_Client_Report();
});
</script>
<template>
    <layout>
        <snackbar
            v-model="snackbar.view"
            :text="snackbar.text"
            :color="snackbar.color"
        ></snackbar>
        <v-row align="center" class="mb-3">
            <v-col cols="12" sm="6">
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
                        icon="fas fa-eye"
                        size="x-small"
                        variant="outlined"
                        @click="helpers.linkVisit('/report/client/' + item.id)"
                    >
                    </v-btn>
                </template>
            </v-data-table>
        </v-card>
    </layout>
</template>
