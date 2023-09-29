<script setup>
import {ref} from "vue";
import Layout from "@/Layouts/Authenticated.vue";
import Snackbar from "@/Components/snackbar.vue";
import ExportBtn from "@/Components/ExportBtn.vue";
import DeleteDialog from "@/Components/DeleteDialog.vue";
import {router} from "@inertiajs/vue3";
import helper from "@/helpers";
import labels from "@/labels";
import Filter_form from "@/Pages/Sales/filter_form.vue";
import InvoiceDialog from "@/Components/InvoiceDialog.vue";

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
const dialogDeletePayment = ref(false);
const fields = ref([
  {title: "Fecha", key: "date"},
  {title: "Codigo", key: "Ref"},
  {title: "Cliente", key: "client_name"},
  {title: "Agencia", key: "warehouse_name"},
  {title: "Estado", key: "statut"},
  {title: "Total", key: "GrandTotal"},
  {title: "Pagado", key: "paid_amount"},
  {title: "Deuda", key: "due"},
  {title: "Estado Pago", key: "payment_status"},
  {title: "Acciones", key: "actions"},
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
  snackbar.value = false;
  if (isNaN(payment.value.montant)) {
    payment.value.montant = 0;
  } else if (payment.value.montant > payment.value.received_amount) {
    snackbar.value = true;
    snackbarColor.value = "warning";
    snackbarText.value =
        "El monto de pago es mas alto que el a pagar";
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
  if (payment.value.montant > payment.value.received_amount) {
    payment.value.montant = payment.value.received_amount;
  }
  if (!validate.valid) {
    snackbar.value = true;
    snackbarColor.value = "error";
    snackbarText.value = "Llena los campos correctamente";
  } else if (EditPaymentMode.value) {
    Update_Payment();
  } else {
    Create_Payment();
  }
}

//-------------------------------- Invoice POS ------------------------------\\
function Invoice_POS(id) {
  loading.value = true;
  dialogInvoice.value = false;
  snackbar.value = false;
  axios
      .get("/sales_print_invoice/" + id)
      .then(({data}) => {
        invoice_pos.value = data;
        // payments_pos.value = data.payments;
        // pos_settings.value = data.pos_settings;
        dialogInvoice.value = true;
        // if (response.data.pos_settings.is_printable) {
        //   setTimeout(() => print_it(), 1000);
        // }
      })
      .catch(() => {
      })
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
      .catch(() => {
      })
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
      .catch(() => {
      })
      .finally(() => {
        loading.value = false;
      });
}

function Number_Order_Payment() {
  axios
      .get("/payment_sale_get_number")
      .then(({data}) => (payment.value.Ref = data));
}

//----------------------------------- New Payment Sale ------------------------------\\
function New_Payment(saleItem) {
  dialogAddPayment.value = false;
  snackbar.value = false;
  if (saleItem.payment_status == "paid") {
    snackbar.value = true;
    snackbarColor.value = "error";
    snackbarText.value = "Pago ya realizado";
  } else {
    reset_form_payment();
    EditPaymentMode.value = false;
    sale.value = saleItem;
    payment.value.date = new Date().toISOString().slice(0, 10);
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
  console.log(payment_item)
  due.value = parseFloat(sale_due.value) - payment_item.montant;
  console.log()
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
  loading.value = true;
  snackbar.value = false;
  axios
      .post("/payment_sale", {
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
        snackbar.value = true;
        snackbarColor.value = "success";
        snackbarText.value = "Transaccion realizada con exito";
        router.reload({
          preserveState: true,
          preserveScroll: true,
        });
        dialogAddPayment.value = false;
      })
      .catch((error) => {
        console.log(error);
        snackbar.value = true;
        snackbarColor.value = "error";
        snackbarText.value = error;
      })
      .finally(() => {
        loading.value = false;
      });
}

//---------------------------------------- Update Payment ------------------------------\\
function Update_Payment() {
  loading.value = true;
  snackbar.value = false;

  axios
      .put("/payment_sale/" + payment.value.id, {
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
        snackbar.value = true;
        snackbarColor.value = "success";
        snackbarText.value = "Transaccion realizada con exito";
        router.reload({
          preserveState: true,
          preserveScroll: true,
        });
        dialogAddPayment.value = false;
        dialogShowPayment.value = false;
      })
      .catch((error) => {
        console.log(error);
        snackbar.value = true;
        snackbarColor.value = "error";
        snackbarText.value = error.response.data.message;
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

function Delete_Payment(item) {
  sale.value = item;
  dialogDeletePayment.value = true;
}

function Remove_Payment() {
  snackbar.value = false;
  loading.value = false;
  axios
      .delete("/payment_sale/" + sale.value.id)
      .then(({data}) => {
        snackbar.value = true;
        snackbarColor.value = "success";
        snackbarText.value = "Borrado exitoso";
        router.reload({
          preserveState: true,
          preserveScroll: true,
        });
        dialogDeletePayment.value = false;
        dialogShowPayment.value = false;
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
      .then(({data}) => {
        payments.value = data.payments;
        sale_due.value = data.due;
        dialogShowPayment.value = true;
      })
      .catch(() => {
        // Complete the animation of the  progress bar.
      })
      .finally(() => {
      });
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
        .then(({data}) => {
          snackbar.value = true;
          snackbarColor.value = "success";
          snackbarText.value = "Borrado exitoso";
          router.reload({
            preserveState: true,
            preserveScroll: true,
          });
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
  <layout>
    <snackbar
        :snackbar="snackbar"
        :snackbar-text="snackbarText"
        :snackbar-color="snackbarColor"
    ></snackbar>
    <!-- Modal Remove Sale -->
    <delete-dialog
        :model="dialogDelete"
        :on-save="Remove_Sale"
        :on-close="onCloseDelete"
    ></delete-dialog>
    <!-- Modal Remove Payment -->
    <delete-dialog
        :model="dialogDeletePayment"
        :on-save="Remove_Payment"
        :on-close="()=>{dialogDeletePayment.value=false}"
    ></delete-dialog>
    <!-- Modal Show Payments-->
    <v-dialog v-model="dialogShowPayment" width="800">
      <v-card>
        <v-toolbar>
          <v-toolbar-title>Pagos realizados</v-toolbar-title>
          <v-spacer></v-spacer>
          <v-btn icon="mdi-close" size="small" density="comfortable" variant="tonal" @click="dialogShowPayment=false"></v-btn>
        </v-toolbar>
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
                    {{ helper.formatNumber(payment.montant, 2) }}
                  </td>
                  <td>{{ helper.getReglamentPayment(payment.Reglement)[0].title }}</td>
                  <td>
<!--                    <v-btn-->
<!--                        title="Imprimir"-->
<!--                        size="x-small"-->
<!--                        color="info"-->
<!--                        @click="Payment_Sale_PDF(payment,payment.id)"-->
<!--                        icon="mdi-printer"-->
<!--                        class="ma-1"-->
<!--                    >-->
<!--                    </v-btn>-->
                    <v-btn
                        title="Editar"
                        size="small"
                        density="comfortable"
                        color="success"
                        @click="Edit_Payment(payment)"
                        icon="mdi-pencil"
                        class="mx-1 rounded"
                    >
                    </v-btn>
                    <v-btn
                        title="Borrar"
                        size="small"
                        density="comfortable"
                        color="error"
                        @click="Delete_Payment(payment)"
                        icon="mdi-close"
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
        <v-toolbar :title="((EditPaymentMode)?'Editar ':'Añadir ') + 'Pago'"></v-toolbar>
        <v-card-text>
          <v-form ref="form">
            <v-row>
              <!-- date -->
              <v-col md="4" cols="12">
                <v-text-field
                    :label="paymentLabel.date + ' *'"
                    v-model="payment.date"
                    :placeholder="paymentLabel.date"
                    :rules="helper.required"
                    variant="outlined"
                    density="comfortable"
                    hide-details="auto"
                >
                </v-text-field>
              </v-col>

              <!-- Reference  -->
              <v-col md="4" cols="12">
                <v-text-field
                    :label="paymentLabel.Ref + ' *'"
                    v-model="payment.Ref"
                    :placeholder="paymentLabel.Ref"
                    variant="outlined"
                    density="comfortable"
                    hide-details="auto"
                    readonly=""
                >
                </v-text-field>
              </v-col>

              <!-- Payment choice -->
              <v-col md="4" cols="12">
                <v-select
                    v-model="payment.Reglement"
                    :items="helper.reglamentPayment()"
                    :rules="helper.required"
                    :label="labels.payment.role"
                    item-title="title"
                    item-value="value"
                    variant="outlined"
                    density="comfortable"
                    hide-details="auto"
                ></v-select>
              </v-col>

              <!-- Received  Amount  -->
              <v-col md="4" cols="12">
                <v-text-field
                    label="Deuda"
                    v-model="payment.received_amount"
                    placeholder="Deuda"
                    variant="outlined"
                    density="comfortable"
                    hide-details="auto"
                    readonly
                >
                </v-text-field>
              </v-col>

              <!-- Paying Amount  -->
              <v-col md="4" cols="12">
                <v-text-field
                    :label="paymentLabel.montant"
                    v-model="payment.montant"
                    :placeholder="paymentLabel.montant"
                    :rules="helper.required.concat(helper.numberWithDecimal)"
                    variant="outlined"
                    density="comfortable"
                    hide-details="auto"
                    @keyup="Verified_paidAmount"
                >
                </v-text-field>
              </v-col>

              <!-- change Amount  -->
              <v-col md="4" cols="12">
                <v-text-field
                    label="Saldo"
                    :model-value="parseFloat(payment.received_amount - payment.montant).toFixed(2)"
                    placeholder="Saldo"
                    variant="outlined"
                    density="comfortable"
                    hide-details="auto"
                    readonly
                >
                </v-text-field>
              </v-col>

              <!-- Note -->
              <v-col cols="12">
                <v-textarea
                    rows="4"
                    :label="paymentLabel.notes"
                    v-model="payment.notes"
                    :placeholder="paymentLabel.notes"
                    variant="outlined"
                    density="comfortable"
                    hide-details="auto"
                ></v-textarea>
              </v-col>
            </v-row>
          </v-form>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn
              size="small"
              variant="outlined"
              color="error"
              class="ma-1"
              @click="dialogAddPayment=false"
          >
            Cancelar
          </v-btn>
          <v-btn
              size="small"
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
    <invoice-dialog :model="dialogInvoice" :invoice_pos="invoice_pos"></invoice-dialog>

    <v-row align="center" class="mb-3">
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
          :loading="loading"
          loading-text="Cargando..."
      >
        <template v-slot:item.statut="{ item }">
          <v-chip
              :color="helper.statutSaleColor(item.statut)"
              variant="tonal"
              size="x-small"
          >{{helper.statutSale(item.statut)}}</v-chip>
        </template>
        <template v-slot:item.payment_status="{ item }">
          <v-chip
              :color="helper.statusPaymentColor(item.payment_status)"
              variant="tonal"
              size="x-small"
          >{{helper.statusPayment(item.payment_status)}}</v-chip>
        </template>
        <template v-slot:item.Ref="{ item }">
          <v-btn
              variant="tonal"
              size="x-small"
              color="default"
              :text="item.Ref"
              @click="router.visit('/sales/detail/'+item.id)"
          ></v-btn>
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
            <v-list density="compact">
              <v-list-item
                  @click="router.visit('/sales/detail/' + item.id)"
                  prepend-icon="mdi-eye"
              >
                <v-list-item-title>
                  Detalle de Orden
                </v-list-item-title>
              </v-list-item>
              <v-list-item
                  @click="router.visit('/sales/edit/' + item.id)"
                  prepend-icon="mdi-pen"
              >
                <v-list-item-title>
                  Editar Orden
                </v-list-item-title>
              </v-list-item>
              <v-list-item
                  @click="Show_Payments(item.id, item)"
                  prepend-icon="mdi-basket"
              >
                <v-list-item-title>
                  Ver Pagos
                </v-list-item-title>
              </v-list-item>
              <!--                                  v-if="currentUserPermissions.includes('payment_sales_add')"-->
              <v-list-item
                  @click="New_Payment(item)"
                  prepend-icon="mdi-currency-usd"
                  v-if="item.payment_status!='paid'"
              >
                <v-list-item-title>
                  Añadir Pago
                </v-list-item-title>
              </v-list-item>
              <v-list-item
                  @click="Invoice_POS(item.id)"
                  prepend-icon="mdi-printer-pos-outline"
              >
                <v-list-item-title>
                  Impresion Orden
                </v-list-item-title>
              </v-list-item>

              <!--                <v-dropdown-item title="PDF" @click="Invoice_PDF(props.row , props.row.id)">-->
              <!--                  <i class="nav-icon i-File-TXT font-weight-bold mr-2"></i>-->
              <!--                  {{$t('DownloadPdf')}}-->
              <!--                </v-dropdown-item>-->
              <v-list-item
                  @click="Delete_Item(item)"
                  prepend-icon="mdi-delete"
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
  </layout>
</template>
<style>
.total {
  font-weight: bold;
  /*font-size: 14px;*/
  /* text-transform: uppercase;
  height: 50px; */
}
</style>
