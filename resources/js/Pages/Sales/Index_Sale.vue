<script setup>
import {ref} from "vue";
import Layout from "@/Layouts/Authenticated.vue";
import Snackbar from "@/Components/snackbar.vue";
import ExportBtn from "@/Components/ExportBtn.vue";
import DeleteDialog from "@/Components/DeleteDialog.vue";
import {router} from "@inertiajs/vue3";
import helper from "@/helpers";
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
const dialogDeletePayment = ref(false);
const dialogShipment = ref(false);
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
      .then(({data}) => (payment.value.Ref = data));
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
        sale.value.due = data.due;
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
        :on-close="()=>{dialogDeletePayment=false}"
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
                      helper.formatNumber(
                          payment.montant,
                          2
                      )
                    }}
                  </td>
                  <td>{{ payment.Reglement }}</td>
                  <td>
                    <v-btn
                        title="Imprimir"
                        size="x-small"
                        color="info"
                        @click="
                                                        Payment_Sale_PDF(
                                                            payment,
                                                            payment.id
                                                        )
                                                    "
                        icon="mdi-printer"
                        class="ma-1"
                    >
                    </v-btn>
                    <v-btn
                        title="Editar"
                        size="x-small"
                        color="success"
                        @click="
                                                        Edit_Payment(payment)
                                                    "
                        icon="mdi-pencil"
                        class="ma-1"
                    >
                    </v-btn>
                    <v-btn
                        title="Borrar"
                        size="x-small"
                        color="error"
                        @click="
                                                         Delete_Payment(
                                                            payment
                                                        )
                                                    "
                        icon="mdi-close"
                        class="ma-1"
                    >
                    </v-btn>
                    <span
                        title="Delete"
                        class="btn btn-icon btn-danger btn-sm"
                        @click="

                                                    "
                    >
                                                    <i class="i-Close"></i>
                                                </span>
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
              color="primary"
              variant="tonal"
              size="x-small"
              v-if="item.raw.statut == 'completed'"
          >Completado
          </v-chip
          >
          <v-chip
              color="secondary"
              variant="tonal"
              size="x-small"
              v-else-if="item.raw.statut == 'pending'"
          >Pendiente
          </v-chip
          >
          <v-chip
              color="info"
              variant="tonal"
              size="x-small"
              v-else-if="item.raw.statut == 'ordered'"
          >Ordenado
          </v-chip
          >
        </template>
        <template v-slot:item.payment_status="{ item }">
          <v-chip
              color="success"
              variant="tonal"
              size="x-small"
              v-if="item.raw.payment_status == 'paid'"
          >Pagado
          </v-chip
          >
          <v-chip
              color="warning"
              variant="tonal"
              size="x-small"
              v-else-if="item.raw.payment_status == 'partial'"
          >Parcial
          </v-chip
          >
          <v-chip
              color="error"
              variant="tonal"
              size="x-small"
              v-else-if="item.raw.payment_status == 'unpaid'"
          >Deuda
          </v-chip
          >
        </template>
        <template v-slot:item.Ref="{ item }">
          <v-btn
              variant="tonal"
              size="x-small"
              color="default"
              :text="item.raw.Ref"
              @click="router.visit('/sales/detail/'+item.raw.id)"
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
                  @click="
                                    router.visit('/sales/detail/' + item.raw.id)
                                "
                  prepend-icon="mdi-eye"
              >
                <v-list-item-title>
                  Detalle de Orden
                </v-list-item-title>
              </v-list-item>
              <v-list-item
                  @click="
                                    router.visit('/sales/edit/' + item.raw.id)
                                "
                  prepend-icon="mdi-pen"
              >
                <v-list-item-title>
                  Editar Orden
                </v-list-item-title>
              </v-list-item>
              <v-list-item
                  @click="Show_Payments(item.raw.id, item.raw)"
                  prepend-icon="mdi-basket"
              >
                <v-list-item-title>
                  Ver Pagos
                </v-list-item-title>
              </v-list-item>
              <!--                                  v-if="currentUserPermissions.includes('payment_sales_add')"-->
              <v-list-item
                  @click="New_Payment(item.raw)"
                  prepend-icon="mdi-currency-usd"
                  v-if="item.raw.payment_status!='paid'"
              >
                <v-list-item-title>
                  Añadir Pago
                </v-list-item-title>
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

</template>

<style>
.total {
  font-weight: bold;
  font-size: 14px;
  /* text-transform: uppercase;
height: 50px; */
}
</style>
