<script setup>
import {onMounted, ref} from "vue";
import {router} from "@inertiajs/vue3";
import Layout from "@/Layouts/Authenticated.vue";
import Snackbar from "@/Components/snackbar.vue";
import printJS from "print-js";
import helper from "@/helpers";
import DeleteDialog from "@/Components/DeleteDialog.vue";

const props = defineProps({
  details: Object,
  sale: Object,
  company: Object,
});

const loading = ref(false);
const snackbar = ref(false);
const snackbarText = ref("");
const snackbarColor = ref("info");
const dialogDelete = ref(false);
const dialogInvoice = ref(false);
const invoice_pos = ref({});

const variants = ref([]);

//----------------------------------- Invoice Sale PDF  -------------------------\\
function Sale_PDF(id) {
  loading.value = true;

  axios
      .get(`/sale_pdf/${id}`, {
        responseType: "blob", // important
        headers: {
          "Content-Type": "application/json",
        },
      })
      .then((response) => {
        const url = window.URL.createObjectURL(new Blob([response.data]));
        const link = document.createElement("a");
        link.href = url;
        link.setAttribute("download", "Sale_" + props.sale.Ref + ".pdf");
        document.body.appendChild(link);
        link.click();
        // Complete the animation of the  progress bar.
      })
      .catch(() => {
        // Complete the animation of the  progress bar.
      })
      .finally(() => {
        loading.value = false;
      });
}

//------------------------------ Print -------------------------\\
function print() {
  printJS("print_Invoice", "html");
}
//------------------------------ Print POS-------------------------\\
function print_POS() {
  Invoice_POS(props.sale.id);
}

//------------------------------------------ DELETE Sale ------------------------------\\
function Delete_Sale() {
  dialogDelete.value = true;
}
function Remove_Sale() {
  snackbar.value = false;
  loading.value = true;
  if (props.sale.sale_has_return == "yes") {
    snackbar.value = true;
    snackbarColor.value = "error";
    snackbarText.value = "Existe una devolucion en la transaccion";
  } else {
    axios
        .delete("/sales/" + props.sale.id)
        .then(({data}) => {
          snackbar.value = true;
          snackbarColor.value = "success";
          snackbarText.value = "Borrado exitoso";
          router.visit("/sales/");
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
//-------------------------------- Invoice POS ------------------------------\\
function Invoice_POS(id) {
  loading.value = true;
  dialogInvoice.value = false;
  snackbar.value = false;
  axios
      .get("/sales_print_invoice/" + id)
      .then(({data}) => {
        invoice_pos.value = data;
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
onMounted(() => {
})
</script>
<template>
  <Layout :loading="loading">
    <snackbar
        v-model="snackbar"
        :snackbarColor="snackbarColor"
        :snackbarText="snackbarText"
    >
    </snackbar>
    <!-- Modal Remove Sale -->
    <delete-dialog
        :model="dialogDelete"
        :on-save="Remove_Sale"
        :on-close="()=>{dialogDelete=false}"
    ></delete-dialog>
    <!-- Modal Show Invoice POS-->
    <v-dialog v-model="dialogInvoice" max-width="350">
      <v-card>
        <v-card-text>
          <div id="invoice-POS">
            <div class="info">
              <h2 class="text-center">{{ invoice_pos.setting?.CompanyName }}</h2>
              <v-col cols="12">

                <span>Fecha : {{ invoice_pos.sale.date }} <br></span>
                <span v-show="invoice_pos.pos_settings.show_address">Direccion : {{ invoice_pos.setting.CompanyAdress??'' }} <br></span>
                <span v-show="invoice_pos.pos_settings.show_email">Correo : {{ invoice_pos.setting?.email }} <br></span>
                <span v-show="invoice_pos.pos_settings.show_phone">Telefono : {{ invoice_pos.setting?.CompanyPhone }} <br></span>
                <span v-show="invoice_pos.pos_settings.show_customer">Cliente : {{ invoice_pos.sale.client_name }} <br></span>
              </v-col>
            </div>
            <v-table density="compact" hover>
              <tbody>
              <tr v-for="detail_invoice in invoice_pos.details">
                <td colspan="3">
                  {{ detail_invoice.name }}
                  <br>
                  <span>{{ helper.formatNumber(detail_invoice.quantity, 2) }} {{ detail_invoice.unit_sale }} x {{ helper.formatNumber(detail_invoice.total / detail_invoice.quantity, 2) }}</span>
                </td>
                <td style="text-align:right;vertical-align:bottom">{{ helper.formatNumber(detail_invoice.total, 2) }}
                </td>
              </tr>

              <tr style="margin-top:10px" v-show="invoice_pos.pos_settings.show_discount">
                <td colspan="3" class="total">Impuesto</td>
                <td style="text-align:right;" class="total">{{ invoice_pos.symbol }}
                  {{ helper.formatNumber(invoice_pos.sale.taxe, 2) }} ({{ helper.formatNumber(invoice_pos.sale.tax_rate, 2) }}
                  %)
                </td>
              </tr>

              <tr style="margin-top:10px" v-show="invoice_pos.pos_settings.show_discount">
                <td colspan="3" class="total">Descuento</td>
                <td style="text-align:right;" class="total">{{ invoice_pos.symbol }}
                  {{ helper.formatNumber(invoice_pos.sale.discount, 2) }}
                </td>
              </tr>

              <tr style="margin-top:10px">
                <td colspan="3" class="total">Total</td>
                <td style="text-align:right;" class="total">{{ invoice_pos.symbol }}
                  {{ helper.formatNumber(invoice_pos.sale.GrandTotal, 2) }}
                </td>
              </tr>

              <tr v-show="invoice_pos.sale.paid_amount < invoice_pos.sale.GrandTotal">
                <td colspan="3" class="total">Pagado</td>
                <td
                    style="text-align:right;"
                    class="total"
                >{{ invoice_pos.symbol }} {{ helper.formatNumber(invoice_pos.sale.paid_amount, 2) }}
                </td>
              </tr>

              <tr v-show="invoice_pos.sale.paid_amount < invoice_pos.sale.GrandTotal">
                <td colspan="3" class="total">Deuda</td>
                <td
                    style="text-align:right;"
                    class="total"
                >{{ invoice_pos.symbol }}
                  {{ parseFloat(invoice_pos.sale.GrandTotal - invoice_pos.sale.paid_amount).toFixed(2) }}
                </td>
              </tr>
              </tbody>
            </v-table>
            <v-table
                class="change mt-3"
                style=" font-size: 12px;"
                v-show="invoice_pos.sale.paid_amount > 0"
                density="compact"
                hover
            >
              <thead>
              <tr style="background: #eee; ">
                <th style="text-align: left;" colspan="1">Pagado Por:</th>
                <th style="text-align: center;" colspan="2">Monto:</th>
                <th style="text-align: right;" colspan="1">Cambio:</th>
              </tr>
              </thead>
              <tbody>
              <tr v-for="payment_pos in invoice_pos.payments">
                <td style="text-align: left;" colspan="1">{{ helper.getReglamentPayment(payment_pos.Reglement)[0].title }}</td>
                <td
                    style="text-align: center;"
                    colspan="2"
                >{{ helper.formatNumber(payment_pos.montant, 2) }}
                </td>
                <td
                    style="text-align: right;"
                    colspan="1"
                >{{ helper.formatNumber(payment_pos.change, 2) }}
                </td>
              </tr>
              </tbody>
            </v-table>
          </div>
        </v-card-text>
        <v-card-actions >
          <v-spacer></v-spacer>
          <v-btn prepend-icon="mdi-printer" @click="helper.print_pos('invoice-POS')" color="primary" variant="outlined">
            Imprimir
          </v-btn>
          <v-spacer></v-spacer>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <v-row>
      <v-col cols="12" class="mb-5">
        <v-btn
            class="mr-1"
            v-if="sale.sale_has_return == 'no'"
            color="success"
            size="small"
            @click="router.visit('/sales/edit/'+sale.id )"
            prepend-icon="mdi-pencil-box-outline"
        > Editar Venta
        </v-btn>
<!--        <v-btn-->
<!--            class="mr-1"-->
<!--            @click="Sale_PDF(sale.id)"-->
<!--            color="primary"-->
<!--            size="small"-->
<!--            prepend-icon="mdi-file-pdf-box"-->
<!--        >-->
<!--          PDF-->
<!--        </v-btn>-->
        <v-btn
            class="mr-1"
            @click="print()"
            color="warning"
            size="small"
            prepend-icon="mdi-printer"
        >
          Imprimir
        </v-btn>
        <v-btn
            class="mr-1"
            @click="print_POS()"
            color="info"
            size="small"
            prepend-icon="mdi-clipboard-outline"
        >
          Imprimir Comprobante
        </v-btn>
        <v-btn
            class="mr-1"
            v-if="sale.sale_has_return == 'no'"
            @click="Delete_Sale()"
            color="error"
            size="small"
            prepend-icon="mdi-close-circle"
        >
          Borrar
        </v-btn>
      </v-col>
    </v-row>
    <v-card>
      <v-card-text class="ma-4">
        <div class="invoice" id="print_Invoice">
          <div class="invoice-print">
            <v-row class="justify-content-md-center">
              <v-col cols="12 text-center mb-3">
                <p class="font-weight-bold text-h5">
                  Detalle de Orden : {{ sale.Ref }}
                </p>
              </v-col>
            </v-row>
            <v-divider></v-divider>
            <v-row class="mt-5">
              <v-col lg="4" md="4" cols="12" class="mb-4">
                <p class="font-weight-bold text-h6">
                  Información del Cliente
                </p>
                <div>{{ sale.client_company_name }}</div>
                <div>{{ sale.client_name }}</div>
                <div>{{ sale.client_email }}</div>
                <div>{{ sale.client_phone }}</div>
                <div>{{ sale.client_adr }}</div>
              </v-col>
              <v-col
                  lg="4"
                  md="4"
                  cols="12"
                  class="mb-4"
                  v-if="company"
              >
                <p class="font-weight-bold text-h6">
                  Información de la Empresa
                </p>
                <div>{{ company.CompanyName }}</div>
                <div>{{ company.email }}</div>
                <div>{{ company.CompanyPhone }}</div>
                <div>{{ company.CompanyAdress }}</div>
              </v-col>
              <v-col lg="4" md="4" cols="12" class="mb-4">
                <p class="font-weight-bold text-h6">
                  Información de la Orden
                </p>
                <div>Codigo : {{ sale.Ref }}</div>
                <div>
                  Estado de Pago :
                  <v-chip
                      v-if="sale.payment_status == 'paid'"
                      color="success"
                      variant="outlined"
                      size="small"
                      density="compact"
                  >Pagado
                  </v-chip>
                  <v-chip
                      v-else-if="sale.payment_status == 'partial'"
                      color="primary"
                      variant="outlined"
                      size="small"
                      density="compact"
                  >Parcial
                  </v-chip>
                  <v-chip
                      v-else
                      color="error"
                      variant="outlined"
                      size="small"
                      density="compact"
                  >Sin Pagar
                  </v-chip>
                </div>
                <div>Agencia : {{ sale.warehouse }}</div>
                <div>
                  Estado :
                  <v-chip
                      v-if="sale.statut == 'completed'"
                      color="success"
                      variant="outlined"
                      size="small"
                      density="compact"
                  >Completo
                  </v-chip>
                  <v-chip
                      v-else-if="sale.statut == 'pending'"
                      color="info"
                      variant="outlined"
                      size="small"
                      density="compact"
                  >Pendiente
                  </v-chip>
                  <v-chip
                      v-else
                      color="warning"
                      variant="outlined"
                      size="small"
                      density="compact"
                  >Ordenado
                  </v-chip>
                </div>
              </v-col>
            </v-row>
            <v-row class="mt-3">
              <v-col cols="12">
                <p class="font-weight-bold text-h6">
                  Resumen de la Orden
                </p>
                <div class="table-responsive">
                  <v-table density="compact" hover>
                    <thead class="bg-gray-300">
                    <tr>
                      <th>Producto</th>
                      <th>Precio x Unidad</th>
                      <th>Cantidad</th>
                      <th>Precio unitario</th>
                      <th>SubTotal</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="detail in details">
                      <td>
                        {{ detail.code }}
                        ({{ detail.name }})
                      </td>
                      <td>
                        Bs
                        {{
                          helper.formatNumber(detail.Net_price, 3)
                        }}
                      </td>
                      <td>
                        {{
                          helper.formatNumber(detail.quantity, 2)
                        }}
                        {{ detail.unit_sale }}
                      </td>
                      <td>
                        Bs
                        {{ helper.formatNumber(detail.price, 2) }}
                      </td>
                      <td>
                        Bs
                        {{ detail.total.toFixed(2) }}
                      </td>
                    </tr>
                    </tbody>
                  </v-table>
                </div>
              </v-col>
            </v-row>
            <v-row class="mt-4">
              <v-col sm="6" offset-sm="6" md="4" offset-md="8" cols="12">
                <v-table density="compact" hover>
                  <tbody>
                  <tr>
                    <td class="font-weight-bold">
                      Total
                    </td>
                    <td class="font-weight-bold">
                      Bs
                      {{ sale.GrandTotal }}
                    </td>
                  </tr>
                  <tr>
                    <td class="font-weight-bold">
                      Pagado
                    </td>
                    <td class="font-weight-bold">
                      Bs
                      {{ sale.paid_amount }}
                    </td>
                  </tr>
                  <tr>
                    <td class="font-weight-bold">
                      Deuda
                    </td>
                    <td class="font-weight-bold">
                      Bs {{ sale.due }}
                    </td>
                  </tr>
                  </tbody>
                </v-table>
              </v-col>
            </v-row>
            <v-divider v-if="sale.note"></v-divider>
            <v-row class="mt-4">
              <v-col cols="12">
                <p>Nota : {{ sale.note }}</p>
              </v-col>
            </v-row>
          </div>
        </div>
      </v-card-text>
    </v-card>
  </Layout>
</template>
