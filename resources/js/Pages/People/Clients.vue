<script setup>
import { onMounted, ref } from "vue";
import Layout from "@/Layouts/Authenticated.vue";
import ExportBtn from "@/Components/buttons/ExportBtn.vue";
import {api, globals, labels, rules} from "@/helpers";
import DeleteDialog from "@/Components/dialogs/DeleteDialog.vue";
import Snackbar from "@/Components/snackbar.vue";

const currency = globals.currency();

const props = defineProps({
    errors: Object,
});
//declare variable
 const clients = ref([]);
 const company_info  = ref({});
const form = ref(null);
const formPayDue = ref(null);
const search = ref("");
const loading = ref(false);
const snackbar = ref({
    view: false,
    color: "",
    text: "",
});
const editmode = ref(false);
const dialog = ref(false);
const dialogDelete = ref(false);
const dialogImport = ref(false);
const dialogPayDue = ref(false);
const dialogDetail = ref(false);
const dialogInvoice = ref(false);

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
const payment_codes = ref("");

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

const fields = ref([
    { title: labels.client.code, key: "code" },
    { title: labels.client.company_name, key: "company_name" },
    { title: labels.client.phone, key: "phone" },
    { title: labels.client.name, key: "name" },
    { title: labels.client.nit_ci, key: "nit_ci" },
    { title: "Deuda Total", key: "due" },
    // { title: "Deuda Total Devolucion", key: "return_Due" },
    { title: "Acciones", key: "actions" },
]);
const jsonFields = ref({
    Codigo: "code",
    Nombre_Factura: "name",
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
    snackbar.value.view = false;
    let file = e.target.files[0];
    let errorFilesize;

    if (file["size"] < 1048576) {
        // 1 mega = 1,048,576 Bytes
        errorFilesize = false;
    } else {
        snackbar.value.view = true;
        snackbar.value.color = "error";
        snackbar.value.text = error.response.data.message;
    }

    if (errorFilesize === false) {
        import_clients.value = file;
    }
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
    api.post({
        url: "/clients",
        params: {
            name: client.value.name,
            company_name: client.value.company_name,
            email: client.value.email,
            phone: client.value.phone,
            nit_ci: client.value.nit_ci,
            country: client.value.country,
            city: client.value.city,
            adresse: client.value.adresse,
        },
        loadingItem: loading,
        snackbar,
        onSuccess: () => {
            dialog.value = false;
        },
    });
}

//----------------------------------- Update Client -------------------------------\\
function Update_Client() {
    api.put({
        url: "/clients/" + client.value.id,
        params: {
            name: client.value.name,
            company_name: client.value.company_name,
            email: client.value.email,
            phone: client.value.phone,
            nit_ci: client.value.nit_ci,
            country: client.value.country,
            city: client.value.city,
            adresse: client.value.adresse,
        },
        loadingItem: loading,
        snackbar,
        onSuccess: () => {
            dialog.value = false;
        },
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
    api.delete({
        url: "/clients/" + client.value.id,
        loadingItem: loading,
        snackbar,
        onSuccess: () => {
            dialogDelete.value = false;
        },
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
    snackbar.value.view = false;
    const validate = await formPayDue.value.validate();
    if (!validate.valid) {
        snackbar.value.view = true;
        snackbar.value.color = "error";
        snackbar.value.text = labels.no_fill_data;
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
        snackbar.value.view = true;
        snackbar.value.color = "warning";
        snackbar.value.text = labels.payment_mayor_due;
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
    dialogPayDue.value = true;
    dialogInvoice.value = false;
}

//---------------------------------------- Submit_Pay_due-------------------------------\\
function Submit_Pay_due() {
    payment_codes.value = "";
    api.post({
        url: "/clients_pay_due",
        params: {
            client_id: payment.value.client_id,
            amount: payment.value.amount,
            notes: payment.value.notes,
            Reglement: payment.value.Reglement,
        },
        loadingItem: loading,
        snackbar,
        onSuccess: (data) => {
            payment_codes.value = data.payment_codes;
            dialogPayDue.value = false;
            dialogInvoice.value = true;
        },
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
//         snackbar.value.color = "error";
//         snackbar.value.text = "Por favor llene correctamente los campos";
//     } else if (payment.value.amount > payment.value.due) {
//         snackbar.value = true;
//         snackbar.value.color = "error";
//         snackbar.value.text = "el monto es mayor a la deuda";
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
// function Pay_return_due(row) {
// this.reset_Form_payment_return_due();
// this.payment_return.client_id = row.id;
// this.payment_return.client_name = row.name;
// this.payment_return.return_Due = row.return_Due;
// this.payment_return.date = new Date().toISOString().slice(0, 10);
// setTimeout(() => {
//     this.$bvModal.show("modal_Pay_return_due");
// }, 500);
// }

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
//             snackbar.value.color = "success";
//             snackbar.value.text = labels.success_message;
//             router.reload({
//           preserveState: true,
//           preserveScroll: true,
//         });
//             dialog.value = false;
//         })
//         .catch((error) => {
//             console.log(error);
//             snackbar.value = true;
//             snackbar.value.color = "error";
//             snackbar.value.text = error;
//         })
//         .finally(() => {
//             loading.value = false;
//         });
// }
function loadData() {
    api.get({
        url: "/clients/list",
        snackbar,
        loadingItem: loading,
        onSuccess: (data) => {
            clients.value = data.clients;
            company_info.value = data.company_info;
        },
    });
}

onMounted(() => {
    loadData();
});
</script>

<template>
    <layout>
      <snackbar
            v-model="snackbar.view"
            :text="snackbar.text"
            :color="snackbar.color"
        ></snackbar>
        <!-- Modal Show Import Clients -->
        <!--        <v-dialog v-model="dialogImport" max-width="600px" scrollable>
                <v-card>
                    <v-toolbar border  title="Importar Clientes">
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
                                    <v-table >
                                        <tbody>
                                            <tr>
                                                <td>Nombre</td>
                                                <th>
                                                    <v-btn
                                                        variant="outlined"

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
                <v-toolbar border :title="labels.client.detail"></v-toolbar>
                <v-card-text>
                    <v-table hover>
                        <tbody>
                            <tr>
                                <!-- Customer Code -->
                                <td>{{ labels.client.code }}</td>
                                <td class="font-weight-bold">
                                    {{ client.code }}
                                </td>
                            </tr>
                            <tr>
                                <!-- Customer Company Name -->
                                <td>{{ labels.client.company_name }}</td>
                                <td class="font-weight-bold">
                                    {{ client.company_name }}
                                </td>
                            </tr>
                            <tr>
                                <!-- Customer Name -->
                                <td>{{ labels.client.name }}</td>
                                <td class="font-weight-bold">
                                    {{ client.name }}
                                </td>
                            </tr>
                            <tr>
                                <!-- Customer Phone -->
                                <td>{{ labels.client.phone }}</td>
                                <td class="font-weight-bold">
                                    {{ client.phone }}
                                </td>
                            </tr>
                            <tr>
                                <!-- Customer Email -->
                                <td>{{ labels.client.email }}</td>
                                <td class="font-weight-bold">
                                    {{ client.email }}
                                </td>
                            </tr>
                            <tr>
                                <!-- Customer City -->
                                <td>{{ labels.client.city }}</td>
                                <td class="font-weight-bold">
                                    {{ client.city }}
                                </td>
                            </tr>
                            <tr>
                                <!-- Customer Adress -->
                                <td>{{ labels.client.adresse }}</td>
                                <td class="font-weight-bold">
                                    {{ client.adresse }}
                                </td>
                            </tr>
                            <tr>
                                <!-- Tax Number -->
                                <td>{{ labels.client.nit_ci }}</td>
                                <td class="font-weight-bold">
                                    {{ client.nit_ci }}
                                </td>
                            </tr>

                            <tr>
                                <!-- Total_Sale_Due -->
                                <td>{{ labels.total_sale_due }}</td>
                                <td class="font-weight-bold">
                                    {{ currency }}
                                    {{ client.due }}
                                </td>
                            </tr>

                            <!--                            <tr>-->
                            <!--                                &lt;!&ndash; Total_Sell_Return_Due &ndash;&gt;-->
                            <!--                                <td>Total Deuda de Devolucion</td>-->
                            <!--                                <td>-->
                            <!--                                    {{ currency }}-->
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
                        {{ labels.close }}
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
                    border
                    :title="(editmode ? 'Editar ' : 'Añadir ') + 'Cliente'"
                ></v-toolbar>
                <v-form @submit.prevent="onSave" ref="form">
                    <v-card-text>
                        <v-row>
                            <!-- Customer Name -->
                            <v-col cols="12" md="6">
                                <v-text-field
                                    :label="labels.client.name + ' *'"
                                    v-model="client.name"
                                    :placeholder="labels.client.name"
                                    :rules="rules.required"
                                    hide-details="auto"
                                >
                                </v-text-field>
                            </v-col>

                            <!-- Customer Company_name -->
                            <v-col cols="12" md="6">
                                <v-text-field
                                    :label="labels.client.company_name + ' *'"
                                    v-model="client.company_name"
                                    :placeholder="labels.client.company_name"
                                    :rules="rules.required"
                                    hide-details="auto"
                                >
                                </v-text-field>
                            </v-col>

                            <!-- Customer Email -->
                            <v-col cols="12" md="6">
                                <v-text-field
                                    :label="labels.client.email"
                                    v-model="client.email"
                                    :placeholder="labels.client.email"
                                    hide-details="auto"
                                >
                                </v-text-field>
                            </v-col>

                            <!-- Customer Phone -->
                            <v-col cols="12" md="6">
                                <v-text-field
                                    :label="labels.client.phone"
                                    v-model="client.phone"
                                    :placeholder="labels.client.phone"
                                    hide-details="auto"
                                >
                                </v-text-field>
                            </v-col>

                            <!-- Customer City -->
                            <v-col cols="12" md="6">
                                <v-text-field
                                    :label="labels.client.city"
                                    v-model="client.city"
                                    :placeholder="labels.client.city"
                                    hide-details="auto"
                                >
                                </v-text-field>
                            </v-col>

                            <!-- Customer Tax Number -->
                            <v-col cols="12" md="6">
                                <v-text-field
                                    :label="labels.client.nit_ci"
                                    v-model="client.nit_ci"
                                    :placeholder="labels.client.nit_ci"
                                    hide-details="auto"
                                ></v-text-field>
                            </v-col>

                            <!-- Customer Adress -->
                            <v-col md="12" sm="12">
                                <v-textarea
                                    rows="4"
                                    :label="labels.client.adresse"
                                    v-model="client.adresse"
                                    :placeholder="labels.client.adresse"
                                    hide-details="auto"
                                ></v-textarea>
                            </v-col>
                        </v-row>
                    </v-card-text>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn
                            variant="outlined"
                            color="error"
                            class="ma-1"
                            @click="onClose"
                        >
                            {{ labels.cancel }}
                        </v-btn>
                        <v-btn
                            color="primary"
                            variant="flat"
                            class="ma-1"
                            type="submit"
                            :loading="loading"
                            :disabled="loading"
                        >
                            {{ labels.submit }}
                        </v-btn>
                    </v-card-actions>
                </v-form>
            </v-card>
        </v-dialog>
        <!-- Modal Remove Customer -->
        <delete-dialog
            v-model="dialogDelete"
            :on-save="Remove_Client"
            :on-close="onCloseDelete"
        ></delete-dialog>
        <v-row align="center">
            <v-col cols="12" sm="6">
                <v-text-field
                    v-model="search"
                    prepend-icon="fas fa-search"
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
                  v-if="globals.userPermision(['Customers_add'])"
                    color="primary"
                    class="ma-1"
                    prepend-icon="fas fa-user-plus"
                    @click="New_Client"
                >
                    {{ labels.add }}
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
                    :no-data-text="labels.no_data_table"
                    :loading="loading"
                >
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
                            <v-list>
                                <v-list-item

                                    v-if="globals.userPermision(['Customers_view']) && item.due > 0"
                                    @click="Pay_due(item)"
                                    prepend-icon="fas fa-dollar-sign"
                                >
                                    <v-list-item-title>
                                        Pagar todas la deudas a la vez
                                    </v-list-item-title>
                                </v-list-item>

                                <!--                                <v-list-item-->
                                <!--                                    v-if="item.return_Due > 0"-->
                                <!--                                    @click="Pay_return_due(item)"-->
                                <!--                                    prepend-icon="mdi-currency-usd"-->

                                <!--                                >-->
                                <!--                                    <v-list-item-title>-->
                                <!--                                    Pagar todas la deudas de devolucion a la vez-->
                                <!--                                    </v-list-item-title>-->
                                <!--                                </v-list-item>-->

                                <v-list-item
                                 v-if="globals.userPermision(['Customers_view'])"
                                    @click="showDetails(item)"
                                    prepend-icon="fas fa-eye"
                                >
                                    <v-list-item-title>
                                        Detalles del cliente
                                    </v-list-item-title>
                                </v-list-item>

                                <v-list-item
                                 v-if="globals.userPermision(['Customers_edit'])"
                                    @click="Edit_Client(item)"
                                    prepend-icon="fas fa-pen"
                                >
                                    <v-list-item-title>
                                        Editar Cliente
                                    </v-list-item-title>
                                </v-list-item>

                                <v-list-item
                                 v-if="globals.userPermision(['Customers_delete'])"
                                    @click="Delete_Client(item)"
                                    prepend-icon="fas fa-trash"
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

    <!-- Modal Pay_due-->
    <v-dialog
        v-model="dialogPayDue"
        max-width="600px"
        scrollable
        @update:modelValue="
            dialogPayDue === false ? reset_Form_payment() : dialogPayDue
        "
    >
        <v-card>
            <v-toolbar border title="Pagar Deuda"></v-toolbar>
            <v-form @submit.prevent="Submit_Payment_sell_due" ref="formPayDue">
                <v-card-text>
                    <v-row>
                        <!-- Paying Amount  -->
                        <v-col md="6" cols="12">
                            <v-text-field
                                :label="labels.payment.amount + ' *'"
                                v-model="payment.amount"
                                :placeholder="labels.payment.amount"
                                :rules="
                                    rules.required.concat(
                                        rules.numberWithDecimal
                                    )
                                "
                                hide-details="auto"
                            >
                            </v-text-field>
                        </v-col>

                        <!-- Payment choice -->
                        <v-col md="6" cols="12">
                            <v-select
                                v-model="payment.Reglement"
                                hide-details="auto"
                                :items="helpers.reglamentPayment()"
                                label="Tipo de Pago"
                                :rules="rules.required"
                            ></v-select>
                        </v-col>

                        <!-- Note -->
                        <v-col cols="12">
                            <v-textarea
                                v-model="payment.notes"
                                rows="4"
                                variant="outlined"
                                :label="labels.payment.notes"
                                :placeholder="labels.payment.notes"
                                hide-details="auto"
                            ></v-textarea>
                        </v-col>
                    </v-row>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn
                        variant="outlined"
                        color="error"
                        class="ma-1"
                        @click="dialogPayDue = false"
                    >
                        {{ labels.cancel }}
                    </v-btn>
                    <v-btn
                        color="primary"
                        variant="elevated"
                        type="submit"
                        :disabled="paymentProcessing"
                        >{{ labels.submit }}
                    </v-btn>
                </v-card-actions>
            </v-form>
        </v-card>
    </v-dialog>

    <!--        &lt;!&ndash; Modal Pay_return_Due&ndash;&gt;-->
    <!--        <validation-o{{ currency }}erver ref="ref_pay_return_due">-->
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
    <!--        </validation-o{{ currency }}erver>-->

    <!-- Modal Show Customer_Invoice-->
    <v-dialog :model-value="dialogInvoice" max-width="400">
        <v-card>
            <v-card-text>
                <div id="invoice-POS">
                    <div class="info">
                        <h2 class="text-center">
                            {{ company_info?.CompanyName }}
                        </h2>

                        <p>
                            <span>Fecha : {{ payment.date }} <br /></span>
                            <span
                                >Cliente : {{ payment.client_name }} <br
                            /></span>
                        </p>
                    </div>

                    <v-table hover>
                        <thead>
                            <tr style="background: #eee">
                                <th style="text-align: left" colspan="1">
                                    Pago de:
                                </th>
                                <th style="text-align: left" colspan="1">
                                    Pago en:
                                </th>
                                <th style="text-align: center" colspan="1">
                                    {{ labels.sale.paid_amount }}:
                                </th>
                                <th style="text-align: right" colspan="1">
                                    {{ labels.sale.due }}:
                                </th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr>
                                <td style="text-align: left" colspan="1">
                                    <div
                                        v-html="helpers.newLine(payment_codes)"
                                    ></div>
                                </td>
                                <td style="text-align: left" colspan="1">
                                    {{
                                        helpers.getReglamentPayment(
                                            payment.Reglement
                                        )[0].title
                                    }}
                                </td>
                                <td style="text-align: center" colspan="1">
                                    {{ helpers.formatNumber(payment.amount, 2) }}
                                </td>
                                <td style="text-align: right" colspan="1">
                                    {{ helpers.formatNumber(payment.due, 2) }}
                                </td>
                            </tr>
                        </tbody>
                    </v-table>
                    <table class="change mt-2" style="font-size: 12px">
                        <thead>
                            <tr class="total">
                                <th>Notas</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    {{ payment.notes }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </v-card-text>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn
                    prepend-icon="fas fa-print"
                    @click="
                        helpers.print_pdf('invoice-POS', payment.client_name)
                    "
                    color="primary"
                    variant="outlined"
                >
                    {{ labels.print }} PDF
                </v-btn>
                <v-btn
                    prepend-icon="fas fa-print"
                    @click="helpers.print_pos('invoice-POS')"
                    color="primary"
                    variant="outlined"
                >
                    {{ labels.print }}
                </v-btn>
                <v-spacer></v-spacer>
            </v-card-actions>
        </v-card>
    </v-dialog>

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
</template>
