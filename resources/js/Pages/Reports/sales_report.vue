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
const customers = ref([]);
const warehouses = ref([]);
const sales = ref([]);
const currency = globals.currency();
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
    warehouse: "",
    Ref: "",
    status: "",
    Payment: "",
});

const fields = ref([
    { title: "Agencia", key: "warehouse_name" },
    { title: "Fecha", key: "date" },
    { title: "Cliente", key: "client_name" },
    { title: "Codigo", key: "Ref" },
    { title: "Producto", key: "product" },
    { title: "Cantidad", key: "qty" },
    { title: "Precio", key: "price" },
    { title: "Estado", key: "statut" },
    { title: "Total", key: "GrandTotal" },
    { title: "Pagado", key: "paid_amount" },
    { title: "Deuda", key: "due" },
    { title: "Estado de pago", key: "payment_status" },
]);
const jsonFields = ref({
    Agencia: "warehouse_name",
    Fecha: "date",
    Cliente: "client_name",
    "Codigo Cliente": "client_code",
    Codigo: "Ref",
    Producto: "product",
    Cantidad: "qty",
    Precio: "price",
    Estado: "statut",
    Total: "GrandTotal",
    Pagado: "paid_amount",
    Deuda: "due",
    "Estado de pago": "payment_status",
});

function sumCount(rowObj) {
    let sum = 0;
    for (let val of rowObj) {
        sum += parseFloat(val.GrandTotal);
    }
    return parseFloat(sum).toFixed(2);
}

function sumCount2(rowObj) {
    let sum = 0;
    for (let val of rowObj) {
        sum += parseFloat(val.paid_amount);
    }
    return parseFloat(sum).toFixed(2);
}

function sumCount3(rowObj) {
    let sum = 0;
    for (let val of rowObj) {
        sum += parseFloat(val.due);
    }
    return parseFloat(sum).toFixed(2);
}

function querySelections(v) {
    clientFilter.value = props.customers.filter((e) => {
        return (
            (e.name || "").toLowerCase().indexOf((v || "").toLowerCase()) > -1
        );
    });
}

//----------------------------------------- Get all Sales ------------------------------\\
function Get_Sales(page) {
    api.get({
        url: "/report/sales/list",
        params: { filter: form.value },
        loadingItem: loading,
        snackbar,
        onSuccess: (data) => {
            customers.value = helpers.toArraySelect(data.customers);
            warehouses.value = helpers.toArraySelect(data.warehouses);
            sales.value = data.sales;
            menu.value = false;
        },
    });
}
onMounted(() => {
    Get_Sales();
    form.value.startDate = moment().format("YYYY-MM-DD");
    form.value.endDate = moment().format("YYYY-MM-DD");
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
                        <v-form @submit.prevent="Get_Sales">
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
                                        <v-autocomplete
                                            v-model="form.client"
                                            variant="outlined"
                                            clearable
                                            hide-details="auto"
                                            :items="customers"
                                            :label="labels.sale.client_id"
                                        ></v-autocomplete>
                                    </v-col>
                                    <v-col cols="12" sm="6">
                                        <v-select
                                            v-model="form.warehouse"
                                            :items="warehouses"
                                            :label="labels.sale.warehouse_id"
                                            variant="outlined"
                                            hide-details="auto"
                                            clearable
                                        ></v-select>
                                    </v-col>
                                    <v-col cols="12" sm="6">
                                        <v-select
                                            v-model="form.status"
                                            :items="helpers.statutSale()"
                                            :label="labels.sale.statut"
                                            variant="outlined"
                                            hide-details="auto"
                                            clearable
                                        ></v-select>
                                    </v-col>
                                    <v-col cols="12" sm="6">
                                        <v-select
                                            v-model="form.Payment"
                                            :items="helpers.statusPayment()"
                                            :label="labels.sale.payment_status"
                                            variant="outlined"
                                            hide-details="auto"
                                            clearable
                                        ></v-select>
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
                    :data="sales"
                    :fields="jsonFields"
                    name-file="Ventas"
                ></ExportBtn>
            </v-col>
            <v-col cols="12">
                <v-data-table
                    :headers="fields"
                    :items="sales"
                    :search="search"
                    hover
                    :no-data-text="labels.no_data_table"
                    :loading="loading"
                    class="text-center elevation-2"
                >
                    <template v-slot:item.Reglement="{ item }">
                        {{
                            helpers.getReglamentPayment(item.Reglement)[0].title
                        }}
                    </template>
                    <template v-slot:item.statut="{ item }">
                        <v-chip
                            :color="helpers.statutSaleColor(item.statut)"
                            variant="tonal"
                            size="x-small"
                            >{{ helpers.statutSale(item.statut) }}
                        </v-chip>
                    </template>
                    <template v-slot:item.payment_status="{ item }">
                        <v-chip
                            :color="
                                helpers.statusPaymentColor(item.payment_status)
                            "
                            variant="tonal"
                            size="x-small"
                            >{{ helpers.statusPayment(item.payment_status) }}
                        </v-chip>
                    </template>
                </v-data-table>
            </v-col>
        </v-row>
    </layout>
</template>
