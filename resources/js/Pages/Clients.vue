<script setup>
import { ref } from "vue";
import Layout from "@/Layouts/Authenticated.vue";
import JsonExcel from "vue-json-excel3";
import ruleForm from "@/rules";
import { router } from "@inertiajs/vue3";

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

const SubmitProcessing = ref(false);
const ImportProcessing = ref(false);
const paymentProcessing = ref(false);
const payment_return_Processing = ref(false);

const showDropdown = ref(false);
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
const company_info = ref({});
const selectedIds = ref([]);

const import_clients = ref("");
const client = ref({
    id: "",
    name: "",
    code: "",
    email: "",
    phone: "",
    country: "",
    city: "",
    adresse: "",
    tax_number: "",
});

const fields = ref([
    { title: "Codigo", key: "code" },
    { title: "Nombre", key: "name" },
    { title: "Telefono", key: "phone" },
    { title: "NIT", key: "tax_number" },
    { title: "Deuda Total", key: "due" },
    { title: "Deuda Total Devolucion", key: "return_Due" },
    { title: "Acciones", key: "actions" },
]);
const jsonFields = ref({
    Codigo: "code",
    Nombre: "name",
    Telefono: "phone",
    Nit: "tax_number",
    Correo: "email",
    Deuda_Total: "due",
    Deuda_Total_Devolucion: "return_Due",
});

//---------------------- modal  ------------------------------\\
async function onSave() {
    const validate = await form.value.validate();
    if (validate.valid)
        if (!editmode.value) {
            Create_Warehouse();
        } else {
            Update_Warehouse();
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
        .then(({ data }) => {
            snackbar.value = true;
            snackbarColor.value = "success";
            snackbarText.value = "Proceso exitoso";
            router.reload();
            dialog.value = false;
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
    axios
        .post("clients", {
            name: client.value.name,
            email: client.value.email,
            phone: client.value.phone,
            tax_number: client.value.tax_number,
            country: client.value.country,
            city: client.value.city,
            adresse: client.value.adresse,
        })
        .then(({ data }) => {
            snackbar.value = true;
            snackbarColor.value = "success";
            snackbarText.value = "Proceso exitoso";
            router.reload();
            dialog.value = false;
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

//----------------------------------- Update Client -------------------------------\\
function Update_Client() {
    loading.value = true;
    axios
        .put("clients/" + client.value.id, {
            name: client.value.name,
            email: client.value.email,
            phone: client.value.phone,
            tax_number: client.value.tax_number,
            country: client.value.country,
            city: client.value.city,
            adresse: client.value.adresse,
        })
        .then(({ data }) => {
            snackbar.value = true;
            snackbarColor.value = "success";
            snackbarText.value = "Proceso exitoso";
            router.reload();
            dialog.value = false;
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

//-------------------------------- Reset Form -------------------------------\\
function reset_Form() {
    client.value = {
        id: "",
        name: "",
        email: "",
        phone: "",
        country: "",
        tax_number: "",
        city: "",
        adresse: "",
    };
}

//------------------------------- Remove Client -------------------------------\\
function Remove_Client(id) {
    loading.value = true;
    snackbar.value = false;
    axios
        .delete("clients/" + id)
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
            loading.value = false;
        });
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
    axios
        .post("clients_pay_due", {
            client_id: payment.value.client_id,
            amount: payment.value.amount,
            notes: payment.value.notes,
            Reglement: payment.value.Reglement,
        })
        .then(({ data }) => {
            snackbar.value = true;
            snackbarColor.value = "success";
            snackbarText.value = "Proceso exitoso";
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
            loading.value = false;
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
//             router.reload();
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

//------------------------------Formetted Numbers -------------------------\\
function formatNumber(number, dec) {
    const value = (
        typeof number === "string" ? number : number.toString()
    ).split(".");
    if (dec <= 0) return value[0];
    let formated = value[1] || "";
    if (formated.length > dec) return `${value[0]}.${formated.substr(0, dec)}`;
    while (formated.length < dec) formated += "0";
    return `${value[0]}.${formated}`;
}
function consol(item) {
    console.log(item);
}
</script>

<template>
    <layout>
        <v-snackbar
            v-model="snackbar"
            :color="snackbarColor"
            :location="'top right'"
            :timeout="5000"
            elevation="5"
        >
            {{ snackbarText }}
            <template v-slot:actions>
                <v-btn
                    @click="snackbar = false"
                    icon="mdi-close"
                    size="x-small"
                    variant="tonal"
                >
                </v-btn>
            </template>
        </v-snackbar>
        <!-- Modal Show Import Clients -->
        <v-dialog v-model="dialogImport" max-width="600px" scrollable>
            <v-card>
                <v-toolbar
                    border
                    density="comfortable"
                    title="Importar Clientes"
                >
                </v-toolbar>
                <v-card-text>
                    <v-form
                        @submit.prevent="Submit_import"
                        enctype="multipart/form-data"
                    >
                        <v-row>
                            <!-- File -->
                            <v-col md="12" sm="12" class="mb-3">
                                <v-text-field
                                    label="Elige el archivo"
                                    variant="outlined"
                                    density="comfortable"
                                    hide-details="auto"
                                    type="file"
                                    @change="onFileSelected"
                                >
                                </v-text-field>
                            </v-col>

                            <v-col md="6" sm="12">
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

                            <v-col md="6" sm="12">
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
                                                    >campo requerido</v-btn
                                                >
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
        </v-dialog>
        <v-row align="center">
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
                <v-btn
                    size="small"
                    class="ma-1"
                    variant="outlined"
                    color="error"
                    prepend-icon="mdi-file-excel-box"
                >
                    <json-excel
                        :data="props.clients"
                        :fields="jsonFields"
                        worksheet="Clientes"
                        name="clientes.xls"
                    >
                        Exportar
                    </json-excel>
                </v-btn>
                <v-btn
                    @click="Show_import_clients()"
                    size="small"
                    class="ma-1"
                    color="info"
                    variant="elevated"
                    prepend-icon="mdi-download"
                >
                    Importar
                </v-btn>
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
                <v-skeleton-loader :loading="loading" boilerplate type="table">
                    <v-data-table
                        :headers="fields"
                        :items="clients"
                        :search="search"
                        class="elevation-2"
                        density="compact"
                        no-data-text="No existen datos a mostrar"
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
                                <v-list nav density="compact">
                                    <v-list-item
                                        v-if="item.raw.due > 0"
                                        @click="Pay_due(item.raw)"
                                        prepend-icon="mdi-dollar"
                                    >
                                        Pagar todas la deudas a la vez
                                    </v-list-item>

                                    <v-list-item
                                        v-if="item.raw.return_Due > 0"
                                        @click="Pay_return_due(item.raw)"
                                        prepend-icon="mdi-dollar"
                                    >
                                        Pagar todas la deudas de devolucion a la
                                        vez
                                    </v-list-item>

                                    <v-list-item
                                        @click="showDetails(item.raw)"
                                        prepend-icon="mdi-eye"
                                    >
                                        Detalles del cliente
                                    </v-list-item>

                                    <v-list-item
                                        @click="Edit_Client(item.raw)"
                                        prepend-icon="mdi-pencil"
                                    >
                                        Editar Cliente
                                    </v-list-item>

                                    <v-list-item
                                        @click="Remove_Client(item.raw)"
                                        prepend-icon="mdi-delete"
                                    >
                                        Eliminar Cliente
                                    </v-list-item>
                                </v-list>
                            </v-menu>
                        </template>
                    </v-data-table>
                </v-skeleton-loader>
            </v-col>
        </v-row>
    </layout>
    <!--    <div class="main-content">-->
    <!--        <div-->
    <!--            v-if="isLoading"-->
    <!--            class="loading_page spinner spinner-primary mr-3"-->
    <!--        ></div>-->
    <!--        <div v-else>-->
    <!--            <vue-good-table-->
    <!--                mode="remote"-->
    <!--                :columns="columns"-->
    <!--                :totalRows="totalRows"-->
    <!--                :rows="clients"-->
    <!--                @on-page-change="onPageChange"-->
    <!--                @on-per-page-change="onPerPageChange"-->
    <!--                @on-sort-change="onSortChange"-->
    <!--                @on-search="onSearch"-->
    <!--                :search-options="{-->
    <!--                    enabled: true,-->
    <!--                    placeholder: $t('Search_this_table'),-->
    <!--                }"-->
    <!--                :select-options="{-->
    <!--                    enabled: true,-->
    <!--                    clearSelectionText: '',-->
    <!--                }"-->
    <!--                @on-selected-rows-change="selectionChanged"-->
    <!--                :pagination-options="{-->
    <!--                    enabled: true,-->
    <!--                    mode: 'records',-->
    <!--                    nextLabel: 'next',-->
    <!--                    prevLabel: 'prev',-->
    <!--                }"-->
    <!--                :styleClass="-->
    <!--                    showDropdown-->
    <!--                        ? 'tableOne table-hover vgt-table full-height'-->
    <!--                        : 'tableOne table-hover vgt-table non-height'-->
    <!--                "-->
    <!--            >-->
    <!--                <div slot="table-actions" class="mt-2 mb-3">-->
    <!--                    <v-btn-->
    <!--                        variant="outline-info m-1"-->
    <!--                        size="sm"-->
    <!--                        v-b-toggle.sidebar-right-->
    <!--                    >-->
    <!--                        <i class="i-Filter-2"></i>-->
    <!--                        {{ $t("Filter") }}-->
    <!--                    </v-btn>-->
    <!--                    <v-btn-->
    <!--                        @click="clients_PDF()"-->
    <!--                        size="sm"-->
    <!--                        variant="outline-success m-1"-->
    <!--                    >-->
    <!--                        <i class="i-File-Copy"></i> PDF-->
    <!--                    </v-btn>-->
    <!--                    <vue-excel-xlsx-->
    <!--                        class="btn btn-sm btn-outline-danger ripple m-1"-->
    <!--                        :data="clients"-->
    <!--                        :columns="columns"-->
    <!--                        :file-name="'clients'"-->
    <!--                        :file-type="'xlsx'"-->
    <!--                        :sheet-name="'clients'"-->
    <!--                    >-->
    <!--                        <i class="i-File-Excel"></i> EXCEL-->
    <!--                    </vue-excel-xlsx>-->

    <!--                </div>-->

    <!--                <template slot="table-row" slot-scope="props">-->
    <!--                    <span v-if="props.column.field == 'actions'">-->
    <!--                        <div>-->
    <!--                            <b-dropdown-->
    <!--                                id="dropdown-right"-->
    <!--                                variant="link"-->
    <!--                                text="right align"-->
    <!--                                toggle-class="text-decoration-none"-->
    <!--                                size="lg"-->
    <!--                                right-->
    <!--                                no-caret-->
    <!--                            >-->
    <!--                                <template-->
    <!--                                    v-slot:button-content-->
    <!--                                    class="_r_btn border-0"-->
    <!--                                >-->
    <!--                                    <span-->
    <!--                                        class="_dot _r_block-dot bg-dark"-->
    <!--                                    ></span>-->
    <!--                                    <span-->
    <!--                                        class="_dot _r_block-dot bg-dark"-->
    <!--                                    ></span>-->
    <!--                                    <span-->
    <!--                                        class="_dot _r_block-dot bg-dark"-->
    <!--                                    ></span>-->
    <!--                                </template>-->

    <!--                            </b-dropdown>-->
    <!--                        </div>-->
    <!--                    </span>-->
    <!--                </template>-->
    <!--            </vue-good-table>-->
    <!--        </div>-->

    <!--        &lt;!&ndash; Multiple filters &ndash;&gt;-->
    <!--        <b-sidebar-->
    <!--            id="sidebar-right"-->
    <!--            :title="$t('Filter')"-->
    <!--            bg-variant="white"-->
    <!--            right-->
    <!--            shadow-->
    <!--        >-->
    <!--            <div class="px-3 py-2">-->
    <!--                <v-row>-->
    <!--                    &lt;!&ndash; Code Customer   &ndash;&gt;-->
    <!--                    <v-col md="12">-->
    <!--                        <v-form-group :label="$t('CustomerCode')">-->
    <!--                            <v-form-input-->
    <!--                                label="Code"-->
    <!--                                :placeholder="$t('SearchByCode')"-->
    <!--                                v-model="Filter_Code"-->
    <!--                            ></v-form-input>-->
    <!--                        </v-form-group>-->
    <!--                    </v-col>-->

    <!--                    &lt;!&ndash; Name Customer   &ndash;&gt;-->
    <!--                    <v-col md="12">-->
    <!--                        <v-form-group :label="$t('CustomerName')">-->
    <!--                            <v-form-input-->
    <!--                                label="Name"-->
    <!--                                :placeholder="$t('SearchByName')"-->
    <!--                                v-model="Filter_Name"-->
    <!--                            ></v-form-input>-->
    <!--                        </v-form-group>-->
    <!--                    </v-col>-->

    <!--                    &lt;!&ndash; Phone Customer   &ndash;&gt;-->
    <!--                    <v-col md="12">-->
    <!--                        <v-form-group :label="$t('Phone')">-->
    <!--                            <v-form-input-->
    <!--                                label="Phone"-->
    <!--                                :placeholder="$t('SearchByPhone')"-->
    <!--                                v-model="Filter_Phone"-->
    <!--                            ></v-form-input>-->
    <!--                        </v-form-group>-->
    <!--                    </v-col>-->

    <!--                    &lt;!&ndash; Email Customer   &ndash;&gt;-->
    <!--                    <v-col md="12">-->
    <!--                        <v-form-group :label="$t('Email')">-->
    <!--                            <v-form-input-->
    <!--                                label="Email"-->
    <!--                                :placeholder="$t('SearchByEmail')"-->
    <!--                                v-model="Filter_Email"-->
    <!--                            ></v-form-input>-->
    <!--                        </v-form-group>-->
    <!--                    </v-col>-->

    <!--                    <v-col md="6" sm="12">-->
    <!--                        <v-btn-->
    <!--                            @click="Get_Clients(serverParams.page)"-->
    <!--                            variant="primary m-1"-->
    <!--                            size="sm"-->
    <!--                            block-->
    <!--                        >-->
    <!--                            <i class="i-Filter-2"></i>-->
    <!--                            {{ $t("Filter") }}-->
    <!--                        </v-btn>-->
    <!--                    </v-col>-->
    <!--                    <v-col md="6" sm="12">-->
    <!--                        <v-btn-->
    <!--                            @click="Reset_Filter()"-->
    <!--                            variant="danger m-1"-->
    <!--                            size="sm"-->
    <!--                            block-->
    <!--                        >-->
    <!--                            <i class="i-Power-2"></i>-->
    <!--                            {{ $t("Reset") }}-->
    <!--                        </v-btn>-->
    <!--                    </v-col>-->
    <!--                </v-row>-->
    <!--            </div>-->
    <!--        </b-sidebar>-->

    <!--        &lt;!&ndash; Modal Pay_due&ndash;&gt;-->
    <!--        <validation-observer ref="ref_pay_due">-->
    <!--            <b-modal hide-footer size="md" id="modal_Pay_due" title="Pay Due">-->
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
    <!--            </b-modal>-->
    <!--        </validation-observer>-->

    <!--        &lt;!&ndash; Modal Pay_return_Due&ndash;&gt;-->
    <!--        <validation-observer ref="ref_pay_return_due">-->
    <!--            <b-modal-->
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
    <!--            </b-modal>-->
    <!--        </validation-observer>-->

    <!--        &lt;!&ndash; Modal Show Customer_Invoice&ndash;&gt;-->
    <!--        <b-modal-->
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
    <!--        </b-modal>-->

    <!--        &lt;!&ndash; Modal Show_invoice_return&ndash;&gt;-->
    <!--        <b-modal-->
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
    <!--        </b-modal>-->

    <!--        &lt;!&ndash; Modal Create & Edit Customer &ndash;&gt;-->
    <!--        <validation-observer ref="Create_Customer">-->
    <!--            <b-modal-->
    <!--                hide-footer-->
    <!--                size="lg"-->
    <!--                id="New_Customer"-->
    <!--                :title="editmode ? $t('Edit') : $t('Add')"-->
    <!--            >-->
    <!--                <v-form @submit.prevent="Submit_Customer">-->
    <!--                    <v-row>-->
    <!--                        &lt;!&ndash; Customer Name &ndash;&gt;-->
    <!--                        <v-col md="6" sm="12">-->
    <!--                            <validation-provider-->
    <!--                                name="Name Customer"-->
    <!--                                :rules="{ required: true }"-->
    <!--                                v-slot="validationContext"-->
    <!--                            >-->
    <!--                                <v-form-group-->
    <!--                                    :label="$t('CustomerName') + ' ' + '*'"-->
    <!--                                >-->
    <!--                                    <v-form-input-->
    <!--                                        :state="-->
    <!--                                            getValidationState(-->
    <!--                                                validationContext-->
    <!--                                            )-->
    <!--                                        "-->
    <!--                                        aria-describedby="name-feedback"-->
    <!--                                        label="name"-->
    <!--                                        :placeholder="$t('CustomerName')"-->
    <!--                                        v-model="client.name"-->
    <!--                                    ></v-form-input>-->
    <!--                                    <v-form-invalid-feedback id="name-feedback"-->
    <!--                                        >{{ validationContext.errors[0] }}-->
    <!--                                    </v-form-invalid-feedback>-->
    <!--                                </v-form-group>-->
    <!--                            </validation-provider>-->
    <!--                        </v-col>-->

    <!--                        &lt;!&ndash; Customer Email &ndash;&gt;-->
    <!--                        <v-col md="6" sm="12">-->
    <!--                            <v-form-group :label="$t('Email')">-->
    <!--                                <v-form-input-->
    <!--                                    label="email"-->
    <!--                                    v-model="client.email"-->
    <!--                                    :placeholder="$t('Email')"-->
    <!--                                ></v-form-input>-->
    <!--                            </v-form-group>-->
    <!--                        </v-col>-->

    <!--                        &lt;!&ndash; Customer Phone &ndash;&gt;-->
    <!--                        <v-col md="6" sm="12">-->
    <!--                            <v-form-group :label="$t('Phone')">-->
    <!--                                <v-form-input-->
    <!--                                    label="Phone"-->
    <!--                                    v-model="client.phone"-->
    <!--                                    :placeholder="$t('Phone')"-->
    <!--                                ></v-form-input>-->
    <!--                            </v-form-group>-->
    <!--                        </v-col>-->

    <!--                        &lt;!&ndash; Customer Country &ndash;&gt;-->
    <!--                        <v-col md="6" sm="12">-->
    <!--                            <v-form-group :label="$t('Country')">-->
    <!--                                <v-form-input-->
    <!--                                    label="Country"-->
    <!--                                    v-model="client.country"-->
    <!--                                    :placeholder="$t('Country')"-->
    <!--                                ></v-form-input>-->
    <!--                            </v-form-group>-->
    <!--                        </v-col>-->

    <!--                        &lt;!&ndash; Customer City &ndash;&gt;-->
    <!--                        <v-col md="6" sm="12">-->
    <!--                            <v-form-group :label="$t('City')">-->
    <!--                                <v-form-input-->
    <!--                                    label="City"-->
    <!--                                    v-model="client.city"-->
    <!--                                    :placeholder="$t('City')"-->
    <!--                                ></v-form-input>-->
    <!--                            </v-form-group>-->
    <!--                        </v-col>-->

    <!--                        &lt;!&ndash; Customer Tax Number &ndash;&gt;-->
    <!--                        <v-col md="6" sm="12">-->
    <!--                            <v-form-group :label="$t('Tax_Number')">-->
    <!--                                <v-form-input-->
    <!--                                    label="Tax Number"-->
    <!--                                    v-model="client.tax_number"-->
    <!--                                    :placeholder="$t('Tax_Number')"-->
    <!--                                ></v-form-input>-->
    <!--                            </v-form-group>-->
    <!--                        </v-col>-->

    <!--                        &lt;!&ndash; Customer Adress &ndash;&gt;-->
    <!--                        <v-col md="12" sm="12">-->
    <!--                            <v-form-group :label="$t('Adress')">-->
    <!--                                <textarea-->
    <!--                                    label="Adress"-->
    <!--                                    class="form-control"-->
    <!--                                    rows="4"-->
    <!--                                    v-model="client.adresse"-->
    <!--                                    :placeholder="$t('Adress')"-->
    <!--                                ></textarea>-->
    <!--                            </v-form-group>-->
    <!--                        </v-col>-->

    <!--                        <v-col md="12" class="mt-3">-->
    <!--                            <v-btn-->
    <!--                                variant="primary"-->
    <!--                                type="submit"-->
    <!--                                :disabled="SubmitProcessing"-->
    <!--                                >{{ $t("submit") }}-->
    <!--                            </v-btn>-->
    <!--                            <div v-once class="typo__p" v-if="SubmitProcessing">-->
    <!--                                <div-->
    <!--                                    class="spinner sm spinner-primary mt-3"-->
    <!--                                ></div>-->
    <!--                            </div>-->
    <!--                        </v-col>-->
    <!--                    </v-row>-->
    <!--                </v-form>-->
    <!--            </b-modal>-->
    <!--        </validation-observer>-->

    <!--        &lt;!&ndash; Modal Show Customer Details &ndash;&gt;-->
    <!--        <b-modal-->
    <!--            ok-only-->
    <!--            size="md"-->
    <!--            id="showDetails"-->
    <!--            :title="$t('CustomerDetails')"-->
    <!--        >-->
    <!--            <v-row>-->
    <!--                <v-col lg="12" md="12" sm="12" class="mt-3">-->
    <!--                    <table class="table table-striped table-md">-->
    <!--                        <tbody>-->
    <!--                            <tr>-->
    <!--                                &lt;!&ndash; Customer Code &ndash;&gt;-->
    <!--                                <td>{{ $t("CustomerCode") }}</td>-->
    <!--                                <th>{{ client.code }}</th>-->
    <!--                            </tr>-->
    <!--                            <tr>-->
    <!--                                &lt;!&ndash; Customer Name &ndash;&gt;-->
    <!--                                <td>{{ $t("CustomerName") }}</td>-->
    <!--                                <th>{{ client.name }}</th>-->
    <!--                            </tr>-->
    <!--                            <tr>-->
    <!--                                &lt;!&ndash; Customer Phone &ndash;&gt;-->
    <!--                                <td>{{ $t("Phone") }}</td>-->
    <!--                                <th>{{ client.phone }}</th>-->
    <!--                            </tr>-->
    <!--                            <tr>-->
    <!--                                &lt;!&ndash; Customer Email &ndash;&gt;-->
    <!--                                <td>{{ $t("Email") }}</td>-->
    <!--                                <th>{{ client.email }}</th>-->
    <!--                            </tr>-->
    <!--                            <tr>-->
    <!--                                &lt;!&ndash; Customer country &ndash;&gt;-->
    <!--                                <td>{{ $t("Country") }}</td>-->
    <!--                                <th>{{ client.country }}</th>-->
    <!--                            </tr>-->
    <!--                            <tr>-->
    <!--                                &lt;!&ndash; Customer City &ndash;&gt;-->
    <!--                                <td>{{ $t("City") }}</td>-->
    <!--                                <th>{{ client.city }}</th>-->
    <!--                            </tr>-->
    <!--                            <tr>-->
    <!--                                &lt;!&ndash; Customer Adress &ndash;&gt;-->
    <!--                                <td>{{ $t("Adress") }}</td>-->
    <!--                                <th>{{ client.adresse }}</th>-->
    <!--                            </tr>-->
    <!--                            <tr>-->
    <!--                                &lt;!&ndash; Tax Number &ndash;&gt;-->
    <!--                                <td>{{ $t("Tax_Number") }}</td>-->
    <!--                                <th>{{ client.tax_number }}</th>-->
    <!--                            </tr>-->

    <!--                            <tr>-->
    <!--                                &lt;!&ndash; Total_Sale_Due &ndash;&gt;-->
    <!--                                <td>{{ $t("Total_Sale_Due") }}</td>-->
    <!--                                <th>-->
    <!--                                    {{ currentUser.currency }} {{ client.due }}-->
    <!--                                </th>-->
    <!--                            </tr>-->

    <!--                            <tr>-->
    <!--                                &lt;!&ndash; Total_Sell_Return_Due &ndash;&gt;-->
    <!--                                <td>{{ $t("Total_Sell_Return_Due") }}</td>-->
    <!--                                <th>-->
    <!--                                    {{ currentUser.currency }}-->
    <!--                                    {{ client.return_Due }}-->
    <!--                                </th>-->
    <!--                            </tr>-->
    <!--                        </tbody>-->
    <!--                    </table>-->
    <!--                </v-col>-->
    <!--            </v-row>-->
    <!--        </b-modal>-->
</template>
