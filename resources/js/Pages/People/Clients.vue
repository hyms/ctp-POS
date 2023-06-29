<script setup>
import {ref} from "vue";
import Layout from "@/Layouts/Authenticated.vue";
import Snackbar from "@/Components/snackbar.vue";
import ExportBtn from "@/Components/ExportBtn.vue";
import helper from "@/helpers";
import {router} from "@inertiajs/vue3";
import DeleteDialog from "@/Components/DeleteDialog.vue";

const props = defineProps({
  clients: Array,
  company_info: Object,
  errors: Object,
});
//declare variable
const form = ref(null);
const formPayDue = ref(null);
const search = ref("");
const loading = ref(false);
const snackbar = ref(false);
const snackbarText = ref("");
const snackbarColor = ref("info");
const editmode = ref(false);
const dialog = ref(false);
const dialogDelete = ref(false);
const dialogImport = ref(false);
const dialogPayDue = ref(false);
const dialogDetail = ref(false);

const ImportProcessing = ref(false);
const paymentProcessing = ref(false);
const payment_return_Processing = ref(false);

const payment = ref({
  client_id: "",
  client_name: "",
  date: "",
  due: "",
  amount: "",
  notes: "",
  Reglement: "",
});
const payment_return = ref({
  client_id: "",
  client_name: "",
  date: "",
  return_Due: "",
  amount: "",
  notes: "",
  Reglement: "",
});
const selectedIds = ref([]);

const import_clients = ref("");
const client = ref({
  id: "",
  name: "",
  company_name: "",
  code: "",
  email: "",
  phone: "",
  country: "",
  city: "",
  adresse: "",
  nit_ci: "",
});
const clientLabel = ref({
  name: "Nombre",
  company_name: "Nombre de Empresa",
  code: "Codigo",
  email: "Correo",
  phone: "Telefono",
  city: "Ciudad",
  adresse: "Direccion",
  nit_ci: "NIT",
});

const fields = ref([
  {title: "Codigo", key: "code"},
  // { title: "Nombre", key: "name" },
  {title: "Nombre de Empresa", key: "company_name"},
  {title: "Telefono", key: "phone"},
  {title: "NIT", key: "nit_ci"},
  {title: "Deuda Total", key: "due"},
  // { title: "Deuda Total Devolucion", key: "return_Due" },
  {title: "Acciones", key: "actions"},
]);
const jsonFields = ref({
  Codigo: "code",
  Nombre: "name",
  Nombre_Empresa: "company_name",
  Telefono: "phone",
  Nit: "nit_ci",
  Correo: "email",
  Deuda_Total: "due",
  // Deuda_Total_Devolucion: "return_Due",
});

//---------------------- modal  ------------------------------\\
async function onSave() {
  const validate = await form.value.validate();
  if (validate.valid)
    if (!editmode.value) {
      Create_Client();
    } else {
      Update_Client();
    }
}

function onClose() {
  dialog.value = false;
  reset_Form();
}

//----------------------------------- Show import Client -------------------------------\\
function Show_import_clients() {
  dialogImport.value = true;
}

//------------------------------ Event Import clients -------------------------------\\
function onFileSelected(e) {
  import_clients.value = "";
  snackbar.value = false;
  let file = e.target.files[0];
  let errorFilesize;

  if (file["size"] < 1048576) {
    // 1 mega = 1,048,576 Bytes
    errorFilesize = false;
  } else {
    snackbar.value = true;
    snackbarColor.value = "error";
    snackbarText.value = error.response.data.message;
  }

  if (errorFilesize === false) {
    import_clients.value = file;
  }
}

//----------------------------------------Submit  import clients-----------------\\
function Submit_import() {
  loading.value = true;
  let data = new FormData();
  data.append("clients", import_clients.value);
  axios
      .post("clients/import/csv", data)
      .then(({data}) => {
        snackbar.value = true;
        snackbarColor.value = "success";
        snackbarText.value = "Proceso exitoso";
        router.reload({
          preserveState: true,
          preserveScroll: true,
        });

        dialog.value = false;
      })
      .catch((error) => {
        console.log(error);
        snackbar.value = true;
        snackbarColor.value = "error";
        snackbarText.value = error;
      })
      .finally(() => {
        setTimeout(() => {
          loading.value = false;
        }, 1000);
      });
}

//----------------------------------- Show Details Client -------------------------------\\
function showDetails(item) {
  client.value = item;
  dialogDetail.value = true;
}

//------------------------------ Show Modal (create Client) -------------------------------\\
function New_Client() {
  reset_Form();
  editmode.value = false;
  dialog.value = true;
}

//------------------------------ Show Modal (Edit Client) -------------------------------\\
function Edit_Client(item) {
  // Get_Clients(serverParams.page);
  reset_Form();
  client.value = item;
  editmode.value = true;
  dialog.value = true;
}

//---------------------------------------- Create new Client -------------------------------\\
function Create_Client() {
  loading.value = true;
  snackbar.value = false;
  axios
      .post("clients", {
        name: client.value.name,
        company_name: client.value.company_name,
        email: client.value.email,
        phone: client.value.phone,
        nit_ci: client.value.nit_ci,
        country: client.value.country,
        city: client.value.city,
        adresse: client.value.adresse,
      })
      .then(({data}) => {
        snackbar.value = true;
        snackbarColor.value = "success";
        snackbarText.value = "Proceso exitoso";
        router.reload({
          preserveState: true,
          preserveScroll: true,
        });
        dialog.value = false;
      })
      .catch((error) => {
        console.log(error);
        snackbar.value = true;
        snackbarColor.value = "error";
        snackbarText.value = error;
      })
      .finally(() => {
        setTimeout(() => {
          loading.value = false;
        }, 1000);
      });
}

//----------------------------------- Update Client -------------------------------\\
function Update_Client() {
  loading.value = true;
  snackbar.value = false;
  axios
      .put("clients/" + client.value.id, {
        name: client.value.name,
        company_name: client.value.company_name,
        email: client.value.email,
        phone: client.value.phone,
        nit_ci: client.value.nit_ci,
        country: client.value.country,
        city: client.value.city,
        adresse: client.value.adresse,
      })
      .then(({data}) => {
        snackbar.value = true;
        snackbarColor.value = "success";
        snackbarText.value = "Proceso exitoso";
        router.reload({
          preserveState: true,
          preserveScroll: true,
        });

        dialog.value = false;
      })
      .catch((error) => {
        console.log(error);
        snackbar.value = true;
        snackbarColor.value = "error";
        snackbarText.value = error;
      })
      .finally(() => {
        setTimeout(() => {
          loading.value = false;
        }, 1000);
      });
}

//-------------------------------- Reset Form -------------------------------\\
function reset_Form() {
  client.value = {
    id: "",
    name: "",
    company_name: "",
    email: "",
    phone: "",
    country: "",
    nit_ci: "",
    city: "",
    adresse: "",
  };
}

//------------------------------- Remove Client -------------------------------\\
function Remove_Client() {
  loading.value = true;
  snackbar.value = false;
  axios
      .delete("clients/" + client.value.id)
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

function onCloseDelete() {
  reset_Form();
  dialogDelete.value = false;
}

function Delete_Client(item) {
  reset_Form();
  client.value = item;
  dialogDelete.value = true;
}

//------ Validate Form Submit_Payment_sell_due
async function Submit_Payment_sell_due() {
  snackbar.value = false;
  const validate = await formPayDue.value.validate();
  if (!validate.valid) {
    snackbar.value = true;
    snackbarColor.value = "error";
    snackbarText.value = "Por favor llene correctamente los campos";
  } else if (payment.value.amount > payment.value.due) {
    payment.value.amount = 0;
  } else {
    Submit_Pay_due();
  }
}

//---------- keyup paid Amount

function Verified_paidAmount() {
  snackbar.value = false;
  if (isNaN(payment.value.amount)) {
    payment.value.amount = 0;
  } else if (payment.value.amount > payment.value.due) {
    snackbar.value = true;
    snackbarColor.value = "warning";
    snackbarText.value = "El pago no puede ser mayor a la deuda";
    payment.value.amount = 0;
  }
}

//-------------------------------- reset_Form_payment-------------------------------\\
function reset_Form_payment() {
  payment.value = {
    client_id: "",
    client_name: "",
    date: "",
    due: "",
    amount: "",
    notes: "",
    Reglement: "",
  };
}

//------------------------------ Show Modal Pay_due-------------------------------\\
function Pay_due(row) {
  reset_Form_payment();
  payment.value.client_id = row.id;
  payment.value.client_name = row.name;
  payment.value.due = row.due;
  payment.value.date = new Date().toISOString().slice(0, 10);
  setTimeout(() => {
    dialogPayDue.value = true;
  }, 500);
}

//------------------------------ Print Customer_Invoice -------------------------\\
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

//---------------------------------------- Submit_Pay_due-------------------------------\\
function Submit_Pay_due() {
  loading.value = true;
  snackbar.value = false;
  axios
      .post("clients_pay_due", {
        client_id: payment.value.client_id,
        amount: payment.value.amount,
        notes: payment.value.notes,
        Reglement: payment.value.Reglement,
      })
      .then(({data}) => {
        snackbar.value = true;
        snackbarColor.value = "success";
        snackbarText.value = "Proceso exitoso";
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

//-------------------------------Pay sell return due -----------------------------------\\
//
// //------ Validate Form Submit_Payment_sell_return_due
// async function Submit_Payment_sell_return_due() {
//     snackbar.value = false;
//     const validate = await formPayDue.value.validate();
//     if (!validate.valid) {
//         snackbar.value = true;
//         snackbarColor.value = "error";
//         snackbarText.value = "Por favor llene correctamente los campos";
//     } else if (payment.value.amount > payment.value.due) {
//         snackbar.value = true;
//         snackbarColor.value = "error";
//         snackbarText.value = "el monto es mayor a la deuda";
//         payment.value.amount = 0;
//     } else {
//         Submit_Pay_return_due();
//     }
// }
//
// //---------- keyup paid Amount
//
// function Verified_return_paidAmount() {
//     if (isNaN(this.payment_return.amount)) {
//         this.payment_return.amount = 0;
//     } else if (this.payment_return.amount > this.payment_return.return_Due) {
//         this.makeToast(
//             "warning",
//             this.$t("Paying_amount_is_greater_than_Total_Due"),
//             this.$t("Warning")
//         );
//         this.payment_return.amount = 0;
//     }
// }
//
// //-------------------------------- reset_Form_payment-------------------------------\\
// function reset_Form_payment_return_due() {
//     this.payment_return = {
//         client_id: "",
//         client_name: "",
//         date: "",
//         return_Due: "",
//         amount: "",
//         notes: "",
//         Reglement: "",
//     };
// }
//
// //------------------------------ Show Modal Pay_return_due-------------------------------\\
function Pay_return_due(row) {
  // this.reset_Form_payment_return_due();
  // this.payment_return.client_id = row.id;
  // this.payment_return.client_name = row.name;
  // this.payment_return.return_Due = row.return_Due;
  // this.payment_return.date = new Date().toISOString().slice(0, 10);
  // setTimeout(() => {
  //     this.$bvModal.show("modal_Pay_return_due");
  // }, 500);
}

// //------------------------------ Print Customer_Invoice -------------------------\\
// function print_return_due() {
//     let divContents = document.getElementById("invoice-POS-return").innerHTML;
//     let a = window.open("", "", "height=500, width=500");
//     a.document.write('<link rel="stylesheet" href="/css/pos_print.css"><html>');
//     a.document.write("<body >");
//     a.document.write(divContents);
//     a.document.write("</body></html>");
//     a.document.close();
//     setTimeout(() => {
//         a.print();
//     }, 1000);
// }
//
// //---------------------------------------- Submit_Pay_due-------------------------------\\
// function Submit_Pay_return_due() {
//     this.payment_return_Processing = true;
//     axios
//         .post("clients_pay_return_due", {
//             client_id: this.payment_return.client_id,
//             amount: this.payment_return.amount,
//             notes: this.payment_return.notes,
//             Reglement: this.payment_return.Reglement,
//         })
//         .then(({ data }) => {
//             snackbar.value = true;
//             snackbarColor.value = "success";
//             snackbarText.value = "Proceso exitoso";
//             router.reload({
//           preserveState: true,
//           preserveScroll: true,
//         });
//             dialog.value = false;
//         })
//         .catch((error) => {
//             console.log(error);
//             snackbar.value = true;
//             snackbarColor.value = "error";
//             snackbarText.value = error;
//         })
//         .finally(() => {
//             loading.value = false;
//         });
// }
</script>

<template>
  <layout>
    <snackbar
        :snackbar="snackbar"
        :snackbar-text="snackbarText"
        :snackbar-color="snackbarColor"
    ></snackbar>
    <!-- Modal Show Import Clients -->
    <!--        <v-dialog v-model="dialogImport" max-width="600px" scrollable>
                <v-card>
                    <v-toolbar border density="compact" title="Importar Clientes">
                    </v-toolbar>
                    <v-card-text>
                        <v-form
                            @submit.prevent="Submit_import"
                            enctype="multipart/form-data"
                        >
                            <v-row>
                                &lt;!&ndash; File &ndash;&gt;
                                <v-col md="12" sm="12" class="mb-3">
                                    <v-file-input
                                        accept="csv/*"
                                        label="Elige el archivo"
                                        variant="solo"
                                        density="comfortable"
                                        hide-details="auto"
                                        @change="onFileSelected"
                                    ></v-file-input>
                                </v-col>

                                <v-col cols="12" md="6">
                                    <v-btn
                                        type="submit"
                                        color="primary"
                                        variant="elevated"
                                        :disabled="ImportProcessing"
                                        size="small"
                                        block
                                        >Enviar
                                    </v-btn>
                                    <div
                                        v-once
                                        class="typo__p"
                                        v-if="ImportProcessing"
                                    >
                                        <div
                                            class="spinner sm spinner-primary mt-3"
                                        ></div>
                                    </div>
                                </v-col>

                                <v-col cols="12" md="6">
                                    <v-btn
                                        :href="'/import/exemples/import_clients.csv'"
                                        color="info"
                                        variant="elevated"
                                        size="small"
                                        block
                                        >Decargar Ejemplo
                                    </v-btn>
                                </v-col>

                                <v-col md="12" sm="12">
                                    <v-table density="compact">
                                        <tbody>
                                            <tr>
                                                <td>Nombre</td>
                                                <th>
                                                    <v-btn
                                                        variant="outlined"
                                                        density="compact"
                                                        size="small"
                                                        color="success"
                                                        >campo requerido
                                                    </v-btn>
                                                </th>
                                            </tr>
                                            <tr>
                                                <td>telefono</td>
                                            </tr>

                                            <tr>
                                                <td>correo</td>
                                            </tr>

                                            <tr>
                                                <td>pais</td>
                                            </tr>

                                            <tr>
                                                <td>ciudad</td>
                                            </tr>

                                            <tr>
                                                <td>direccion</td>
                                            </tr>
                                            <tr>
                                                <td>numero de impuesto</td>
                                            </tr>
                                        </tbody>
                                    </v-table>
                                </v-col>
                            </v-row>
                        </v-form>
                    </v-card-text>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn
                            size="small"
                            variant="elevated"
                            color="primary"
                            class="ma-1"
                            @click="dialogImport = false"
                        >
                            Cerrar
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>-->
    <!-- Modal Show Customer Details -->
    <v-dialog v-model="dialogDetail" max-width="600px" scrollable>
      <v-card>
        <v-toolbar
            border
            density="compact"
            title="Detalle del Cliente"
        ></v-toolbar>
        <v-card-text>
          <v-table density="compact" hover>
            <tbody>
            <tr>
              <!-- Customer Code -->
              <td>{{ clientLabel.code }}</td>
              <td class="font-weight-bold">
                {{ client.code }}
              </td>
            </tr>
            <tr>
              <!-- Customer Company Name -->
              <td>{{ clientLabel.company_name }}</td>
              <td class="font-weight-bold">
                {{ client.company_name }}
              </td>
            </tr>
            <tr>
              <!-- Customer Name -->
              <td>{{ clientLabel.name }}</td>
              <td class="font-weight-bold">
                {{ client.name }}
              </td>
            </tr>
            <tr>
              <!-- Customer Phone -->
              <td>{{ clientLabel.phone }}</td>
              <td class="font-weight-bold">
                {{ client.phone }}
              </td>
            </tr>
            <tr>
              <!-- Customer Email -->
              <td>{{ clientLabel.email }}</td>
              <td class="font-weight-bold">
                {{ client.email }}
              </td>
            </tr>
            <tr>
              <!-- Customer City -->
              <td>{{ clientLabel.city }}</td>
              <td class="font-weight-bold">
                {{ client.city }}
              </td>
            </tr>
            <tr>
              <!-- Customer Adress -->
              <td>{{ clientLabel.adresse }}</td>
              <td class="font-weight-bold">
                {{ client.adresse }}
              </td>
            </tr>
            <tr>
              <!-- Tax Number -->
              <td>{{ clientLabel.nit_ci }}</td>
              <td class="font-weight-bold">
                {{ client.nit_ci }}
              </td>
            </tr>

            <tr>
              <!-- Total_Sale_Due -->
              <td>Total Deuda</td>
              <td class="font-weight-bold">
                Bs
                {{ client.due }}
              </td>
            </tr>

            <!--                            <tr>-->
            <!--                                &lt;!&ndash; Total_Sell_Return_Due &ndash;&gt;-->
            <!--                                <td>Total Deuda de Devolucion</td>-->
            <!--                                <td>-->
            <!--                                    Bs-->
            <!--                                    {{ client.return_Due }}-->
            <!--                                </td>-->
            <!--                            </tr>-->
            </tbody>
          </v-table>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn
              variant="elevated"
              color="primary"
              class="ma-1"
              @click="dialogDetail = false"
          >
            Cerrar
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
    <!-- Modal Create & Edit Customer -->
    <v-dialog
        v-model="dialog"
        max-width="600px"
        scrollable
        @update:modelValue="dialog === false ? reset_Form() : dialog"
    >
      <v-card>
        <v-toolbar
            density="compact"
            border
            :title="(editmode ? 'Editar ' : 'Añadir ') + 'Cliente'"
        ></v-toolbar>
        <v-card-text>
          <v-form @submit.prevent="onSave" ref="form">
            <v-row>
              <!-- Customer Name -->
              <v-col cols="12" md="6">
                <v-text-field
                    :label="clientLabel.name + ' *'"
                    v-model="client.name"
                    :placeholder="clientLabel.name"
                    :rules="helper.required"
                    variant="outlined"
                    density="comfortable"
                    hide-details="auto"
                >
                </v-text-field>
              </v-col>

              <!-- Customer Company_name -->
              <v-col cols="12" md="6">
                <v-text-field
                    :label="clientLabel.company_name + ' *'"
                    v-model="client.company_name"
                    :placeholder="clientLabel.company_name"
                    :rules="helper.required"
                    variant="outlined"
                    density="comfortable"
                    hide-details="auto"
                >
                </v-text-field>
              </v-col>

              <!-- Customer Email -->
              <v-col cols="12" md="6">
                <v-text-field
                    :label="clientLabel.email"
                    v-model="client.email"
                    :placeholder="clientLabel.email"
                    variant="outlined"
                    density="comfortable"
                    hide-details="auto"
                >
                </v-text-field>
              </v-col>

              <!-- Customer Phone -->
              <v-col cols="12" md="6">
                <v-text-field
                    :label="clientLabel.phone"
                    v-model="client.phone"
                    :placeholder="clientLabel.phone"
                    variant="outlined"
                    density="comfortable"
                    hide-details="auto"
                >
                </v-text-field>
              </v-col>

              <!-- Customer City -->
              <v-col cols="12" md="6">
                <v-text-field
                    :label="clientLabel.city"
                    v-model="client.city"
                    :placeholder="clientLabel.city"
                    variant="outlined"
                    density="comfortable"
                    hide-details="auto"
                >
                </v-text-field>
              </v-col>

              <!-- Customer Tax Number -->
              <v-col cols="12" md="6">
                <v-text-field
                    :label="clientLabel.nit_ci"
                    v-model="client.nit_ci"
                    :placeholder="clientLabel.nit_ci"
                    variant="outlined"
                    density="comfortable"
                    hide-details="auto"
                ></v-text-field>
              </v-col>

              <!-- Customer Adress -->
              <v-col md="12" sm="12">
                <v-textarea
                    rows="4"
                    :label="clientLabel.adresse"
                    v-model="client.adresse"
                    :placeholder="clientLabel.adresse"
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
              @click="onClose"
          >
            Cancelar
          </v-btn>
          <v-btn
              size="small"
              color="primary"
              variant="flat"
              class="ma-1"
              @click="onSave"
              :loading="loading"
              :disabled="loading"
          >
            Guardar
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
    <!-- Modal Remove Customer -->
    <delete-dialog
        :model="dialogDelete"
        :on-save="Remove_Client"
        :on-close="onCloseDelete"
    ></delete-dialog>
    <v-row align="center">
      <v-col cols="12" sm="6">
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
      <v-col cols="12" sm="auto" class="text-right">
        <ExportBtn
            :data="clients"
            :fields="jsonFields"
            name-file="Clientes"
        ></ExportBtn>
        <!--                <v-btn-->
        <!--                    @click="Show_import_clients()"-->
        <!--                    size="small"-->
        <!--                    class="ma-1"-->
        <!--                    color="info"-->
        <!--                    variant="elevated"-->
        <!--                    prepend-icon="mdi-download"-->
        <!--                >-->
        <!--                    Importar-->
        <!--                </v-btn>-->
        <v-btn
            size="small"
            color="primary"
            class="ma-1"
            prepend-icon="mdi-account-plus"
            @click="New_Client"
        >
          Añadir
        </v-btn>
      </v-col>
    </v-row>
    <v-row>
      <v-col>
        <v-data-table
            :headers="fields"
            :items="clients"
            :search="search"
            hover
            class="elevation-2"
            density="compact"
            no-data-text="No existen datos a mostrar"
            :loading="loading"
            loading-text="Cargando..."
        >
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
                <!--                                <v-list-item-->
                <!--                                    v-if="item.raw.due > 0"-->
                <!--                                    @click="Pay_due(item.raw)"-->
                <!--                                    prepend-icon="mdi-currency-usd"-->
                <!--                                >-->
                <!--                                    <v-list-item-title>-->
                <!--                                    Pagar todas la deudas a la vez-->
                <!--                                    </v-list-item-title>-->
                <!--                                </v-list-item>-->

                <!--                                <v-list-item-->
                <!--                                    v-if="item.raw.return_Due > 0"-->
                <!--                                    @click="Pay_return_due(item.raw)"-->
                <!--                                    prepend-icon="mdi-currency-usd"-->

                <!--                                >-->
                <!--                                    <v-list-item-title>-->
                <!--                                    Pagar todas la deudas de devolucion a la vez-->
                <!--                                    </v-list-item-title>-->
                <!--                                </v-list-item>-->

                <v-list-item
                    @click="showDetails(item.raw)"
                    prepend-icon="mdi-eye"
                >
                  <v-list-item-title>
                    Detalles del cliente
                  </v-list-item-title>
                </v-list-item>

                <v-list-item
                    @click="Edit_Client(item.raw)"
                    prepend-icon="mdi-pencil"
                >
                  <v-list-item-title>
                    Editar Cliente
                  </v-list-item-title>
                </v-list-item>

                <v-list-item
                    @click="Delete_Client(item.raw)"
                    prepend-icon="mdi-delete"
                >
                  <v-list-item-title>
                    Eliminar Cliente
                  </v-list-item-title>
                </v-list-item>
              </v-list>
            </v-menu>
          </template>
        </v-data-table>
      </v-col>
    </v-row>
  </layout>

  <!--        &lt;!&ndash; Modal Pay_due&ndash;&gt;-->
  <!--        <validation-observer ref="ref_pay_due">-->
  <!--            <v-dialog hide-footer size="md" id="modal_Pay_due" title="Pay Due">-->
  <!--                <v-form @submit.prevent="Submit_Payment_sell_due">-->
  <!--                    <v-row>-->
  <!--                        &lt;!&ndash; Paying Amount  &ndash;&gt;-->
  <!--                        <v-col lg="6" md="12" sm="12">-->
  <!--                            <validation-provider-->
  <!--                                name="Amount"-->
  <!--                                :rules="{-->
  <!--                                    required: true,-->
  <!--                                    regex: /^\d*\.?\d*$/,-->
  <!--                                }"-->
  <!--                                v-slot="validationContext"-->
  <!--                            >-->
  <!--                                <v-form-group-->
  <!--                                    :label="$t('Paying_Amount') + ' ' + '*'"-->
  <!--                                >-->
  <!--                                    <v-form-input-->
  <!--                                        @keyup="-->
  <!--                                            Verified_paidAmount(payment.amount)-->
  <!--                                        "-->
  <!--                                        label="Amount"-->
  <!--                                        :placeholder="$t('Paying_Amount')"-->
  <!--                                        v-model.number="payment.amount"-->
  <!--                                        :state="-->
  <!--                                            getValidationState(-->
  <!--                                                validationContext-->
  <!--                                            )-->
  <!--                                        "-->
  <!--                                        aria-describedby="Amount-feedback"-->
  <!--                                    ></v-form-input>-->
  <!--                                    <v-form-invalid-feedback-->
  <!--                                        id="Amount-feedback"-->
  <!--                                        >{{ validationContext.errors[0] }}-->
  <!--                                    </v-form-invalid-feedback>-->
  <!--                                    <span class="badge badge-danger"-->
  <!--                                        >{{ $t("Due") }} :-->
  <!--                                        {{ currentUser.currency }}-->
  <!--                                        {{ payment.due }}</span-->
  <!--                                    >-->
  <!--                                </v-form-group>-->
  <!--                            </validation-provider>-->
  <!--                        </v-col>-->

  <!--                        &lt;!&ndash; Payment choice &ndash;&gt;-->
  <!--                        <v-col lg="6" md="12" sm="12">-->
  <!--                            <validation-provider-->
  <!--                                name="Payment choice"-->
  <!--                                :rules="{ required: true }"-->
  <!--                            >-->
  <!--                                <v-form-group-->
  <!--                                    slot-scope="{ valid, errors }"-->
  <!--                                    :label="$t('Paymentchoice') + ' ' + '*'"-->
  <!--                                >-->
  <!--                                    <v-select-->
  <!--                                        :class="{-->
  <!--                                            'is-invalid': !!errors.length,-->
  <!--                                        }"-->
  <!--                                        :state="-->
  <!--                                            errors[0]-->
  <!--                                                ? false-->
  <!--                                                : valid-->
  <!--                                                ? true-->
  <!--                                                : null-->
  <!--                                        "-->
  <!--                                        v-model="payment.Reglement"-->
  <!--                                        :reduce="(label) => label.value"-->
  <!--                                        :placeholder="$t('PleaseSelect')"-->
  <!--                                        :options="[-->
  <!--                                            { label: 'Cash', value: 'Cash' },-->
  <!--                                            {-->
  <!--                                                label: 'credit card',-->
  <!--                                                value: 'credit card',-->
  <!--                                            },-->
  <!--                                            { label: 'TPE', value: 'tpe' },-->
  <!--                                            {-->
  <!--                                                label: 'cheque',-->
  <!--                                                value: 'cheque',-->
  <!--                                            },-->
  <!--                                            {-->
  <!--                                                label: 'Western Union',-->
  <!--                                                value: 'Western Union',-->
  <!--                                            },-->
  <!--                                            {-->
  <!--                                                label: 'bank transfer',-->
  <!--                                                value: 'bank transfer',-->
  <!--                                            },-->
  <!--                                            { label: 'other', value: 'other' },-->
  <!--                                        ]"-->
  <!--                                    ></v-select>-->
  <!--                                    <v-form-invalid-feedback-->
  <!--                                        >{{ errors[0] }}-->
  <!--                                    </v-form-invalid-feedback>-->
  <!--                                </v-form-group>-->
  <!--                            </validation-provider>-->
  <!--                        </v-col>-->

  <!--                        &lt;!&ndash; Note &ndash;&gt;-->
  <!--                        <v-col lg="12" md="12" sm="12" class="mt-3">-->
  <!--                            <v-form-group-->
  <!--                                :label="$t('Please_provide_any_details')"-->
  <!--                            >-->
  <!--                                <v-form-textarea-->
  <!--                                    id="textarea"-->
  <!--                                    v-model="payment.notes"-->
  <!--                                    rows="3"-->
  <!--                                    max-rows="6"-->
  <!--                                ></v-form-textarea>-->
  <!--                            </v-form-group>-->
  <!--                        </v-col>-->

  <!--                        <v-col md="12" class="mt-3">-->
  <!--                            <v-btn-->
  <!--                                variant="primary"-->
  <!--                                type="submit"-->
  <!--                                :disabled="paymentProcessing"-->
  <!--                                >{{ $t("submit") }}-->
  <!--                            </v-btn>-->
  <!--                            <div-->
  <!--                                v-once-->
  <!--                                class="typo__p"-->
  <!--                                v-if="paymentProcessing"-->
  <!--                            >-->
  <!--                                <div-->
  <!--                                    class="spinner sm spinner-primary mt-3"-->
  <!--                                ></div>-->
  <!--                            </div>-->
  <!--                        </v-col>-->
  <!--                    </v-row>-->
  <!--                </v-form>-->
  <!--            </v-dialog>-->
  <!--        </validation-observer>-->

  <!--        &lt;!&ndash; Modal Pay_return_Due&ndash;&gt;-->
  <!--        <validation-observer ref="ref_pay_return_due">-->
  <!--            <v-dialog-->
  <!--                hide-footer-->
  <!--                size="md"-->
  <!--                id="modal_Pay_return_due"-->
  <!--                title="Pay Sell Return Due"-->
  <!--            >-->
  <!--                <v-form @submit.prevent="Submit_Payment_sell_return_due">-->
  <!--                    <v-row>-->
  <!--                        &lt;!&ndash; Paying Amount &ndash;&gt;-->
  <!--                        <v-col lg="6" md="12" sm="12">-->
  <!--                            <validation-provider-->
  <!--                                name="Amount"-->
  <!--                                :rules="{-->
  <!--                                    required: true,-->
  <!--                                    regex: /^\d*\.?\d*$/,-->
  <!--                                }"-->
  <!--                                v-slot="validationContext"-->
  <!--                            >-->
  <!--                                <v-form-group-->
  <!--                                    :label="$t('Paying_Amount') + ' ' + '*'"-->
  <!--                                >-->
  <!--                                    <v-form-input-->
  <!--                                        @keyup="-->
  <!--                                            Verified_return_paidAmount(-->
  <!--                                                payment_return.amount-->
  <!--                                            )-->
  <!--                                        "-->
  <!--                                        label="Amount"-->
  <!--                                        :placeholder="$t('Paying_Amount')"-->
  <!--                                        v-model.number="payment_return.amount"-->
  <!--                                        :state="-->
  <!--                                            getValidationState(-->
  <!--                                                validationContext-->
  <!--                                            )-->
  <!--                                        "-->
  <!--                                        aria-describedby="Amount-feedback"-->
  <!--                                    ></v-form-input>-->
  <!--                                    <v-form-invalid-feedback-->
  <!--                                        id="Amount-feedback"-->
  <!--                                        >{{ validationContext.errors[0] }}-->
  <!--                                    </v-form-invalid-feedback>-->
  <!--                                    <span class="badge badge-danger"-->
  <!--                                        >{{ $t("Due") }} :-->
  <!--                                        {{ currentUser.currency }}-->
  <!--                                        {{ payment_return.return_Due }}</span-->
  <!--                                    >-->
  <!--                                </v-form-group>-->
  <!--                            </validation-provider>-->
  <!--                        </v-col>-->

  <!--                        &lt;!&ndash; Payment choice &ndash;&gt;-->
  <!--                        <v-col lg="6" md="12" sm="12">-->
  <!--                            <validation-provider-->
  <!--                                name="Payment choice"-->
  <!--                                :rules="{ required: true }"-->
  <!--                            >-->
  <!--                                <v-form-group-->
  <!--                                    slot-scope="{ valid, errors }"-->
  <!--                                    :label="$t('Paymentchoice') + ' ' + '*'"-->
  <!--                                >-->
  <!--                                    <v-select-->
  <!--                                        :class="{-->
  <!--                                            'is-invalid': !!errors.length,-->
  <!--                                        }"-->
  <!--                                        :state="-->
  <!--                                            errors[0]-->
  <!--                                                ? false-->
  <!--                                                : valid-->
  <!--                                                ? true-->
  <!--                                                : null-->
  <!--                                        "-->
  <!--                                        v-model="payment_return.Reglement"-->
  <!--                                        :reduce="(label) => label.value"-->
  <!--                                        :placeholder="$t('PleaseSelect')"-->
  <!--                                        :options="[-->
  <!--                                            { label: 'Cash', value: 'Cash' },-->
  <!--                                            {-->
  <!--                                                label: 'credit card',-->
  <!--                                                value: 'credit card',-->
  <!--                                            },-->
  <!--                                            { label: 'TPE', value: 'tpe' },-->
  <!--                                            {-->
  <!--                                                label: 'cheque',-->
  <!--                                                value: 'cheque',-->
  <!--                                            },-->
  <!--                                            {-->
  <!--                                                label: 'Western Union',-->
  <!--                                                value: 'Western Union',-->
  <!--                                            },-->
  <!--                                            {-->
  <!--                                                label: 'bank transfer',-->
  <!--                                                value: 'bank transfer',-->
  <!--                                            },-->
  <!--                                            { label: 'other', value: 'other' },-->
  <!--                                        ]"-->
  <!--                                    ></v-select>-->
  <!--                                    <v-form-invalid-feedback-->
  <!--                                        >{{ errors[0] }}-->
  <!--                                    </v-form-invalid-feedback>-->
  <!--                                </v-form-group>-->
  <!--                            </validation-provider>-->
  <!--                        </v-col>-->

  <!--                        &lt;!&ndash; Note &ndash;&gt;-->
  <!--                        <v-col lg="12" md="12" sm="12" class="mt-3">-->
  <!--                            <v-form-group-->
  <!--                                :label="$t('Please_provide_any_details')"-->
  <!--                            >-->
  <!--                                <v-form-textarea-->
  <!--                                    id="textarea"-->
  <!--                                    v-model="payment_return.notes"-->
  <!--                                    rows="3"-->
  <!--                                    max-rows="6"-->
  <!--                                ></v-form-textarea>-->
  <!--                            </v-form-group>-->
  <!--                        </v-col>-->

  <!--                        <v-col md="12" class="mt-3">-->
  <!--                            <v-btn-->
  <!--                                variant="primary"-->
  <!--                                type="submit"-->
  <!--                                :disabled="payment_return_Processing"-->
  <!--                                >{{ $t("submit") }}-->
  <!--                            </v-btn>-->
  <!--                            <div-->
  <!--                                v-once-->
  <!--                                class="typo__p"-->
  <!--                                v-if="payment_return_Processing"-->
  <!--                            >-->
  <!--                                <div-->
  <!--                                    class="spinner sm spinner-primary mt-3"-->
  <!--                                ></div>-->
  <!--                            </div>-->
  <!--                        </v-col>-->
  <!--                    </v-row>-->
  <!--                </v-form>-->
  <!--            </v-dialog>-->
  <!--        </validation-observer>-->

  <!--        &lt;!&ndash; Modal Show Customer_Invoice&ndash;&gt;-->
  <!--        <v-dialog-->
  <!--            hide-footer-->
  <!--            size="sm"-->
  <!--            scrollable-->
  <!--            id="Show_invoice"-->
  <!--            :title="$t('Customer_Credit_Note')"-->
  <!--        >-->
  <!--            <div id="invoice-POS">-->
  <!--                <div style="max-width: 400px; margin: 0px auto">-->
  <!--                    <div class="info">-->
  <!--                        <h2 class="text-center">-->
  <!--                            {{ company_info.CompanyName }}-->
  <!--                        </h2>-->

  <!--                        <p>-->
  <!--                            <span-->
  <!--                                >{{ $t("date") }} : {{ payment.date }} <br-->
  <!--                            /></span>-->
  <!--                            <span-->
  <!--                                >{{ $t("Adress") }} :-->
  <!--                                {{ company_info.CompanyAdress }} <br-->
  <!--                            /></span>-->
  <!--                            <span-->
  <!--                                >{{ $t("Phone") }} :-->
  <!--                                {{ company_info.CompanyPhone }} <br-->
  <!--                            /></span>-->
  <!--                            <span-->
  <!--                                >{{ $t("Customer") }} :-->
  <!--                                {{ payment.client_name }} <br-->
  <!--                            /></span>-->
  <!--                        </p>-->
  <!--                    </div>-->

  <!--                    <table class="change mt-3" style="font-size: 10px">-->
  <!--                        <thead>-->
  <!--                            <tr style="background: #eee">-->
  <!--                                <th style="text-align: left" colspan="1">-->
  <!--                                    {{ $t("PayeBy") }}:-->
  <!--                                </th>-->
  <!--                                <th style="text-align: center" colspan="2">-->
  <!--                                    {{ $t("Amount") }}:-->
  <!--                                </th>-->
  <!--                                <th style="text-align: right" colspan="1">-->
  <!--                                    {{ $t("Due") }}:-->
  <!--                                </th>-->
  <!--                            </tr>-->
  <!--                        </thead>-->

  <!--                        <tbody>-->
  <!--                            <tr>-->
  <!--                                <td style="text-align: left" colspan="1">-->
  <!--                                    {{ payment.Reglement }}-->
  <!--                                </td>-->
  <!--                                <td style="text-align: center" colspan="2">-->
  <!--                                    {{ formatNumber(payment.amount, 2) }}-->
  <!--                                </td>-->
  <!--                                <td style="text-align: right" colspan="1">-->
  <!--                                    {{-->
  <!--                                        formatNumber(-->
  <!--                                            payment.due - payment.amount,-->
  <!--                                            2-->
  <!--                                        )-->
  <!--                                    }}-->
  <!--                                </td>-->
  <!--                            </tr>-->
  <!--                        </tbody>-->
  <!--                    </table>-->
  <!--                </div>-->
  <!--            </div>-->
  <!--            <button @click="print_it()" class="btn btn-outline-primary">-->
  <!--                <i class="i-Billing"></i>-->
  <!--                {{ $t("print") }}-->
  <!--            </button>-->
  <!--        </v-dialog>-->

  <!--        &lt;!&ndash; Modal Show_invoice_return&ndash;&gt;-->
  <!--        <v-dialog-->
  <!--            hide-footer-->
  <!--            size="sm"-->
  <!--            scrollable-->
  <!--            id="Show_invoice_return"-->
  <!--            :title="$t('Sell_return_due')"-->
  <!--        >-->
  <!--            <div id="invoice-POS-return">-->
  <!--                <div style="max-width: 400px; margin: 0px auto">-->
  <!--                    <div class="info">-->
  <!--                        <h2 class="text-center">-->
  <!--                            {{ company_info.CompanyName }}-->
  <!--                        </h2>-->

  <!--                        <p>-->
  <!--                            <span-->
  <!--                                >{{ $t("date") }} : {{ payment_return.date }}-->
  <!--                                <br-->
  <!--                            /></span>-->
  <!--                            <span-->
  <!--                                >{{ $t("Adress") }} :-->
  <!--                                {{ company_info.CompanyAdress }} <br-->
  <!--                            /></span>-->
  <!--                            <span-->
  <!--                                >{{ $t("Phone") }} :-->
  <!--                                {{ company_info.CompanyPhone }} <br-->
  <!--                            /></span>-->
  <!--                            <span-->
  <!--                                >{{ $t("Customer") }} :-->
  <!--                                {{ payment_return.client_name }} <br-->
  <!--                            /></span>-->
  <!--                        </p>-->
  <!--                    </div>-->

  <!--                    <table class="change mt-3" style="font-size: 10px">-->
  <!--                        <thead>-->
  <!--                            <tr style="background: #eee">-->
  <!--                                <th style="text-align: left" colspan="1">-->
  <!--                                    {{ $t("PayeBy") }}:-->
  <!--                                </th>-->
  <!--                                <th style="text-align: center" colspan="2">-->
  <!--                                    {{ $t("Amount") }}:-->
  <!--                                </th>-->
  <!--                                <th style="text-align: right" colspan="1">-->
  <!--                                    {{ $t("Due") }}:-->
  <!--                                </th>-->
  <!--                            </tr>-->
  <!--                        </thead>-->

  <!--                        <tbody>-->
  <!--                            <tr>-->
  <!--                                <td style="text-align: left" colspan="1">-->
  <!--                                    {{ payment_return.Reglement }}-->
  <!--                                </td>-->
  <!--                                <td style="text-align: center" colspan="2">-->
  <!--                                    {{ formatNumber(payment_return.amount, 2) }}-->
  <!--                                </td>-->
  <!--                                <td style="text-align: right" colspan="1">-->
  <!--                                    {{-->
  <!--                                        formatNumber(-->
  <!--                                            payment_return.return_Due - -->
  <!--                                                payment_return.amount,-->
  <!--                                            2-->
  <!--                                        )-->
  <!--                                    }}-->
  <!--                                </td>-->
  <!--                            </tr>-->
  <!--                        </tbody>-->
  <!--                    </table>-->
  <!--                </div>-->
  <!--            </div>-->
  <!--            <button @click="print_return_due()" class="btn btn-outline-primary">-->
  <!--                <i class="i-Billing"></i>-->
  <!--                {{ $t("print") }}-->
  <!--            </button>-->
  <!--        </v-dialog>-->
</template>
