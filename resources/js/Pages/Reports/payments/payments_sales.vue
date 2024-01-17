<script setup>
import { inject, onMounted, ref } from "vue";
import Layout from "@/Layouts/Authenticated.vue";
import ExportBtn from "@/Components/buttons/ExportBtn.vue";
import Snackbar from "@/Components/snackbar.vue";
import { api, globals, helpers, labels } from "@/helpers";

const moment = inject("moment");
const props = defineProps({
    errors: Object,
});
const currency = globals.currency();

const payments = ref([]);
const sales = ref([]);
const clients = ref([]);
const loading = ref(false);
const menu = ref(false);
const search = ref("");
const clientFilter = ref([]);
const snackbar = ref({
    view: false,
    color: "",
    text: "",
});
const form = ref({
    startDate: "",
    endDate: "",
    client: "",
    Ref: "",
    sale: "",
    Reg: "",
});

const fields = ref([
    { title: "Fecha", key: "date" },
    { title: "Codigo", key: "Ref" },
    { title: "Codigo Venta", key: "Ref_Sale" },
    { title: "Cliente", key: "client_name" },
    { title: "Forma de Pago", key: "Reglement" },
    { title: "Monto", key: "montant" },
    { title: "Notas", key: "notes" },
]);
const jsonFields = ref({
    Fecha: "date",
    Codigo: "Ref",
    "Codigo Venta": "Ref_Sale",
    Cliente: "client_name",
    "Forma de Pago": "Reglement",
    Monto: "montant",
    Notas: "notes",
});

function sumCount(rowObj) {
    let sum = 0;
    for (let val of rowObj) {
        sum += parseFloat(val.montant);
    }
    return parseFloat(sum).toFixed(2);
}

function querySelections(v) {
    clientFilter.value = props.clients.filter((e) => {
        return (
            (e.name || "").toLowerCase().indexOf((v || "").toLowerCase()) > -1
        );
    });
}

//-------------------------------- Get All Payments Sales ---------------------\\
function Payments_Sales() {
    api.get({
        url: "/payment_sale/list",
        params: { filter: form.value },
        loadingItem: loading,
        snackbar,
        onSuccess: (data) => {
            payments.value = data.payments;
            // sales.value = data.sales;
            clients.value = helpers.toArraySelect(data.clients);
            menu.value = false;
        },
    });
}
onMounted(() => {
    form.value.startDate = moment().format("YYYY-MM-DD");
    form.value.endDate = moment().format("YYYY-MM-DD");
    Payments_Sales();
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
                        <v-form @submit.prevent="Payments_Sales">
                            <v-card-text>
                                <v-row>
                                    <v-col cols="12" sm="6">
                                        <v-text-field
                                            v-model="form.startDate"
                                            variant="outlined"
                                            clearable
                                            hide-details="auto"
                                            type="date"
                                            :label="labels.start_date"
                                        ></v-text-field>
                                    </v-col>
                                    <v-col cols="12" sm="6">
                                        <v-text-field
                                            v-model="form.endDate"
                                            variant="outlined"
                                            clearable
                                            hide-details="auto"
                                            type="date"
                                            :label="labels.end_date"
                                        ></v-text-field>
                                    </v-col>
                                    <v-col cols="12" sm="6">
                                        <v-text-field
                                            v-model="form.Ref"
                                            variant="outlined"
                                            clearable
                                            hide-details="auto"
                                            type="text"
                                            :label="labels.payment.Ref"
                                        ></v-text-field>
                                    </v-col>
                                    <v-col cols="12" sm="6">
                                        <v-text-field
                                            v-model="form.sale"
                                            variant="outlined"
                                            clearable
                                            hide-details="auto"
                                            type="text"
                                            :label="labels.sale.Ref"
                                        ></v-text-field>
                                    </v-col>
                                    <v-col cols="12" sm="6">
                                        <v-select
                                            v-model="form.Reg"
                                            :items="helpers.reglamentPayment()"
                                            :label="labels.payment.Reglement"
                                            item-title="title"
                                            item-value="value"
                                            variant="outlined"
                                            hide-details="auto"
                                            clearable
                                        ></v-select>
                                    </v-col>
                                    <v-col cols="12" sm="6">
                                        <v-autocomplete
                                            v-model="form.client"
                                            variant="outlined"
                                            clearable
                                            hide-details="auto"
                                            :items="clients"
                                            :label="labels.sale.client_id"
                                        ></v-autocomplete>
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
                                    @click="Payments_Sales"
                                >
                                    {{ labels.search }}
                                </v-btn>
                            </v-card-actions>
                        </v-form>
                    </v-card>
                </v-menu>
                <ExportBtn
                    :data="payments"
                    :fields="jsonFields"
                    name-file="Ventas"
                ></ExportBtn>
            </v-col>
        </v-row>
        <v-card>
            <v-data-table
                :headers="fields"
                :items="payments"
                :search="search"
                hover
                :no-data-text="labels.no_data_table"
                :loading="loading"
                class="text-center"
            >
                <template v-slot:item.Reglement="{ item }">
                    {{ helpers.getReglamentPayment(item.Reglement)[0]?.title }}
                </template>
                <template v-slot:tfoot>
                    <tr>
                        <td colspan="4"></td>
                        <td class="font-weight-bold">TOTAL</td>
                        <td class="text-center">{{ sumCount(payments) }}</td>
                    </tr>
                </template>
            </v-data-table>
        </v-card>
    </layout>
</template>
