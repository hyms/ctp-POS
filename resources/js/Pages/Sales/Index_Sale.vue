<script setup>
import { inject, onMounted, ref } from "vue";
import Layout from "@/Layouts/Authenticated.vue";
import ExportBtn from "@/Components/buttons/ExportBtn.vue";
import { router } from "@inertiajs/vue3";
import DeleteDialog from "@/Components/dialogs/DeleteDialog.vue";
import Snackbar from "@/Components/snackbar.vue";
import { api, globals, helpers, labels, rules } from "@/helpers";
import InvoiceDialog from "@/Components/dialogs/InvoiceDialog.vue";

const moment = inject("moment");
const props = defineProps({
    // filter_form: Object,
    errors: Object,
});
const sales = ref([]);
const sales_types = ref([]);
const customers = ref([]);
const warehouses = ref([]);
const enableDays = globals.oldDay();
const search = ref("");
const menu = ref(false);
const loading = ref(false);
const loadingInvoice = ref(false);
const snackbar = ref({
    view: false,
    color: "",
    text: "",
});
const dialogDelete = ref(false);
const dialogInvoice = ref(false);
const dialogAddPayment = ref(false);
const dialogShowPayment = ref(false);
const dialogDeletePayment = ref(false);
const fields = ref([
    { title: "Fecha", key: "date" },
    { title: "Codigo", key: "Ref" },
    { title: "Cliente", key: "client_name" },
    { title: "Agencia", key: "warehouse_name" },
    { title: "Estado", key: "statut" },
    { title: "Total", key: "GrandTotal" },
    { title: "Pagado", key: "paid_amount" },
    { title: "Deuda", key: "due" },
    { title: "Estado Pago", key: "payment_status" },
    { title: "Acciones", key: "actions" },
]);
const jsonFields = ref({
    Fecha: "date",
    Codigo: "Ref",
    "Creado Por": "created_by",
    Cliente: "client_name",
    Agencia: "warehouse_name",
    Estado: "statut",
    Total: "GrandTotal",
    Pagado: "paid_amount",
    Deuda: "due",
    "Estado Pago": "payment_status",
});
const formFilterLabel = ref({
    start_date: "Fecha desde",
    end_date: "Fecha hasta",
    sale_ref: "Codigo Orden",
    client: "Cliente",
    sale_type: "Tipo de orden",
    statut: "Estado",
    status_payment: "Estado de pago",
});
const pos_settings = ref({});
const EditPaymentMode = ref(false);
const shipment = ref({});
const sale_due = ref(0);
const due = ref(0);
const invoice_pos = ref({
    sale: {
        Ref: "",
        client_name: "",
        discount: "",
        taxe: "",
        tax_rate: "",
        shipping: "",
        GrandTotal: "",
        paid_amount: "",
    },
    details: [],
    setting: {
        logo: "",
        CompanyName: "",
        CompanyAdress: "",
        email: "",
        CompanyPhone: "",
    },
});

const payments = ref([]);
const payments_pos = ref([]);
const payment = ref({});

const Sale_id = ref("");
const sale = ref({});

const form = ref(null);
const form_filter = ref({
    start_date: moment().subtract(1, 'days').format('YYYY-MM-DD'),
    end_date: moment().format('YYYY-MM-DD'),
    sale_ref: "",
    client: "",
    sale_type: "",
    statut: "",
    status_payment: "",
});

//------------------------------ Print -------------------------\\
function print_it() {
    let divContents = document.getElementById("invoice-POS").innerHTML;
    let a = window.open("", "", "height=500, width=500");
    a.document.write('<link rel="stylesheet" href="' + pos_css + '"><html>');
    a.document.write("<body >");
    a.document.write(divContents);
    a.document.write("</body></html>");
    a.document.close();

    setTimeout(() => {
        a.print();
    }, 1000);
}

//---------- keyup paid Amount
function Verified_paidAmount() {
    snackbar.value.view = false;
    if (isNaN(payment.value.montant)) {
        payment.value.montant = 0;
        // } else if ((payment.value.montant*1) > (payment.value.received_amount*1)) {
        //   snackbar.value = true;
        //   snackbar.value.color = "warning";
        //   snackbar.value.text =
        //       "El monto de pago es mas alto que el a pagar";
        //   payment.value.montant = 0;
    } else if (payment.value.montant * 1 > due.value * 1) {
        snackbar.value.view = true;
        snackbar.value.color = "warning";
        snackbar.value.text =
            "El monto de pago es mal alto que el Total de venta";
        payment.value.montant = 0;
    }
}

//---------- keyup Received Amount

function Verified_Received_Amount() {
    if (isNaN(payment.value.received_amount)) {
        payment.value.received_amount = 0;
    }
}

//------ Validate Form Submit_Payment
async function Submit_Payment() {
    snackbar.value.view = false;
    const validate = await form.value.validate();
    if (payment.value.montant > payment.value.received_amount) {
        payment.value.montant = payment.value.received_amount;
    }
    if (!validate.valid) {
        snackbar.value.view = true;
        snackbar.value.color = "error";
        snackbar.value.text = "Llena los campos correctamente";
    } else if (EditPaymentMode.value) {
        Update_Payment();
    } else {
        Create_Payment();
    }
}

//-------------------------------- Invoice POS ------------------------------\\
function Invoice_POS(id) {
    dialogInvoice.value = false;
    api.get({
        url: "/sales_print_invoice/" + id,
        loadingItem: loadingInvoice,
        snackbar,
        onSuccess: (data) => {
            invoice_pos.value = data;
            // payments_pos.value = data.payments;
            // pos_settings.value = data.pos_settings;
            dialogInvoice.value = true;
            // if (response.data.pos_settings.is_printable) {
            //   setTimeout(() => print_it(), 1000);
            // }
        },
    });
}

//-----------------------------  Invoice PDF ------------------------------\\
function Invoice_PDF(sale, id) {
    loading.value = true;
    axios
        .get("/sale_pdf/" + id, {
            responseType: "blob", // important
            headers: {
                "Content-Type": "application/json",
            },
        })
        .then((response) => {
            const url = window.URL.createObjectURL(new Blob([response.data]));
            const link = document.createElement("a");
            link.href = url;
            link.setAttribute("download", "Sale-" + sale.Ref + ".pdf");
            document.body.appendChild(link);
            link.click();
        })
        .catch(() => {})
        .finally(() => {
            loading.value = false;
        });
}

//------------------------ Payments Sale PDF ------------------------------\\
function Payment_Sale_PDF(payment, id) {
    loading.value = true;
    axios
        .get("/payment_sale_pdf/" + id, {
            responseType: "blob", // important
            headers: {
                "Content-Type": "application/json",
            },
        })
        .then((response) => {
            const url = window.URL.createObjectURL(new Blob([response.data]));
            const link = document.createElement("a");
            link.href = url;
            link.setAttribute("download", "Payment-" + payment.Ref + ".pdf");
            document.body.appendChild(link);
            link.click();
        })
        .catch(() => {})
        .finally(() => {
            loading.value = false;
        });
}

function Number_Order_Payment() {
    axios
        .get("/payment_sale_get_number")
        .then(({ data }) => (payment.value.Ref = data));
}

//----------------------------------- New Payment Sale ------------------------------\\
function New_Payment(saleItem) {
    dialogAddPayment.value = false;
    snackbar.value.view = false;
    if (saleItem.payment_status == "paid") {
        snackbar.value.view = true;
        snackbar.value.color = "error";
        snackbar.value.text = "Pago ya realizado";
    } else {
        reset_form_payment();
        EditPaymentMode.value = false;
        sale.value = saleItem;
        payment.value.date = moment().format("YYYY-MM-DD");
        Number_Order_Payment();
        payment.value.montant = saleItem.due;
        payment.value.Reglement = "cash";
        payment.value.received_amount = saleItem.due;
        due.value = parseFloat(saleItem.due);
        dialogAddPayment.value = true;
    }
}

//------------------------------------Edit Payment ------------------------------\\
function Edit_Payment(payment_item) {
    reset_form_payment();
    EditPaymentMode.value = true;
    payment.value.id = payment_item.id;
    payment.value.Ref = payment_item.Ref;
    payment.value.Reglement = payment_item.Reglement;
    payment.value.date = payment_item.date;
    payment.value.change = payment_item.change;
    payment.value.montant = payment_item.montant;
    payment.value.received_amount = parseFloat(
        payment_item.montant + payment_item.change
    ).toFixed(2);
    payment.value.notes = payment_item.notes;
    // console.log(payment_item);
    due.value = parseFloat(sale_due.value) - payment_item.montant;
    dialogAddPayment.value = true;
}

//-------------------------------Show All Payment with Sale ---------------------\\
function Show_Payments(id, itemSale) {
    // Start the progress bar.
    reset_form_payment();
    Sale_id.value = id;
    sale.value = itemSale;
    Get_Payments(id);
}

//----------------------------------Create Payment sale ------------------------------\\
function Create_Payment() {
    api.post({
        url: "/payment_sale",
        params: {
            sale_id: sale.value.id,
            date: payment.value.date,
            montant: parseFloat(payment.value.montant).toFixed(2),
            received_amount: parseFloat(payment.value.received_amount).toFixed(
                2
            ),
            change: parseFloat(
                payment.value.received_amount - payment.value.montant
            ).toFixed(2),
            Reglement: payment.value.Reglement,
            notes: payment.value.notes,
        },
        loadingItem: loading,
        snackbar,
        onSuccess: () => {
            snackbar.value.text = "Transaccion realizada con exito";
            loadData();
            dialogAddPayment.value = false;
        },
    });
}

//---------------------------------------- Update Payment ------------------------------\\
function Update_Payment() {
    api.put({
        url: "/payment_sale/" + payment.value.id,
        params: {
            sale_id: sale.value.id,
            date: payment.value.date,
            montant: parseFloat(payment.value.montant).toFixed(2),
            received_amount: parseFloat(payment.value.received_amount).toFixed(
                2
            ),
            change: parseFloat(
                payment.value.received_amount - payment.value.montant
            ).toFixed(2),
            Reglement: payment.value.Reglement,
            notes: payment.value.notes,
        },
        loadingItem: loading,
        snackbar,
        onSuccess: () => {
            snackbar.value.text = "Transaccion realizada con exito";
            loadData();
            dialogAddPayment.value = false;
            dialogShowPayment.value = false;
        },
    });
}

//----------------------------------------- Remove Payment ------------------------------\\
function onCloseDelete() {
    reset_form_payment();
    dialogDelete.value = false;
}

function Delete_Item(item) {
    reset_form_payment();
    sale.value = item;
    dialogDelete.value = true;
}

function Delete_Payment(item) {
    sale.value = item;
    dialogDeletePayment.value = true;
}

function Remove_Payment() {
    // console.log(sale.value.id);

    api.delete({
        url: "/payment_sale/" + sale.value.id,
        loadingItem: loading,
        onSuccess: () => {
            snackbar.value.text = "Borrado exitoso";
            loadData();
            dialogDeletePayment.value = false;
            dialogShowPayment.value = false;
        },
    });
}

//----------------------------------------- Get Payments  -------------------------------\\
function Get_Payments(id) {
    dialogShowPayment.value = false;
    axios
        .get("/get_payments_by_sale/" + id)
        .then(({ data }) => {
            // console.log(data);
            payments.value = data.payments;
            sale_due.value = data.due;
            dialogShowPayment.value = true;
        })
        .catch(() => {
            // Complete the animation of the  progress bar.
        })
        .finally(() => {});
}

//------------------------------------------ Reset Form Payment ------------------------------\\
function reset_form_payment() {
    due.value = 0;
    payment.value = {
        id: "",
        Sale_id: "",
        date: "",
        Ref: "",
        montant: "",
        received_amount: "",
        Reglement: "",
        notes: "",
    };
}

//------------------------------------------ Remove Sale ------------------------------\\
function Remove_Sale(id, sale_has_return) {
    snackbar.value.view = false;
    if (sale_has_return == "yes") {
        snackbar.value.view = true;
        snackbar.value.color = "error";
        snackbar.value.text = "Existe una devolucion en la transaccion";
    } else {
        api.delete({
            url: "/sales/" + sale.value.id,
            loadingItem: loading,
            snackbar,
            onSuccess: () => {
                snackbar.value.text = "Borrado exitoso";
                loadData();
                dialogDelete.value = false;
            },
        });
    }
}

function loadData() {
    api.get({
        url: "/sales/list",
        params: {
            filter: form_filter.value,
        },
        loadingItem: loading,
        snackbar,
        onSuccess: (data) => {
            sales.value = data.sales;
            sales_types.value = data.sales_types;
            customers.value = data.customers;
            warehouses.value = data.warehouses;
            menu.value = false;
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
        <!-- Modal Remove Sale -->
        <delete-dialog
            v-model="dialogDelete"
            :on-save="Remove_Sale"
        ></delete-dialog>
        <!-- Modal Remove Payment -->
        <delete-dialog
            v-model="dialogDeletePayment"
            :on-save="Remove_Payment"
        ></delete-dialog>
        <!-- Modal Show Payments-->
        <v-dialog v-model="dialogShowPayment" width="800">
            <v-card>
                <v-toolbar>
                    <v-toolbar-title>Pagos realizados</v-toolbar-title>
                    <v-spacer></v-spacer>
                    <v-btn
                        icon="fas fa-times"
                        size="small"
                        density="comfortable"
                        variant="tonal"
                        @click="dialogShowPayment = false"
                    ></v-btn>
                </v-toolbar>
                <v-card-text>
                    <v-row class="mb-3">
                        <v-col>
                            <v-table hover>
                                <thead>
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Codigo</th>
                                        <th>Monto</th>
                                        <th>Pagado en</th>
                                        <th>Detalle</th>
                                        <th>Accion</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-if="payments.length <= 0">
                                        <td colspan="5">No Disponible</td>
                                    </tr>
                                    <tr v-for="payment in payments">
                                        <td>{{ payment.date }}</td>
                                        <td>{{ payment.Ref }}</td>
                                        <td>
                                            Bs
                                            {{
                                                helpers.formatNumber(
                                                    payment.montant,
                                                    2
                                                )
                                            }}
                                        </td>
                                        <td>
                                            {{
                                                helpers.getReglamentPayment(
                                                    payment.Reglement
                                                )[0].title
                                            }}
                                        </td>
                                        <td>
                                            {{ payment.notes }}
                                        </td>
                                        <td>
                                            <!--                    <v-btn-->
                                            <!--                        title="Imprimir"-->
                                            <!--                        size="x-small"-->
                                            <!--                        color="info"-->
                                            <!--                        @click="Payment_Sale_PDF(payment,payment.id)"-->
                                            <!--                        icon="fas fa-print"-->
                                            <!--                        class="ma-1"-->
                                            <!--                    >-->
                                            <!--                    </v-btn>-->
                                            <v-btn
                                                v-if="
                                                    globals.userPermision([
                                                        'payment_sales_edit',
                                                    ])
                                                "
                                                title="Editar"
                                                size="small"
                                                density="comfortable"
                                                color="success"
                                                @click="Edit_Payment(payment)"
                                                :disabled="
                                                    helpers.maxEnableButtons(
                                                        payment.updated_at,
                                                        enableDays
                                                    )
                                                "
                                                icon="fas fa-pen"
                                                class="mx-1 rounded"
                                            >
                                            </v-btn>
                                            <v-btn
                                                v-if="
                                                    globals.userPermision([
                                                        'payment_sales_delete',
                                                    ])
                                                "
                                                title="Borrar"
                                                size="small"
                                                density="comfortable"
                                                color="error"
                                                @click="Delete_Payment(payment)"
                                                :disabled="
                                                    helpers.maxEnableButtons(
                                                        payment.updated_at,
                                                        enableDays
                                                    )
                                                "
                                                icon="fas fa-times"
                                                class="mx-1 rounded"
                                            >
                                            </v-btn>
                                        </td>
                                    </tr>
                                </tbody>
                            </v-table>
                        </v-col>
                    </v-row>
                </v-card-text>
            </v-card>
        </v-dialog>
        <!-- Modal Add Payment-->
        <v-dialog v-model="dialogAddPayment" width="600">
            <v-card>
                <v-toolbar
                    :title="(EditPaymentMode ? 'Editar ' : 'Añadir ') + 'Pago'"
                ></v-toolbar>
                <v-card-text>
                    <v-form ref="form">
                        <v-row>
                            <!-- date -->
                            <v-col md="4" sm="6" cols="12">
                                <v-text-field
                                    :label="labels.payment.date + ' *'"
                                    v-model="payment.date"
                                    :placeholder="labels.payment.date"
                                    :rules="rules.required"
                                    hide-details="auto"
                                >
                                </v-text-field>
                            </v-col>

                            <!-- Reference  -->
                            <v-col md="4" sm="6" cols="12">
                                <v-text-field
                                    :label="labels.payment.Ref + ' *'"
                                    v-model="payment.Ref"
                                    :placeholder="labels.payment.Ref"
                                    hide-details="auto"
                                    readonly=""
                                >
                                </v-text-field>
                            </v-col>

                            <!-- Payment choice -->
                            <v-col md="4" sm="6" cols="12">
                                <v-select
                                    v-model="payment.Reglement"
                                    :items="helpers.reglamentPayment()"
                                    :rules="rules.required"
                                    :label="labels.payment.role"
                                    item-title="title"
                                    item-value="value"
                                    hide-details="auto"
                                ></v-select>
                            </v-col>

                            <!-- Received  Amount  -->
                            <v-col md="4" sm="6" cols="12">
                                <v-text-field
                                    label="Deuda"
                                    v-model="payment.received_amount"
                                    placeholder="Deuda"
                                    hide-details="auto"
                                    readonly
                                >
                                </v-text-field>
                            </v-col>

                            <!-- Paying Amount  -->
                            <v-col md="4" sm="6" cols="12">
                                <v-text-field
                                    :label="labels.payment.montant"
                                    v-model="payment.montant"
                                    :placeholder="labels.payment.montant"
                                    :rules="
                                        rules.required.concat(
                                            rules.numberWithDecimal
                                        )
                                    "
                                    hide-details="auto"
                                    @keyup="Verified_paidAmount"
                                >
                                </v-text-field>
                            </v-col>

                            <!-- change Amount  -->
                            <v-col md="4" sm="6" cols="12">
                                <v-text-field
                                    label="Saldo"
                                    :model-value="
                                        parseFloat(
                                            payment.received_amount -
                                                payment.montant
                                        ).toFixed(2)
                                    "
                                    placeholder="Saldo"
                                    hide-details="auto"
                                    readonly
                                >
                                </v-text-field>
                            </v-col>

                            <!-- Note -->
                            <v-col cols="12">
                                <v-textarea
                                    rows="4"
                                    :label="labels.payment.notes"
                                    v-model="payment.notes"
                                    :placeholder="labels.payment.notes"
                                    hide-details="auto"
                                ></v-textarea>
                            </v-col>
                        </v-row>
                    </v-form>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn
                        variant="outlined"
                        color="error"
                        class="ma-1"
                        @click="dialogAddPayment = false"
                    >
                        Cancelar
                    </v-btn>
                    <v-btn
                        color="primary"
                        variant="flat"
                        class="ma-1"
                        :loading="loading"
                        :disabled="loading"
                        @click="Submit_Payment"
                    >
                        Guardar
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
        <!-- Modal Show Invoice POS-->
        <invoice-dialog
            v-model="dialogInvoice"
            :invoice_pos="invoice_pos"
            :loading="loadingInvoice"
        ></invoice-dialog>

        <v-row align="center" class="mb-3">
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
                            Filtros
                        </v-btn>
                    </template>

                    <v-card max-width="500">
                        <v-form @submit.prevent="loadData()">
                            <v-card-text>
                                <v-row>
                                    <v-col cols="12" sm="6">
                                        <v-text-field
                                            v-model="form_filter.start_date"
                                            variant="outlined"
                                            clearable
                                            hide-details="auto"
                                            type="date"
                                            :label="formFilterLabel.start_date"
                                        ></v-text-field>
                                    </v-col>
                                    <v-col cols="12" sm="6">
                                        <v-text-field
                                            v-model="form_filter.end_date"
                                            variant="outlined"
                                            clearable
                                            hide-details="auto"
                                            type="date"
                                            :label="formFilterLabel.end_date"
                                        ></v-text-field>
                                    </v-col>
                                    <v-col cols="12" sm="6">
                                        <v-text-field
                                            v-model="form_filter.sale_ref"
                                            variant="outlined"
                                            clearable
                                            hide-details="auto"
                                            type="text"
                                            :label="formFilterLabel.sale_ref"
                                        ></v-text-field>
                                    </v-col>
                                    <v-col cols="12" sm="6">
                                        <v-autocomplete
                                            v-model="form_filter.client"
                                            variant="outlined"
                                            clearable
                                            hide-details="auto"
                                            :items="
                                                helpers.toArraySelect(customers)
                                            "
                                            :label="formFilterLabel.client"
                                            :loading="loading"
                                        ></v-autocomplete>
                                    </v-col>
                                    <v-col cols="12" sm="6">
                                        <v-select
                                            v-model="form_filter.sale_type"
                                            variant="outlined"
                                            clearable
                                            hide-details="auto"
                                            :items="
                                                helpers.toArraySelect(
                                                    sales_types
                                                )
                                            "
                                            :label="formFilterLabel.sale_type"
                                            :loading="loading"
                                        ></v-select>
                                    </v-col>
                                    <v-col cols="12" sm="6">
                                        <v-select
                                            v-model="form_filter.statut"
                                            variant="outlined"
                                            clearable
                                            hide-details="auto"
                                            :items="helpers.statutSale()"
                                            :label="formFilterLabel.statut"
                                        ></v-select>
                                    </v-col>
                                    <v-col cols="12" sm="6">
                                        <v-select
                                            v-model="form_filter.status_payment"
                                            item-text="text"
                                            item-value="value"
                                            variant="outlined"
                                            clearable
                                            hide-details="auto"
                                            :items="helpers.statusPayment()"
                                            :label="
                                                formFilterLabel.status_payment
                                            "
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
                                    Cancelar
                                </v-btn>
                                <v-btn
                                    type="submit"
                                    variant="tonal"
                                    size="small"
                                    color="primary"
                                    :loading="loading"
                                >
                                    Buscar
                                </v-btn>
                            </v-card-actions>
                        </v-form>
                    </v-card>
                </v-menu>
                <ExportBtn
                    :data="sales"
                    :fields="jsonFields"
                    name-file="Productos"
                ></ExportBtn>
                <v-btn
                    v-if="globals.userPermision(['Sales_add'])"
                    color="primary"
                    class="ma-1"
                    variant="flat"
                    prepend-icon="fas fa-plus"
                    @click="router.visit('/sales/create')"
                >
                    {{ labels.add }}
                </v-btn>
            </v-col>
        </v-row>
        <v-card>
            <v-data-table
                :headers="fields"
                :items="sales"
                :search="search"
                hover
                :no-data-text="labels.no_data_table"
                :loading="loading"
            >
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
                        :color="helpers.statusPaymentColor(item.payment_status)"
                        variant="tonal"
                        size="x-small"
                        >{{ helpers.statusPayment(item.payment_status) }}
                    </v-chip>
                </template>
                <template v-slot:item.Ref="{ item }">
                    <v-btn
                        variant="tonal"
                        size="x-small"
                        color="default"
                        :text="item.Ref"
                        @click="router.visit('/sales/detail/' + item.id)"
                    ></v-btn>
                </template>
                <template v-slot:item.actions="{ item }">
                    <v-menu>
                        <template v-slot:activator="{ props }">
                            <v-btn
                                v-bind="props"
                                class="ma-1"
                                icon="fas fa-ellipsis-v"
                                color="primary"
                                size="x-small"
                                variant="elevated"
                            >
                            </v-btn>
                        </template>
                        <v-list density="compact">
                            <v-list-item
                                @click="
                                    router.visit('/sales/detail/' + item.id)
                                "
                                prepend-icon="fas fa-eye"
                            >
                                <v-list-item-title>
                                    Detalle de Orden
                                </v-list-item-title>
                            </v-list-item>
                            <v-list-item
                                v-if="globals.userPermision(['Sales_edit'])"
                                @click="router.visit('/sales/edit/' + item.id)"
                                prepend-icon="fas fa-pen"
                                :disabled="
                                    helpers.maxEnableButtons(
                                        item.updated_at,
                                        enableDays
                                    )
                                "
                            >
                                <v-list-item-title>
                                    Editar Orden
                                </v-list-item-title>
                            </v-list-item>
                            <v-list-item
                                v-if="
                                    globals.userPermision([
                                        'payment_sales_view',
                                    ])
                                "
                                @click="Show_Payments(item.id, item)"
                                prepend-icon="fas fa-shopping-basket"
                            >
                                <v-list-item-title>
                                    Ver Pagos
                                </v-list-item-title>
                            </v-list-item>
                            <v-list-item
                                v-if="
                                    globals.userPermision([
                                        'payment_sales_add',
                                    ]) && item.payment_status != 'paid'
                                "
                                @click="New_Payment(item)"
                                prepend-icon="fas fa-dollar-sign"
                            >
                                <v-list-item-title>
                                    Añadir Pago
                                </v-list-item-title>
                            </v-list-item>
                            <!--              <v-list-item-->
                            <!--                  @click="New_Payment(item)"-->
                            <!--                  prepend-icon="mdi-hammer"-->
                            <!--                  v-if="item.statut!='completed'"-->
                            <!--              >-->
                            <!--                <v-list-item-title>-->
                            <!--                 Completar Trabajo-->
                            <!--                </v-list-item-title>-->
                            <!--              </v-list-item>-->
                            <v-list-item
                                @click="Invoice_POS(item.id)"
                                prepend-icon="fas fa-print"
                            >
                                <v-list-item-title>
                                    Impresion Orden
                                </v-list-item-title>
                            </v-list-item>

                            <!--                            <v-dropdown-item-->
                            <!--                                title="PDF"-->
                            <!--                                @click="Invoice_PDF(props.row, props.row.id)"-->
                            <!--                            >-->
                            <!--                                <i-->
                            <!--                                    class="nav-icon i-File-TXT font-weight-bold mr-2"-->
                            <!--                                ></i>-->
                            <!--                                {{ $t("DownloadPdf") }}-->
                            <!--                            </v-dropdown-item>-->
                            <v-list-item
                                v-if="globals.userPermision(['Sales_delete'])"
                                @click="Delete_Item(item)"
                                prepend-icon="fas fa-trash"
                                :disabled="
                                    helpers.maxEnableButtons(
                                        item.updated_at,
                                        enableDays
                                    )
                                "
                            >
                                <v-list-item-title>
                                    Eliminar
                                </v-list-item-title>
                            </v-list-item>
                        </v-list>
                    </v-menu>
                </template>
            </v-data-table>
        </v-card>
    </Layout>
</template>
<style>
.total {
    font-weight: bold;
}
</style>
