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
const form = ref({
    start_date: moment().subtract(1, "months").format("YYYY-MM-DD"),
    end_date: moment().format("YYYY-MM-DD"),
});
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
        params: { filter: form.value },
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
                <v-menu
                    v-model="menu"
                    :close-on-content-click="false"
                    location="bottom"
                >
                    <template v-slot:activator="{ props }">
                        <v-btn
                            color="primary"
                            variant="outlined"
                            size="small"
                            elevation="1"
                            class="mr-2 my-1"
                            v-bind="props"
                            append-icon="fas fa-search"
                        >
                            {{ labels.filters }}
                        </v-btn>
                    </template>

                    <v-card max-width="500">
                        <v-form @submit.prevent="Get_Client_Report">
                            <v-card-text>
                                <v-row>
                                    <v-col cols="12" sm="6">
                                        <v-text-field
                                            v-model="form.start_date"
                                            variant="outlined"
                                            clearable
                                            hide-details="auto"
                                            type="date"
                                            :label="labels.start_date"
                                        ></v-text-field>
                                    </v-col>
                                    <v-col cols="12" sm="6">
                                        <v-text-field
                                            v-model="form.end_date"
                                            variant="outlined"
                                            clearable
                                            hide-details="auto"
                                            type="date"
                                            :label="labels.end_date"
                                        ></v-text-field>
                                    </v-col>
                                </v-row>
                            </v-card-text>
                            <v-divider></v-divider>
                            <v-card-actions>
                                <v-spacer></v-spacer>
                                <v-btn
                                    variant="text"
                                    size="small"
                                    color="error"
                                    @click="menu = false"
                                >
                                    {{ labels.cancel }}
                                </v-btn>
                                <v-btn
                                    type="submit"
                                    variant="tonal"
                                    size="small"
                                    color="primary"
                                    @click="Get_Sales"
                                >
                                    {{ labels.search }}
                                </v-btn>
                            </v-card-actions>
                        </v-form>
                    </v-card>
                </v-menu>
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
