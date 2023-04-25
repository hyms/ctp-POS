<script setup>
import { ref } from "vue";
import Layout from "@/Layouts/Authenticated.vue";
import Snackbar from "@/Components/snackbar.vue";
import ExportBtn from "@/Components/ExportBtn.vue";
import DeleteDialog from "@/Components/DeleteDialog.vue";
import { router } from "@inertiajs/vue3";
import rulesForm from "@/rules";
import Filter_form from "@/Pages/Sales/filter_form.vue";

const props = defineProps({
    sales: Object,
    sales_types: Object,
    customers: Object,
    warehouses: Object,
    filter_form: Object,
    errors: Object,
});

const search = ref("");
const loading = ref(false);
const snackbar = ref(false);
const snackbarText = ref("");
const snackbarColor = ref("info");
const dialogDelete = ref(false);
const dialogInvoice = ref(false);
const dialogAddPayment = ref(false);
const dialogShowPayment = ref(false);
const dialogShipment = ref(false);
const fields = ref([
    { title: "Fecha", key: "date" },
    { title: "Codigo", key: "Ref" },
    { title: "Creado Por", key: "created_by" },
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

const pos_settings = ref({});
const paymentProcessing = ref(false);
const Submit_Processing_shipment = ref(false);
const showDropdown = ref(false);
const EditPaiementMode = ref(false);
const shipment = ref({});
const sale_due = ref("");
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
const payment = ref({});
const Sale_id = ref("");
const sale = ref({});
const email = ref({
    to: "",
    subject: "",
    message: "",
    client_name: "",
    Sale_Ref: "",
});
const emailPayment = ref({
    id: "",
    to: "",
    subject: "",
    message: "",
    client_name: "",
    Ref: "",
});
const form = ref(null);
const shipment_ref = ref(null);

//------------------------------ Print -------------------------\\
function print_it() {
    let divContents = document.getElementById("invoice-POS").innerHTML;
    let a = window.open("", "", "height=500, width=500");
    a.document.write('<link rel="stylesheet" href="/css/pos_print.css"><html>');
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
    snackbar.value = false;
    if (isNaN(payment.value.montant)) {
        payment.value.montant = 0;
    } else if (payment.value.montant > payment.value.received_amount) {
        snackbar.value = true;
        snackbarColor.value = "warning";
        snackbarText.value =
            "El monto de pago es mal alto que el monto recibido";
        payment.value.montant = 0;
    } else if (payment.value.montant > due.value) {
        snackbar.value = true;
        snackbarColor.value = "warning";
        snackbarText.value =
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
    snackbar.value = false;
    const validate = await form.value.validate();
    if (!validate.valid) {
        snackbar.value = true;
        snackbarColor.value = "error";
        snackbarText.value = "Llena los campos correctamente";
    } else if (payment.value.montant > payment.value.received_amount) {
        snackbar.value = true;
        snackbarColor.value = "warning";
        snackbarText.value =
            "El monto de pago es mal alto que el monto recibido";
        payment.value.montant = 0;
    } else if (payment.value.montant > due.value) {
        snackbar.value = true;
        snackbarColor.value = "warning";
        snackbarText.value =
            "El monto de pago es mal alto que el Total de venta";
        payment.value.montant = 0;
    } else if (EditPaiementMode.value) {
        Edit_Product();
    } else {
        Create_Product();
    }
}

//-------------------------------- Invoice POS ------------------------------\\
function Invoice_POS(id) {
    loading.value = true;
    dialogInvoice.value = false;
    snackbar.value = true;
    axios
        .get("/sales_print_invoice/" + id)
        .then((response) => {
            invoice_pos.value = response.data;
            payment.values = response.data.payments;
            pos_settings.value = response.data.pos_settings;
            setTimeout(() => {
                dialogInvoice.value = true;
            }, 500);
            if (response.data.pos_settings.is_printable) {
                setTimeout(() => print_it(), 1000);
            }
        })
        .catch(() => {})
        .finally(() => {
            loading.value = false;
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

//--------------------------------------------- Send Payment to Email -------------------------------\\
//     EmailPayment(payment, sale) {
//       this.emailPayment.id = payment.id;
//       this.emailPayment.to = sale.client_email;
//       this.emailPayment.Ref = payment.Ref;
//       this.emailPayment.client_name = sale.client_name;
//       this.Send_Email_Payment();
//     },
//     Send_Email_Payment() {
//       // Start the progress bar.
//       NProgress.start();
//       NProgress.set(0.1);
//       axios
//         .post("payment_sale_send_email", {
//           id: this.emailPayment.id,
//           to: this.emailPayment.to,
//           client_name: this.emailPayment.client_name,
//           Ref: this.emailPayment.Ref
//         })
//         .then(response => {
//           // Complete the animation of the  progress bar.
//           setTimeout(() => NProgress.done(), 500);
//           this.makeToast(
//             "success",
//             this.$t("Send.TitleEmail"),
//             this.$t("Success")
//           );
//         })
//         .catch(error => {
//           // Complete the animation of the  progress bar.
//           setTimeout(() => NProgress.done(), 500);
//           this.makeToast("danger", this.$t("SMTPIncorrect"), this.$t("Failed"));
//         });
//     },
//     //--------------------------------- Send Sale in Email ------------------------------\\
//     Sale_Email(sale) {
//       this.email.to = sale.client_email;
//       this.email.Sale_Ref = sale.Ref;
//       this.email.client_name = sale.client_name;
//       this.Send_Email(sale.id);
//     },
//     Send_Email(id) {
//       // Start the progress bar.
//       NProgress.start();
//       NProgress.set(0.1);
//       axios
//         .post("sales_send_email", {
//           id: id,
//           to: this.email.to,
//           client_name: this.email.client_name,
//           Ref: this.email.Sale_Ref
//         })
//         .then(response => {
//           // Complete the animation of the  progress bar.
//           setTimeout(() => NProgress.done(), 500);
//           this.makeToast(
//             "success",
//             this.$t("Send.TitleEmail"),
//             this.$t("Success")
//           );
//         })
//         .catch(error => {
//           // Complete the animation of the  progress bar.
//           setTimeout(() => NProgress.done(), 500);
//           this.makeToast("danger", this.$t("SMTPIncorrect"), this.$t("Failed"));
//         });
//     },
function Number_Order_Payment() {
    axios
        .get("/payment_sale_get_number")
        .then(({ data }) => (payment.value.Ref = data));
}

//----------------------------------- New Payment Sale ------------------------------\\
function New_Payment(saleItem) {
    dialogAddPayment.value = false;
    snackbar.value = false;
    if (sale.payment_status == "paid") {
        snackbar.value = true;
        snackbarColor.value = "error";
        snackbarText.value = "Pago ya realizado";
    } else {
        reset_form_payment();
        EditPaiementMode.value = false;
        sale.value = saleItem;
        payment.value.date = new Date().toISOString().slice(0, 10);
        Number_Order_Payment();
        payment.value.montant = sale.due;
        payment.value.Reglement = "Cash";
        payment.value.received_amount = sale.due;
        due.value = parseFloat(sale.due);
        setTimeout(() => {
            dialogAddPayment.value = true;
        }, 500);
    }
}

//------------------------------------Edit Payment ------------------------------\\
function Edit_Payment(payment) {
    loading.value = true;
    reset_form_payment();
    EditPaiementMode.value = true;

    payment.value.id = payment.id;
    payment.value.Ref = payment.Ref;
    payment.value.Reglement = payment.Reglement;
    payment.value.date = payment.date;
    payment.value.change = payment.change;
    payment.value.montant = payment.montant;
    payment.value.received_amount = parseFloat(
        payment.montant + payment.change
    ).toFixed(2);
    payment.value.notes = payment.notes;

    due.value = parseFloat(sale_due.value) + payment.montant;
    setTimeout(() => {
        // Complete the animation of the  progress bar.
        loading.value = false;
        dialogAddPayment.value = true;
    }, 1000);
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
    payment.valueProcessing = true;
    loading.value = true;
    snackbar.value = false;
    axios
        .post("/payment_sale", {
            sale_id: sale.id,
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
        })
        .then((response) => {
            payment.valueProcessing = false;
            snackbar.value = true;
            snackbarColor.value = "success";
            snackbarText.value = "Transaccion realizada con exito";
        })
        .catch((error) => {
            payment.valueProcessing = false;
        })
        .finally(() => {
            loading.value = false;
        });
}

//---------------------------------------- Update Payment ------------------------------\\
function Update_Payment() {
    payment.valueProcessing = true;
    loading.value = true;
    snackbar.value = false;

    axios
        .put("payment_sale/" + payment.value.id, {
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
        })
        .then((response) => {
            payment.valueProcessing = false;
            snackbar.value = true;
            snackbarColor.value = "success";
            snackbarText.value = "Transaccion realizada con exito";
        })
        .catch((error) => {
            payment.valueProcessing = false;
        })
        .finally(() => {
            loading.value = false;
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

function Remove_Payment(id) {
    snackbar.value = false;
    axios
        .delete("/payment_sale/" + sale.value.id)
        .then(({ data }) => {
            snackbar.value = true;
            snackbarColor.value = "success";
            snackbarText.value = "Borrado exitoso";
            router.reload();
            dialogDelete.value = false;
        })
        .catch((error) => {
            console.log(error);
            snackbar.value = true;
            snackbarColor.value = "error";
            snackbarText.value = error.response.data.message;
        })
        .finally(() => {
            setTimeout(() => {
                loading.value = false;
            }, 1000);
        });
}

//----------------------------------------- Get Payments  -------------------------------\\
function Get_Payments(id) {
    dialogShowPayment.value = false;
    axios
        .get("/get_payments_by_sale/" + id)
        .then(({ data }) => {
            payments.value = data.payments;
            sale.value_due = data.due;
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
    snackbar.value = false;
    if (sale_has_return == "yes") {
        snackbar.value = true;
        snackbarColor.value = "error";
        snackbarText.value = "Existe una devolucion en la transaccion";
    } else {
        axios
            .delete("/sales/" + id)
            .then(({ data }) => {
                snackbar.value = true;
                snackbarColor.value = "success";
                snackbarText.value = "Borrado exitoso";
                router.reload();
                dialogDelete.value = false;
            })
            .catch((error) => {
                console.log(error);
                snackbar.value = true;
                snackbarColor.value = "error";
                snackbarText.value = error.response.data.message;
            })
            .finally(() => {
                setTimeout(() => {
                    loading.value = false;
                }, 1000);
            });
    }
}
</script>

<template>
    <layout :loading="loading">
        <snackbar
            :snackbar="snackbar"
            :snackbar-text="snackbarText"
            :snackbar-color="snackbarColor"
        ></snackbar>
        <!-- Modal Remove Adjustment -->
        <delete-dialog
            :model="dialogDelete"
            :on-save="Remove_Sale"
            :on-close="onCloseDelete"
        ></delete-dialog>
        <!-- Modal Show Payments-->
        <v-dialog v-model="dialogShowPayment" width="600">
            <v-card>
                <v-toolbar title="Pagos realizados"></v-toolbar>
                <v-card-text>
                    <v-row class="mb-3">
                        <v-col>
                            <v-table density="compact" hover>
                                <thead>
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Codigo</th>
                                        <th>Monto</th>
                                        <th>Pagado en</th>
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
                                                rulesForm.formatNumber(
                                                    payment.montant,
                                                    2
                                                )
                                            }}
                                        </td>
                                        <td>{{ payment.Reglement }}</td>
                                        <td>
                                            <div
                                                role="group"
                                                aria-label="Basic example"
                                                class="btn-group"
                                            >
                                                <span
                                                    title="Print"
                                                    class="btn btn-icon btn-info btn-sm"
                                                    @click="
                                                        Payment_Sale_PDF(
                                                            payment,
                                                            payment.id
                                                        )
                                                    "
                                                >
                                                    <i class="i-Billing"></i>
                                                </span>
                                                <span
                                                    title="Edit"
                                                    class="btn btn-icon btn-success btn-sm"
                                                    @click="
                                                        Edit_Payment(payment)
                                                    "
                                                >
                                                    <i class="i-Pen-2"></i>
                                                </span>

                                                <span
                                                    title="Delete"
                                                    class="btn btn-icon btn-danger btn-sm"
                                                    @click="
                                                        Remove_Payment(
                                                            payment.id
                                                        )
                                                    "
                                                >
                                                    <i class="i-Close"></i>
                                                </span>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </v-table>
                        </v-col>
                    </v-row>
                </v-card-text>
            </v-card>
        </v-dialog>
        <v-row align="center" class="mb-3">
            <v-col>
                <v-text-field
                    v-model="search"
                    prepend-icon="mdi-magnify"
                    density="compact"
                    hide-details
                    label="Buscar"
                    single-line
                    variant="underlined"
                ></v-text-field>
            </v-col>
            <v-spacer></v-spacer>
            <v-col cols="auto" class="text-right">
                <filter_form
                    :filter_form="filter_form"
                    :customers="customers"
                    :sales_types="sales_types"
                ></filter_form>
                <ExportBtn
                    :data="sales"
                    :fields="jsonFields"
                    name-file="Productos"
                ></ExportBtn>
                <v-btn
                    size="small"
                    color="primary"
                    class="ma-1"
                    variant="flat"
                    prepend-icon="mdi-account-plus"
                    @click="router.visit('/sales/create')"
                >
                    Añadir
                </v-btn>
            </v-col>
        </v-row>
        <v-card>
            <v-data-table
                :headers="fields"
                :items="sales"
                :search="search"
                hover
                density="compact"
                no-data-text="No existen datos a mostrar"
            >
                <template v-slot:item.statut="{ item }">
                    <v-chip
                        color="primary"
                        variant="tonal"
                        size="x-small"
                        v-if="item.raw.statut == 'completed'"
                        >Completado</v-chip
                    >
                    <v-chip
                        color="secondary"
                        variant="tonal"
                        size="x-small"
                        v-else-if="item.raw.statut == 'pending'"
                        >Pendiente</v-chip
                    >
                    <v-chip
                        color="info"
                        variant="tonal"
                        size="x-small"
                        v-else-if="item.raw.statut == 'ordered'"
                        >Ordenado</v-chip
                    >
                </template>
                <template v-slot:item.payment_status="{ item }">
                    <v-chip
                        color="success"
                        variant="tonal"
                        size="x-small"
                        v-if="item.raw.payment_status == 'paid'"
                        >Pagado</v-chip
                    >
                    <v-chip
                        color="warning"
                        variant="tonal"
                        size="x-small"
                        v-else-if="item.raw.payment_status == 'partial'"
                        >Parcial</v-chip
                    >
                    <v-chip
                        color="error"
                        variant="tonal"
                        size="x-small"
                        v-else-if="item.raw.payment_status == 'unpaid'"
                        >Deuda</v-chip
                    >
                </template>
                <template v-slot:item.actions="{ item }">
                    <v-menu>
                        <template v-slot:activator="{ props }">
                            <v-btn
                                v-bind="props"
                                class="ma-1"
                                icon="mdi-dots-vertical"
                                color="primary"
                                size="x-small"
                                variant="elevated"
                            >
                            </v-btn>
                        </template>
                        <v-list nav density="compact">
                            <v-list-item
                                @click="
                                    router.visit('/sales/detail/' + item.raw.id)
                                "
                                prepend-icon="mdi-paper"
                            >
                                Detalle de Orden
                            </v-list-item>
                            <v-list-item
                                @click="
                                    router.visit('/sales/edit/' + item.raw.id)
                                "
                                prepend-icon="mdi-pen"
                            >
                                Editar Orden
                            </v-list-item>
                            <v-list-item
                                @click="Show_Payments(item.raw.id, item.raw)"
                                prepend-icon="mdi-pen"
                            >
                                Ver Pagos
                            </v-list-item>
                            <!--                                  v-if="currentUserPermissions.includes('payment_sales_add')"-->
                            <v-list-item
                                @click="New_Payment(item.raw)"
                                prepend-icon="mdi-pen"
                            >
                                Añadir Pago
                            </v-list-item>

                            <!--                <v-dropdown-item title="Invoice" @click="Invoice_POS(props.row.id)">-->
                            <!--                  <i class="nav-icon i-File-TXT font-weight-bold mr-2"></i>-->
                            <!--                  {{$t('Invoice_POS')}}-->
                            <!--                </v-dropdown-item>-->

                            <!--                <v-dropdown-item title="PDF" @click="Invoice_PDF(props.row , props.row.id)">-->
                            <!--                  <i class="nav-icon i-File-TXT font-weight-bold mr-2"></i>-->
                            <!--                  {{$t('DownloadPdf')}}-->
                            <!--                </v-dropdown-item>-->
                            <v-list-item
                                @click="Delete_Item(item.raw)"
                                prepend-icon="mdi-pen"
                            >
                                Eliminar
                            </v-list-item>
                        </v-list>
                    </v-menu>
                </template>
            </v-data-table>
        </v-card>
    </layout>

    <!--          <div v-else-if="props.column.field == 'payment_status'">-->
    <!--            <span-->
    <!--              v-if="props.row.payment_status == 'paid'"-->
    <!--              class="badge badge-outline-success"-->
    <!--            >{{$t('Paid')}}</span>-->
    <!--            <span-->
    <!--              v-else-if="props.row.payment_status == 'partial'"-->
    <!--              class="badge badge-outline-primary"-->
    <!--            >{{$t('partial')}}</span>-->
    <!--            <span v-else class="badge badge-outline-warning">{{$t('Unpaid')}}</span>-->
    <!--          </div>-->
    <!--          <div v-else-if="props.column.field == 'shipping_status'">-->
    <!--            <span-->
    <!--              v-if="props.row.shipping_status == 'ordered'"-->
    <!--              class="badge badge-outline-warning"-->
    <!--            >{{$t('Ordered')}}</span>-->

    <!--            <span-->
    <!--              v-else-if="props.row.shipping_status == 'packed'"-->
    <!--              class="badge badge-outline-info"-->
    <!--            >{{$t('Packed')}}</span>-->

    <!--            <span-->
    <!--              v-else-if="props.row.shipping_status == 'shipped'"-->
    <!--              class="badge badge-outline-secondary"-->
    <!--            >{{$t('Shipped')}}</span>-->

    <!--             <span-->
    <!--              v-else-if="props.row.shipping_status == 'delivered'"-->
    <!--              class="badge badge-outline-success"-->
    <!--            >{{$t('Delivered')}}</span>-->

    <!--            <span v-else-if="props.row.shipping_status == 'cancelled'" class="badge badge-outline-danger">{{$t('Cancelled')}}</span>-->
    <!--          </div>-->
    <!--           <div v-else-if="props.column.field == 'Ref'">-->
    <!--              <router-link-->
    <!--                :to="'/app/sales/detail/'+props.row.id"-->
    <!--              >-->
    <!--                <span class="ul-btn__text ml-1">{{props.row.Ref}}</span>-->
    <!--              </router-link> <br>-->
    <!--              <small v-if="props.row.sale_has_return == 'yes'"><i class="text-15 text-danger i-Back"></i></small>-->
    <!--              -->
    <!--            </div>-->
    <!--        </template>-->
    <!--      </vue-good-table>-->
    <!--    </div>-->

    <!--                      [-->
    <!--                        {label: 'Paid', value: 'paid'},-->
    <!--                        {label: 'partial', value: 'partial'},-->
    <!--                        {label: 'UnPaid', value: 'unpaid'},-->
    <!--                      ]"-->

    <!--                      [-->
    <!--                        {label: 'Ordered', value: 'ordered'},-->
    <!--                        {label: 'Packed', value: 'packed'},-->
    <!--                        {label: 'Shipped', value: 'shipped'},-->
    <!--                        {label: 'Delivered', value: 'delivered'},-->
    <!--                        {label: 'Cancelled', value: 'cancelled'},-->
    <!--                      ]"-->

    <!--    &lt;!&ndash; Modal Add Payment&ndash;&gt;-->
    <!--    <validation-observer ref="Add_payment">-->
    <!--      <v-modal-->
    <!--        hide-footer-->
    <!--        size="lg"-->
    <!--        id="Add_Payment"-->
    <!--        :title="EditPaiementMode?$t('EditPayment'):$t('AddPayment')"-->
    <!--      >-->
    <!--        <v-form @submit.prevent="Submit_Payment">-->
    <!--          <v-row>-->
    <!--            &lt;!&ndash; date &ndash;&gt;-->
    <!--            <v-col lg="4" md="12" sm="12">-->
    <!--              <validation-provider-->
    <!--                name="date"-->
    <!--                :rules="{ required: true}"-->
    <!--                v-slot="validationContext"-->
    <!--              >-->
    <!--                <v-form-group :label="$t('date')">-->
    <!--                  <v-form-input-->
    <!--                    label="date"-->
    <!--                    :state="getValidationState(validationContext)"-->
    <!--                    aria-describedby="date-feedback"-->
    <!--                    v-model="payment.date"-->
    <!--                    type="date"-->
    <!--                  ></v-form-input>-->
    <!--                  <v-form-invalid-feedback id="date-feedback">{{ validationContext.errors[0] }}</v-form-invalid-feedback>-->
    <!--                </v-form-group>-->
    <!--              </validation-provider>-->
    <!--            </v-col>-->

    <!--            &lt;!&ndash; Reference  &ndash;&gt;-->
    <!--            <v-col lg="4" md="12" sm="12">-->
    <!--              <v-form-group :label="$t('Reference')">-->
    <!--                <v-form-input-->
    <!--                  disabled="disabled"-->
    <!--                  label="Reference"-->
    <!--                  :placeholder="$t('Reference')"-->
    <!--                  v-model="payment.Ref"-->
    <!--                ></v-form-input>-->
    <!--              </v-form-group>-->
    <!--            </v-col>-->

    <!--             &lt;!&ndash; Payment choice &ndash;&gt;-->
    <!--            <v-col lg="4" md="12" sm="12">-->
    <!--              <validation-provider name="Payment choice" :rules="{ required: true}">-->
    <!--                <v-form-group slot-scope="{ valid, errors }" :label="$t('Paymentchoice')">-->
    <!--                  <v-select-->
    <!--                    :class="{'is-invalid': !!errors.length}"-->
    <!--                    :state="errors[0] ? false : (valid ? true : null)"-->
    <!--                    v-model="payment.Reglement"-->
    <!--                    @input="Selected_PaymentMethod"-->
    <!--                    :disabled="EditPaiementMode && payment.Reglement == 'credit card'"-->
    <!--                    :reduce="label => label.value"-->
    <!--                    :placeholder="$t('PleaseSelect')"-->
    <!--                    :options="-->
    <!--                          [-->
    <!--                          {label: 'Cash', value: 'Cash'},-->
    <!--                          {label: 'credit card', value: 'credit card'},-->
    <!--                          {label: 'TPE', value: 'tpe'},-->
    <!--                          {label: 'cheque', value: 'cheque'},-->
    <!--                          {label: 'Western Union', value: 'Western Union'},-->
    <!--                          {label: 'bank transfer', value: 'bank transfer'},-->
    <!--                          {label: 'other', value: 'other'},-->
    <!--                          ]"-->
    <!--                  ></v-select>-->
    <!--                  <v-form-invalid-feedback>{{ errors[0] }}</v-form-invalid-feedback>-->
    <!--                </v-form-group>-->
    <!--              </validation-provider>-->
    <!--            </v-col>-->

    <!--            &lt;!&ndash; Received  Amount  &ndash;&gt;-->
    <!--            <v-col lg="4" md="12" sm="12">-->
    <!--                <validation-provider-->
    <!--                  name="Received Amount"-->
    <!--                  :rules="{ required: true , regex: /^\d*\.?\d*$/}"-->
    <!--                  v-slot="validationContext"-->
    <!--                >-->
    <!--                <v-form-group :label="$t('Received_Amount')">-->
    <!--                  <v-form-input-->
    <!--                    @keyup="Verified_Received_Amount(payment.received_amount)"-->
    <!--                    label="Received_Amount"-->
    <!--                    :placeholder="$t('Received_Amount')"-->
    <!--                    v-model.number="payment.received_amount"-->
    <!--                    :state="getValidationState(validationContext)"-->
    <!--                    aria-describedby="Received_Amount-feedback"-->
    <!--                  ></v-form-input>-->
    <!--                  <v-form-invalid-feedback-->
    <!--                    id="Received_Amount-feedback"-->
    <!--                  >{{ validationContext.errors[0] }}</v-form-invalid-feedback>-->
    <!--                </v-form-group>-->
    <!--              </validation-provider>-->
    <!--            </v-col>-->

    <!--            &lt;!&ndash; Paying Amount  &ndash;&gt;-->
    <!--            <v-col lg="4" md="12" sm="12">-->
    <!--              <validation-provider-->
    <!--                name="Amount"-->
    <!--                :rules="{ required: true , regex: /^\d*\.?\d*$/}"-->
    <!--                v-slot="validationContext"-->
    <!--              >-->
    <!--                <v-form-group :label="$t('Paying_Amount')">-->
    <!--                  <v-form-input-->
    <!--                   @keyup="Verified_paidAmount(payment.montant)"-->
    <!--                    label="Amount"-->
    <!--                    :placeholder="$t('Paying_Amount')"-->
    <!--                    v-model.number="payment.montant"-->
    <!--                    :state="getValidationState(validationContext)"-->
    <!--                    aria-describedby="Amount-feedback"-->
    <!--                  ></v-form-input>-->
    <!--                  <v-form-invalid-feedback id="Amount-feedback">{{ validationContext.errors[0] }}</v-form-invalid-feedback>-->
    <!--                </v-form-group>-->
    <!--              </validation-provider>-->
    <!--            </v-col>-->

    <!--            &lt;!&ndash; change Amount  &ndash;&gt;-->
    <!--            <v-col lg="4" md="12" sm="12">-->
    <!--              <label>{{$t('Change')}} :</label>-->
    <!--              <p-->
    <!--                class="change_amount"-->
    <!--              >{{parseFloat(payment.received_amount - payment.montant).toFixed(2)}}</p>-->
    <!--            </v-col>-->

    <!--           -->

    <!--            <v-col md="12" v-if="payment.Reglement == 'credit card'">-->
    <!--              <form id="payment-form">-->
    <!--                <label-->
    <!--                  for="card-element"-->
    <!--                  class="leading-7 text-sm text-gray-600"-->
    <!--                >{{$t('Credit_Card_Info')}}</label>-->
    <!--                <div id="card-element">-->
    <!--                  &lt;!&ndash; Elements will create input elements here &ndash;&gt;-->
    <!--                </div>-->
    <!--                &lt;!&ndash; We'll put the error messages in this element &ndash;&gt;-->
    <!--                <div id="card-errors" class="is-invalid" role="alert"></div>-->
    <!--              </form>-->
    <!--            </v-col>-->

    <!--            &lt;!&ndash; Note &ndash;&gt;-->
    <!--            <v-col lg="12" md="12" sm="12" class="mt-3">-->
    <!--              <v-form-group :label="$t('Note')">-->
    <!--                <v-form-textarea id="textarea" v-model="payment.notes" rows="3" max-rows="6"></v-form-textarea>-->
    <!--              </v-form-group>-->
    <!--            </v-col>-->
    <!--            <v-col md="12" class="mt-3">-->
    <!--              <v-button-->
    <!--                variant="primary"-->
    <!--                type="submit"-->
    <!--                :disabled="paymentProcessing"-->
    <!--              >{{$t('submit')}}</v-button>-->
    <!--              <div v-once class="typo__p" v-if="paymentProcessing">-->
    <!--                <div class="spinner sm spinner-primary mt-3"></div>-->
    <!--              </div>-->
    <!--            </v-col>-->
    <!--          </v-row>-->
    <!--        </v-form>-->
    <!--      </v-modal>-->
    <!--    </validation-observer>-->

    <!--     &lt;!&ndash; Modal Edit Shipment &ndash;&gt;-->
    <!--    <validation-observer ref="shipment_ref">-->
    <!--      <v-modal hide-footer size="md" id="modal_shipment" :title="$t('Edit')">-->
    <!--        <v-form @submit.prevent="Submit_Shipment">-->
    <!--          <v-row>-->
    <!--            &lt;!&ndash; Status  &ndash;&gt;-->
    <!--            <v-col md="12">-->
    <!--              <validation-provider name="Status" :rules="{ required: true}">-->
    <!--                <v-form-group slot-scope="{ valid, errors }" :label="$t('Status') + ' ' + '*'">-->
    <!--                  <v-select-->
    <!--                    :class="{'is-invalid': !!errors.length}"-->
    <!--                    :state="errors[0] ? false : (valid ? true : null)"-->
    <!--                    v-model="shipment.status"-->
    <!--                    :reduce="label => label.value"-->
    <!--                    :placeholder="$t('Choose_Status')"-->
    <!--                    :options="-->
    <!--                                [-->
    <!--                                  {label: 'Ordered', value: 'ordered'},-->
    <!--                                  {label: 'Packed', value: 'packed'},-->
    <!--                                  {label: 'Shipped', value: 'shipped'},-->
    <!--                                  {label: 'Delivered', value: 'delivered'},-->
    <!--                                  {label: 'Cancelled', value: 'cancelled'},-->
    <!--                                ]"-->
    <!--                  ></v-select>-->
    <!--                  <v-form-invalid-feedback>{{ errors[0] }}</v-form-invalid-feedback>-->
    <!--                </v-form-group>-->
    <!--              </validation-provider>-->
    <!--            </v-col>-->

    <!--            <v-col md="12">-->
    <!--              <v-form-group :label="$t('delivered_to')">-->
    <!--                <v-form-input-->
    <!--                  label="delivered_to"-->
    <!--                  v-model="shipment.delivered_to"-->
    <!--                  :placeholder="$t('delivered_to')"-->
    <!--                ></v-form-input>-->
    <!--              </v-form-group>-->
    <!--            </v-col>-->

    <!--            <v-col md="12">-->
    <!--              <v-form-group :label="$t('Adress')">-->
    <!--                <textarea-->
    <!--                  v-model="shipment.shipping_address"-->
    <!--                  rows="4"-->
    <!--                  class="form-control"-->
    <!--                  :placeholder="$t('Enter_Address')"-->
    <!--                ></textarea>-->
    <!--              </v-form-group>-->
    <!--            </v-col>-->

    <!--            <v-col md="12">-->
    <!--              <v-form-group :label="$t('Please_provide_any_details')">-->
    <!--                <textarea-->
    <!--                  v-model="shipment.shipping_details"-->
    <!--                  rows="4"-->
    <!--                  class="form-control"-->
    <!--                  :placeholder="$t('Please_provide_any_details')"-->
    <!--                ></textarea>-->
    <!--              </v-form-group>-->
    <!--            </v-col>-->

    <!--            <v-col md="12" class="mt-3">-->
    <!--              <v-button-->
    <!--                variant="primary"-->
    <!--                type="submit"-->
    <!--                :disabled="Submit_Processing_shipment"-->
    <!--              >{{$t('submit')}}</v-button>-->
    <!--              <div v-once class="typo__p" v-if="Submit_Processing_shipment">-->
    <!--                <div class="spinner sm spinner-primary mt-3"></div>-->
    <!--              </div>-->
    <!--            </v-col>-->
    <!--          </v-row>-->
    <!--        </v-form>-->
    <!--      </v-modal>-->
    <!--    </validation-observer>-->

    <!--    &lt;!&ndash; Modal Show Invoice POS&ndash;&gt;-->
    <!--    <v-modal hide-footer size="sm" scrollable id="Show_invoice" :title="$t('Invoice_POS')">-->
    <!--        <div id="invoice-POS">-->
    <!--          <div style="max-width:400px;margin:0px auto">-->
    <!--          <div class="info" >-->
    <!--            <h2 class="text-center">{{invoice_pos.setting.CompanyName}}</h2>-->

    <!--            <p>-->
    <!--                <span>{{$t('date')}} : {{invoice_pos.sale.date}} <br></span>-->
    <!--                <span v-show="pos_settings.show_address">{{$t('Adress')}} : {{invoice_pos.setting.CompanyAdress}} <br></span>-->
    <!--                <span v-show="pos_settings.show_email">{{$t('Email')}} : {{invoice_pos.setting.email}} <br></span>-->
    <!--                <span v-show="pos_settings.show_phone">{{$t('Phone')}} : {{invoice_pos.setting.CompanyPhone}} <br></span>-->
    <!--                <span v-show="pos_settings.show_customer">{{$t('Customer')}} : {{invoice_pos.sale.client_name}} <br></span>-->
    <!--              </p>-->
    <!--          </div>-->

    <!--          <table>-->
    <!--            <tbody>-->
    <!--              <tr v-for="detail_invoice in invoice_pos.details">-->
    <!--                <td colspan="3">-->
    <!--                  {{detail_invoice.name}}-->
    <!--                  <br v-show="detail_invoice.is_imei && detail_invoice.imei_number !==null">-->
    <!--                  <span v-show="detail_invoice.is_imei && detail_invoice.imei_number !==null ">{{$t('IMEI_SN')}} : {{detail_invoice.imei_number}}</span>-->
    <!--                  <br>-->
    <!--                  <span>{{formatNumber(detail_invoice.quantity,2)}} {{detail_invoice.unit_sale}} x {{formatNumber(detail_invoice.total/detail_invoice.quantity,2)}}</span>-->
    <!--                </td>-->
    <!--                <td style="text-align:right;vertical-align:bottom">{{formatNumber(detail_invoice.total,2)}}</td>-->
    <!--              </tr>-->

    <!--              <tr style="margin-top:10px" v-show="pos_settings.show_discount">-->
    <!--                <td colspan="3" class="total">{{$t('OrderTax')}}</td>-->
    <!--                <td style="text-align:right;" class="total">{{invoice_pos.symbol}} {{formatNumber(invoice_pos.sale.taxe ,2)}} ({{formatNumber(invoice_pos.sale.tax_rate,2)}} %)</td>-->
    <!--              </tr>-->

    <!--              <tr style="margin-top:10px" v-show="pos_settings.show_discount">-->
    <!--                <td colspan="3" class="total">{{$t('Discount')}}</td>-->
    <!--                <td style="text-align:right;" class="total">{{invoice_pos.symbol}} {{formatNumber(invoice_pos.sale.discount ,2)}}</td>-->
    <!--              </tr>-->

    <!--              <tr style="margin-top:10px" v-show="pos_settings.show_discount">-->
    <!--                <td colspan="3" class="total">{{$t('Shipping')}}</td>-->
    <!--                <td style="text-align:right;" class="total">{{invoice_pos.symbol}} {{formatNumber(invoice_pos.sale.shipping ,2)}}</td>-->
    <!--              </tr>-->

    <!--              <tr style="margin-top:10px">-->
    <!--                <td colspan="3" class="total">{{$t('Total')}}</td>-->
    <!--                <td style="text-align:right;" class="total">{{invoice_pos.symbol}} {{formatNumber(invoice_pos.sale.GrandTotal ,2)}}</td>-->
    <!--              </tr>-->

    <!--                  <tr v-show="invoice_pos.sale.paid_amount < invoice_pos.sale.GrandTotal">-->
    <!--                    <td colspan="3" class="total">{{$t('Paid')}}</td>-->
    <!--                    <td-->
    <!--                      style="text-align:right;"-->
    <!--                      class="total"-->
    <!--                    >{{invoice_pos.symbol}} {{formatNumber(invoice_pos.sale.paid_amount ,2)}}</td>-->
    <!--                  </tr>-->

    <!--                  <tr v-show="invoice_pos.sale.paid_amount < invoice_pos.sale.GrandTotal">-->
    <!--                    <td colspan="3" class="total">{{$t('Due')}}</td>-->
    <!--                    <td-->
    <!--                      style="text-align:right;"-->
    <!--                      class="total"-->
    <!--                    >{{invoice_pos.symbol}} {{parseFloat(invoice_pos.sale.GrandTotal - invoice_pos.sale.paid_amount).toFixed(2)}}</td>-->
    <!--                  </tr>-->
    <!--            </tbody>-->
    <!--          </table>-->

    <!--           <table-->
    <!--                class="change mt-3"-->
    <!--                style=" font-size: 10px;"-->
    <!--                v-show="invoice_pos.sale.paid_amount > 0"-->
    <!--              >-->
    <!--                <thead>-->
    <!--                  <tr style="background: #eee; ">-->
    <!--                    <th style="text-align: left;" colspan="1">{{$t('PayeBy')}}:</th>-->
    <!--                    <th style="text-align: center;" colspan="2">{{$t('Amount')}}:</th>-->
    <!--                    <th style="text-align: right;" colspan="1">{{$t('Change')}}:</th>-->
    <!--                  </tr>-->
    <!--                </thead>-->

    <!--                <tbody>-->
    <!--                  <tr v-for="payment_pos in payments">-->
    <!--                    <td style="text-align: left;" colspan="1">{{payment_pos.Reglement}}</td>-->
    <!--                    <td-->
    <!--                      style="text-align: center;"-->
    <!--                      colspan="2"-->
    <!--                    >{{formatNumber(payment_pos.montant ,2)}}</td>-->
    <!--                    <td-->
    <!--                      style="text-align: right;"-->
    <!--                      colspan="1"-->
    <!--                    >{{formatNumber(payment_pos.change ,2)}}</td>-->
    <!--                  </tr>-->
    <!--                </tbody>-->
    <!--              </table>-->

    <!--          <div id="legalcopy" class="ml-2">-->
    <!--            <p class="legal" v-show="pos_settings.show_note">-->
    <!--               <strong>{{pos_settings.note_customer}}</strong>-->
    <!--            </p>-->
    <!--            <div id="bar" v-show="pos_settings.show_barcode">-->
    <!--              <barcode-->
    <!--                class="barcode"-->
    <!--                :format="barcodeFormat"-->
    <!--                :value="invoice_pos.sale.Ref"-->
    <!--                textmargin="0"-->
    <!--                fontoptions="bold"-->
    <!--                fontSize= "15"-->
    <!--                height= "25"-->
    <!--                width= "1"-->
    <!--              ></barcode>-->
    <!--            </div>-->
    <!--            </div>-->
    <!--          </div>-->
    <!--        </div>-->
    <!--      <button @click="print_it()" class="btn btn-outline-primary">-->
    <!--        <i class="i-Billing"></i>-->
    <!--        {{$t('print')}}-->
    <!--      </button>-->
    <!--    </v-modal>-->
    <!--  </div>-->
</template>

<style>
.total {
    font-weight: bold;
    font-size: 14px;
    /* text-transform: uppercase;
height: 50px; */
}
</style>
