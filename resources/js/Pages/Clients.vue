<script setup>
import { ref } from "vue";
import Layout from "@/Layouts/Authenticated.vue";
import { VDataTableServer } from "vuetify/labs/VDataTable";
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
const alertType = ref("info");
const alertText = ref("");
const alert = ref(false);
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
    { title: "Usuario", key: "code" },
    { title: "Apellido", key: "name" },
    { title: "Nombre", key: "phone" },
    { title: "Telefono", key: "email" },
    { title: "Estado", key: "tax_number" },
    { title: "Acciones", key: "due" },
    { title: "Acciones", key: "return_Due" },
    { title: "Acciones", key: "actions" },
]);

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
            name: this.client.name,
            email: this.client.email,
            phone: this.client.phone,
            tax_number: this.client.tax_number,
            country: this.client.country,
            city: this.client.city,
            adresse: this.client.adresse,
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
function Submit_Payment_sell_due() {
snackbar.value = false;
 const validate = await formPayDue.value.validate();
    if (!validate.valid)
       {
        snackbar.value = true;
            snackbarColor.value = "error";
            snackbarText.value = "Por favor llene correctamente los campos";
       }
       else if (payment.value.amount > payment.value.due){
       payment.value.amount = 0;}
       else{
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
        );
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
        dialogPayDue.value=true;
    }, 500);
}

//------------------------------ Print Customer_Invoice -------------------------\\
function print_it() {
    var divContents = document.getElementById("invoice-POS").innerHTML;
    var a = window.open("", "", "height=500, width=500");
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
    loading.value=true;
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

//------ Validate Form Submit_Payment_sell_return_due
function Submit_Payment_sell_return_due() {
snackbar.value = false;
 const validate = await formPayDue.value.validate();
    if (!validate.valid)
       {
        snackbar.value = true;
            snackbarColor.value = "error";
            snackbarText.value = "Por favor llene correctamente los campos";
       }
       else if (payment.value.amount > payment.value.due){
        snackbar.value = true;
            snackbarColor.value = "error";
            snackbarText.value = "el monto es mayor a la deuda";
       payment.value.amount = 0;}
       else{
       Submit_Pay_return_due();
       }
}

//---------- keyup paid Amount

function Verified_return_paidAmount() {
    if (isNaN(this.payment_return.amount)) {
        this.payment_return.amount = 0;
    } else if (this.payment_return.amount > this.payment_return.return_Due) {
        this.makeToast(
            "warning",
            this.$t("Paying_amount_is_greater_than_Total_Due"),
            this.$t("Warning")
        );
        this.payment_return.amount = 0;
    }
}

//-------------------------------- reset_Form_payment-------------------------------\\
function reset_Form_payment_return_due() {
    this.payment_return = {
        client_id: "",
        client_name: "",
        date: "",
        return_Due: "",
        amount: "",
        notes: "",
        Reglement: "",
    };
}

//------------------------------ Show Modal Pay_return_due-------------------------------\\
function Pay_return_due(row) {
    this.reset_Form_payment_return_due();
    this.payment_return.client_id = row.id;
    this.payment_return.client_name = row.name;
    this.payment_return.return_Due = row.return_Due;
    this.payment_return.date = new Date().toISOString().slice(0, 10);
    setTimeout(() => {
        this.$bvModal.show("modal_Pay_return_due");
    }, 500);
}

//------------------------------ Print Customer_Invoice -------------------------\\
function print_return_due() {
    var divContents = document.getElementById("invoice-POS-return").innerHTML;
    var a = window.open("", "", "height=500, width=500");
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
function Submit_Pay_return_due() {
    this.payment_return_Processing = true;
    axios
        .post("clients_pay_return_due", {
            client_id: this.payment_return.client_id,
            amount: this.payment_return.amount,
            notes: this.payment_return.notes,
            Reglement: this.payment_return.Reglement,
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
</script>

<template>
    <div class="main-content">
        <breadcumb :page="$t('CustomerManagement')" :folder="$t('Customers')" />
        <div
            v-if="isLoading"
            class="loading_page spinner spinner-primary mr-3"
        ></div>
        <div v-else>
            <vue-good-table
                mode="remote"
                :columns="columns"
                :totalRows="totalRows"
                :rows="clients"
                @on-page-change="onPageChange"
                @on-per-page-change="onPerPageChange"
                @on-sort-change="onSortChange"
                @on-search="onSearch"
                :search-options="{
                    enabled: true,
                    placeholder: $t('Search_this_table'),
                }"
                :select-options="{
                    enabled: true,
                    clearSelectionText: '',
                }"
                @on-selected-rows-change="selectionChanged"
                :pagination-options="{
                    enabled: true,
                    mode: 'records',
                    nextLabel: 'next',
                    prevLabel: 'prev',
                }"
                :styleClass="
                    showDropdown
                        ? 'tableOne table-hover vgt-table full-height'
                        : 'tableOne table-hover vgt-table non-height'
                "
            >
                <div slot="selected-row-actions">
                    <button
                        class="btn btn-danger btn-sm"
                        @click="delete_by_selected()"
                    >
                        {{ $t("Del") }}
                    </button>
                </div>
                <div slot="table-actions" class="mt-2 mb-3">
                    <b-button
                        variant="outline-info m-1"
                        size="sm"
                        v-b-toggle.sidebar-right
                    >
                        <i class="i-Filter-2"></i>
                        {{ $t("Filter") }}
                    </b-button>
                    <b-button
                        @click="clients_PDF()"
                        size="sm"
                        variant="outline-success m-1"
                    >
                        <i class="i-File-Copy"></i> PDF
                    </b-button>
                    <vue-excel-xlsx
                        class="btn btn-sm btn-outline-danger ripple m-1"
                        :data="clients"
                        :columns="columns"
                        :file-name="'clients'"
                        :file-type="'xlsx'"
                        :sheet-name="'clients'"
                    >
                        <i class="i-File-Excel"></i> EXCEL
                    </vue-excel-xlsx>
                    <b-button
                        @click="Show_import_clients()"
                        size="sm"
                        variant="info m-1"
                        v-if="
                            currentUserPermissions &&
                            currentUserPermissions.includes('customers_import')
                        "
                    >
                        <i class="i-Download"></i>
                        {{ $t("Import_Customers") }}
                    </b-button>
                    <b-button
                        @click="New_Client()"
                        size="sm"
                        variant="btn btn-primary btn-icon m-1"
                        v-if="
                            currentUserPermissions &&
                            currentUserPermissions.includes('Customers_add')
                        "
                    >
                        <i class="i-Add"></i>
                        {{ $t("Add") }}
                    </b-button>
                </div>

                <template slot="table-row" slot-scope="props">
                    <span v-if="props.column.field == 'actions'">
                        <div>
                            <b-dropdown
                                id="dropdown-right"
                                variant="link"
                                text="right align"
                                toggle-class="text-decoration-none"
                                size="lg"
                                right
                                no-caret
                            >
                                <template
                                    v-slot:button-content
                                    class="_r_btn border-0"
                                >
                                    <span
                                        class="_dot _r_block-dot bg-dark"
                                    ></span>
                                    <span
                                        class="_dot _r_block-dot bg-dark"
                                    ></span>
                                    <span
                                        class="_dot _r_block-dot bg-dark"
                                    ></span>
                                </template>

                                <b-dropdown-item
                                    v-if="
                                        props.row.due > 0 &&
                                        currentUserPermissions &&
                                        currentUserPermissions.includes(
                                            'pay_due'
                                        )
                                    "
                                    @click="Pay_due(props.row)"
                                >
                                    <i
                                        class="nav-icon i-Dollar font-weight-bold mr-2"
                                    ></i>
                                    {{ $t("pay_all_sell_due_at_a_time") }}
                                </b-dropdown-item>

                                <b-dropdown-item
                                    v-if="
                                        props.row.return_Due > 0 &&
                                        currentUserPermissions &&
                                        currentUserPermissions.includes(
                                            'pay_sale_return_due'
                                        )
                                    "
                                    @click="Pay_return_due(props.row)"
                                >
                                    <i
                                        class="nav-icon i-Dollar font-weight-bold mr-2"
                                    ></i>
                                    {{
                                        $t("pay_all_sell_return_due_at_a_time")
                                    }}
                                </b-dropdown-item>

                                <b-dropdown-item
                                    @click="showDetails(props.row)"
                                >
                                    <i
                                        class="nav-icon i-Eye font-weight-bold mr-2"
                                    ></i>
                                    {{ $t("Customer_details") }}
                                </b-dropdown-item>

                                <b-dropdown-item
                                    v-if="
                                        currentUserPermissions &&
                                        currentUserPermissions.includes(
                                            'Customers_edit'
                                        )
                                    "
                                    @click="Edit_Client(props.row)"
                                >
                                    <i
                                        class="nav-icon i-Edit font-weight-bold mr-2"
                                    ></i>
                                    {{ $t("Edit_Customer") }}
                                </b-dropdown-item>

                                <b-dropdown-item
                                    title="Delete"
                                    v-if="
                                        currentUserPermissions.includes(
                                            'Customers_delete'
                                        )
                                    "
                                    @click="Remove_Client(props.row.id)"
                                >
                                    <i
                                        class="nav-icon i-Close-Window font-weight-bold mr-2"
                                    ></i>
                                    {{ $t("Delete_Customer") }}
                                </b-dropdown-item>
                            </b-dropdown>
                        </div>
                    </span>
                </template>
            </vue-good-table>
        </div>

        <!-- Multiple filters -->
        <b-sidebar
            id="sidebar-right"
            :title="$t('Filter')"
            bg-variant="white"
            right
            shadow
        >
            <div class="px-3 py-2">
                <b-row>
                    <!-- Code Customer   -->
                    <b-col md="12">
                        <b-form-group :label="$t('CustomerCode')">
                            <b-form-input
                                label="Code"
                                :placeholder="$t('SearchByCode')"
                                v-model="Filter_Code"
                            ></b-form-input>
                        </b-form-group>
                    </b-col>

                    <!-- Name Customer   -->
                    <b-col md="12">
                        <b-form-group :label="$t('CustomerName')">
                            <b-form-input
                                label="Name"
                                :placeholder="$t('SearchByName')"
                                v-model="Filter_Name"
                            ></b-form-input>
                        </b-form-group>
                    </b-col>

                    <!-- Phone Customer   -->
                    <b-col md="12">
                        <b-form-group :label="$t('Phone')">
                            <b-form-input
                                label="Phone"
                                :placeholder="$t('SearchByPhone')"
                                v-model="Filter_Phone"
                            ></b-form-input>
                        </b-form-group>
                    </b-col>

                    <!-- Email Customer   -->
                    <b-col md="12">
                        <b-form-group :label="$t('Email')">
                            <b-form-input
                                label="Email"
                                :placeholder="$t('SearchByEmail')"
                                v-model="Filter_Email"
                            ></b-form-input>
                        </b-form-group>
                    </b-col>

                    <b-col md="6" sm="12">
                        <b-button
                            @click="Get_Clients(serverParams.page)"
                            variant="primary m-1"
                            size="sm"
                            block
                        >
                            <i class="i-Filter-2"></i>
                            {{ $t("Filter") }}
                        </b-button>
                    </b-col>
                    <b-col md="6" sm="12">
                        <b-button
                            @click="Reset_Filter()"
                            variant="danger m-1"
                            size="sm"
                            block
                        >
                            <i class="i-Power-2"></i>
                            {{ $t("Reset") }}
                        </b-button>
                    </b-col>
                </b-row>
            </div>
        </b-sidebar>

        <!-- Modal Pay_due-->
        <validation-observer ref="ref_pay_due">
            <b-modal hide-footer size="md" id="modal_Pay_due" title="Pay Due">
                <b-form @submit.prevent="Submit_Payment_sell_due">
                    <b-row>
                        <!-- Paying Amount  -->
                        <b-col lg="6" md="12" sm="12">
                            <validation-provider
                                name="Amount"
                                :rules="{
                                    required: true,
                                    regex: /^\d*\.?\d*$/,
                                }"
                                v-slot="validationContext"
                            >
                                <b-form-group
                                    :label="$t('Paying_Amount') + ' ' + '*'"
                                >
                                    <b-form-input
                                        @keyup="
                                            Verified_paidAmount(payment.amount)
                                        "
                                        label="Amount"
                                        :placeholder="$t('Paying_Amount')"
                                        v-model.number="payment.amount"
                                        :state="
                                            getValidationState(
                                                validationContext
                                            )
                                        "
                                        aria-describedby="Amount-feedback"
                                    ></b-form-input>
                                    <b-form-invalid-feedback
                                        id="Amount-feedback"
                                        >{{
                                            validationContext.errors[0]
                                        }}</b-form-invalid-feedback
                                    >
                                    <span class="badge badge-danger"
                                        >{{ $t("Due") }} :
                                        {{ currentUser.currency }}
                                        {{ payment.due }}</span
                                    >
                                </b-form-group>
                            </validation-provider>
                        </b-col>

                        <!-- Payment choice -->
                        <b-col lg="6" md="12" sm="12">
                            <validation-provider
                                name="Payment choice"
                                :rules="{ required: true }"
                            >
                                <b-form-group
                                    slot-scope="{ valid, errors }"
                                    :label="$t('Paymentchoice') + ' ' + '*'"
                                >
                                    <v-select
                                        :class="{
                                            'is-invalid': !!errors.length,
                                        }"
                                        :state="
                                            errors[0]
                                                ? false
                                                : valid
                                                ? true
                                                : null
                                        "
                                        v-model="payment.Reglement"
                                        :reduce="(label) => label.value"
                                        :placeholder="$t('PleaseSelect')"
                                        :options="[
                                            { label: 'Cash', value: 'Cash' },
                                            {
                                                label: 'credit card',
                                                value: 'credit card',
                                            },
                                            { label: 'TPE', value: 'tpe' },
                                            {
                                                label: 'cheque',
                                                value: 'cheque',
                                            },
                                            {
                                                label: 'Western Union',
                                                value: 'Western Union',
                                            },
                                            {
                                                label: 'bank transfer',
                                                value: 'bank transfer',
                                            },
                                            { label: 'other', value: 'other' },
                                        ]"
                                    ></v-select>
                                    <b-form-invalid-feedback>{{
                                        errors[0]
                                    }}</b-form-invalid-feedback>
                                </b-form-group>
                            </validation-provider>
                        </b-col>

                        <!-- Note -->
                        <b-col lg="12" md="12" sm="12" class="mt-3">
                            <b-form-group
                                :label="$t('Please_provide_any_details')"
                            >
                                <b-form-textarea
                                    id="textarea"
                                    v-model="payment.notes"
                                    rows="3"
                                    max-rows="6"
                                ></b-form-textarea>
                            </b-form-group>
                        </b-col>

                        <b-col md="12" class="mt-3">
                            <b-button
                                variant="primary"
                                type="submit"
                                :disabled="paymentProcessing"
                                >{{ $t("submit") }}</b-button
                            >
                            <div
                                v-once
                                class="typo__p"
                                v-if="paymentProcessing"
                            >
                                <div
                                    class="spinner sm spinner-primary mt-3"
                                ></div>
                            </div>
                        </b-col>
                    </b-row>
                </b-form>
            </b-modal>
        </validation-observer>

        <!-- Modal Pay_return_Due-->
        <validation-observer ref="ref_pay_return_due">
            <b-modal
                hide-footer
                size="md"
                id="modal_Pay_return_due"
                title="Pay Sell Return Due"
            >
                <b-form @submit.prevent="Submit_Payment_sell_return_due">
                    <b-row>
                        <!-- Paying Amount -->
                        <b-col lg="6" md="12" sm="12">
                            <validation-provider
                                name="Amount"
                                :rules="{
                                    required: true,
                                    regex: /^\d*\.?\d*$/,
                                }"
                                v-slot="validationContext"
                            >
                                <b-form-group
                                    :label="$t('Paying_Amount') + ' ' + '*'"
                                >
                                    <b-form-input
                                        @keyup="
                                            Verified_return_paidAmount(
                                                payment_return.amount
                                            )
                                        "
                                        label="Amount"
                                        :placeholder="$t('Paying_Amount')"
                                        v-model.number="payment_return.amount"
                                        :state="
                                            getValidationState(
                                                validationContext
                                            )
                                        "
                                        aria-describedby="Amount-feedback"
                                    ></b-form-input>
                                    <b-form-invalid-feedback
                                        id="Amount-feedback"
                                        >{{
                                            validationContext.errors[0]
                                        }}</b-form-invalid-feedback
                                    >
                                    <span class="badge badge-danger"
                                        >{{ $t("Due") }} :
                                        {{ currentUser.currency }}
                                        {{ payment_return.return_Due }}</span
                                    >
                                </b-form-group>
                            </validation-provider>
                        </b-col>

                        <!-- Payment choice -->
                        <b-col lg="6" md="12" sm="12">
                            <validation-provider
                                name="Payment choice"
                                :rules="{ required: true }"
                            >
                                <b-form-group
                                    slot-scope="{ valid, errors }"
                                    :label="$t('Paymentchoice') + ' ' + '*'"
                                >
                                    <v-select
                                        :class="{
                                            'is-invalid': !!errors.length,
                                        }"
                                        :state="
                                            errors[0]
                                                ? false
                                                : valid
                                                ? true
                                                : null
                                        "
                                        v-model="payment_return.Reglement"
                                        :reduce="(label) => label.value"
                                        :placeholder="$t('PleaseSelect')"
                                        :options="[
                                            { label: 'Cash', value: 'Cash' },
                                            {
                                                label: 'credit card',
                                                value: 'credit card',
                                            },
                                            { label: 'TPE', value: 'tpe' },
                                            {
                                                label: 'cheque',
                                                value: 'cheque',
                                            },
                                            {
                                                label: 'Western Union',
                                                value: 'Western Union',
                                            },
                                            {
                                                label: 'bank transfer',
                                                value: 'bank transfer',
                                            },
                                            { label: 'other', value: 'other' },
                                        ]"
                                    ></v-select>
                                    <b-form-invalid-feedback>{{
                                        errors[0]
                                    }}</b-form-invalid-feedback>
                                </b-form-group>
                            </validation-provider>
                        </b-col>

                        <!-- Note -->
                        <b-col lg="12" md="12" sm="12" class="mt-3">
                            <b-form-group
                                :label="$t('Please_provide_any_details')"
                            >
                                <b-form-textarea
                                    id="textarea"
                                    v-model="payment_return.notes"
                                    rows="3"
                                    max-rows="6"
                                ></b-form-textarea>
                            </b-form-group>
                        </b-col>

                        <b-col md="12" class="mt-3">
                            <b-button
                                variant="primary"
                                type="submit"
                                :disabled="payment_return_Processing"
                                >{{ $t("submit") }}</b-button
                            >
                            <div
                                v-once
                                class="typo__p"
                                v-if="payment_return_Processing"
                            >
                                <div
                                    class="spinner sm spinner-primary mt-3"
                                ></div>
                            </div>
                        </b-col>
                    </b-row>
                </b-form>
            </b-modal>
        </validation-observer>

        <!-- Modal Show Customer_Invoice-->
        <b-modal
            hide-footer
            size="sm"
            scrollable
            id="Show_invoice"
            :title="$t('Customer_Credit_Note')"
        >
            <div id="invoice-POS">
                <div style="max-width: 400px; margin: 0px auto">
                    <div class="info">
                        <h2 class="text-center">
                            {{ company_info.CompanyName }}
                        </h2>

                        <p>
                            <span
                                >{{ $t("date") }} : {{ payment.date }} <br
                            /></span>
                            <span
                                >{{ $t("Adress") }} :
                                {{ company_info.CompanyAdress }} <br
                            /></span>
                            <span
                                >{{ $t("Phone") }} :
                                {{ company_info.CompanyPhone }} <br
                            /></span>
                            <span
                                >{{ $t("Customer") }} :
                                {{ payment.client_name }} <br
                            /></span>
                        </p>
                    </div>

                    <table class="change mt-3" style="font-size: 10px">
                        <thead>
                            <tr style="background: #eee">
                                <th style="text-align: left" colspan="1">
                                    {{ $t("PayeBy") }}:
                                </th>
                                <th style="text-align: center" colspan="2">
                                    {{ $t("Amount") }}:
                                </th>
                                <th style="text-align: right" colspan="1">
                                    {{ $t("Due") }}:
                                </th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr>
                                <td style="text-align: left" colspan="1">
                                    {{ payment.Reglement }}
                                </td>
                                <td style="text-align: center" colspan="2">
                                    {{ formatNumber(payment.amount, 2) }}
                                </td>
                                <td style="text-align: right" colspan="1">
                                    {{
                                        formatNumber(
                                            payment.due - payment.amount,
                                            2
                                        )
                                    }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <button @click="print_it()" class="btn btn-outline-primary">
                <i class="i-Billing"></i>
                {{ $t("print") }}
            </button>
        </b-modal>

        <!-- Modal Show_invoice_return-->
        <b-modal
            hide-footer
            size="sm"
            scrollable
            id="Show_invoice_return"
            :title="$t('Sell_return_due')"
        >
            <div id="invoice-POS-return">
                <div style="max-width: 400px; margin: 0px auto">
                    <div class="info">
                        <h2 class="text-center">
                            {{ company_info.CompanyName }}
                        </h2>

                        <p>
                            <span
                                >{{ $t("date") }} : {{ payment_return.date }}
                                <br
                            /></span>
                            <span
                                >{{ $t("Adress") }} :
                                {{ company_info.CompanyAdress }} <br
                            /></span>
                            <span
                                >{{ $t("Phone") }} :
                                {{ company_info.CompanyPhone }} <br
                            /></span>
                            <span
                                >{{ $t("Customer") }} :
                                {{ payment_return.client_name }} <br
                            /></span>
                        </p>
                    </div>

                    <table class="change mt-3" style="font-size: 10px">
                        <thead>
                            <tr style="background: #eee">
                                <th style="text-align: left" colspan="1">
                                    {{ $t("PayeBy") }}:
                                </th>
                                <th style="text-align: center" colspan="2">
                                    {{ $t("Amount") }}:
                                </th>
                                <th style="text-align: right" colspan="1">
                                    {{ $t("Due") }}:
                                </th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr>
                                <td style="text-align: left" colspan="1">
                                    {{ payment_return.Reglement }}
                                </td>
                                <td style="text-align: center" colspan="2">
                                    {{ formatNumber(payment_return.amount, 2) }}
                                </td>
                                <td style="text-align: right" colspan="1">
                                    {{
                                        formatNumber(
                                            payment_return.return_Due -
                                                payment_return.amount,
                                            2
                                        )
                                    }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <button @click="print_return_due()" class="btn btn-outline-primary">
                <i class="i-Billing"></i>
                {{ $t("print") }}
            </button>
        </b-modal>

        <!-- Modal Create & Edit Customer -->
        <validation-observer ref="Create_Customer">
            <b-modal
                hide-footer
                size="lg"
                id="New_Customer"
                :title="editmode ? $t('Edit') : $t('Add')"
            >
                <b-form @submit.prevent="Submit_Customer">
                    <b-row>
                        <!-- Customer Name -->
                        <b-col md="6" sm="12">
                            <validation-provider
                                name="Name Customer"
                                :rules="{ required: true }"
                                v-slot="validationContext"
                            >
                                <b-form-group
                                    :label="$t('CustomerName') + ' ' + '*'"
                                >
                                    <b-form-input
                                        :state="
                                            getValidationState(
                                                validationContext
                                            )
                                        "
                                        aria-describedby="name-feedback"
                                        label="name"
                                        :placeholder="$t('CustomerName')"
                                        v-model="client.name"
                                    ></b-form-input>
                                    <b-form-invalid-feedback
                                        id="name-feedback"
                                        >{{
                                            validationContext.errors[0]
                                        }}</b-form-invalid-feedback
                                    >
                                </b-form-group>
                            </validation-provider>
                        </b-col>

                        <!-- Customer Email -->
                        <b-col md="6" sm="12">
                            <b-form-group :label="$t('Email')">
                                <b-form-input
                                    label="email"
                                    v-model="client.email"
                                    :placeholder="$t('Email')"
                                ></b-form-input>
                            </b-form-group>
                        </b-col>

                        <!-- Customer Phone -->
                        <b-col md="6" sm="12">
                            <b-form-group :label="$t('Phone')">
                                <b-form-input
                                    label="Phone"
                                    v-model="client.phone"
                                    :placeholder="$t('Phone')"
                                ></b-form-input>
                            </b-form-group>
                        </b-col>

                        <!-- Customer Country -->
                        <b-col md="6" sm="12">
                            <b-form-group :label="$t('Country')">
                                <b-form-input
                                    label="Country"
                                    v-model="client.country"
                                    :placeholder="$t('Country')"
                                ></b-form-input>
                            </b-form-group>
                        </b-col>

                        <!-- Customer City -->
                        <b-col md="6" sm="12">
                            <b-form-group :label="$t('City')">
                                <b-form-input
                                    label="City"
                                    v-model="client.city"
                                    :placeholder="$t('City')"
                                ></b-form-input>
                            </b-form-group>
                        </b-col>

                        <!-- Customer Tax Number -->
                        <b-col md="6" sm="12">
                            <b-form-group :label="$t('Tax_Number')">
                                <b-form-input
                                    label="Tax Number"
                                    v-model="client.tax_number"
                                    :placeholder="$t('Tax_Number')"
                                ></b-form-input>
                            </b-form-group>
                        </b-col>

                        <!-- Customer Adress -->
                        <b-col md="12" sm="12">
                            <b-form-group :label="$t('Adress')">
                                <textarea
                                    label="Adress"
                                    class="form-control"
                                    rows="4"
                                    v-model="client.adresse"
                                    :placeholder="$t('Adress')"
                                ></textarea>
                            </b-form-group>
                        </b-col>

                        <b-col md="12" class="mt-3">
                            <b-button
                                variant="primary"
                                type="submit"
                                :disabled="SubmitProcessing"
                                >{{ $t("submit") }}</b-button
                            >
                            <div v-once class="typo__p" v-if="SubmitProcessing">
                                <div
                                    class="spinner sm spinner-primary mt-3"
                                ></div>
                            </div>
                        </b-col>
                    </b-row>
                </b-form>
            </b-modal>
        </validation-observer>

        <!-- Modal Show Customer Details -->
        <b-modal
            ok-only
            size="md"
            id="showDetails"
            :title="$t('CustomerDetails')"
        >
            <b-row>
                <b-col lg="12" md="12" sm="12" class="mt-3">
                    <table class="table table-striped table-md">
                        <tbody>
                            <tr>
                                <!-- Customer Code -->
                                <td>{{ $t("CustomerCode") }}</td>
                                <th>{{ client.code }}</th>
                            </tr>
                            <tr>
                                <!-- Customer Name -->
                                <td>{{ $t("CustomerName") }}</td>
                                <th>{{ client.name }}</th>
                            </tr>
                            <tr>
                                <!-- Customer Phone -->
                                <td>{{ $t("Phone") }}</td>
                                <th>{{ client.phone }}</th>
                            </tr>
                            <tr>
                                <!-- Customer Email -->
                                <td>{{ $t("Email") }}</td>
                                <th>{{ client.email }}</th>
                            </tr>
                            <tr>
                                <!-- Customer country -->
                                <td>{{ $t("Country") }}</td>
                                <th>{{ client.country }}</th>
                            </tr>
                            <tr>
                                <!-- Customer City -->
                                <td>{{ $t("City") }}</td>
                                <th>{{ client.city }}</th>
                            </tr>
                            <tr>
                                <!-- Customer Adress -->
                                <td>{{ $t("Adress") }}</td>
                                <th>{{ client.adresse }}</th>
                            </tr>
                            <tr>
                                <!-- Tax Number -->
                                <td>{{ $t("Tax_Number") }}</td>
                                <th>{{ client.tax_number }}</th>
                            </tr>

                            <tr>
                                <!-- Total_Sale_Due -->
                                <td>{{ $t("Total_Sale_Due") }}</td>
                                <th>
                                    {{ currentUser.currency }} {{ client.due }}
                                </th>
                            </tr>

                            <tr>
                                <!-- Total_Sell_Return_Due -->
                                <td>{{ $t("Total_Sell_Return_Due") }}</td>
                                <th>
                                    {{ currentUser.currency }}
                                    {{ client.return_Due }}
                                </th>
                            </tr>
                        </tbody>
                    </table>
                </b-col>
            </b-row>
        </b-modal>

        <!-- Modal Show Import Clients -->
        <b-modal
            ok-only
            ok-title="Cancel"
            size="md"
            id="importClients"
            :title="$t('Import_Customers')"
        >
            <b-form
                @submit.prevent="Submit_import"
                enctype="multipart/form-data"
            >
                <b-row>
                    <!-- File -->
                    <b-col md="12" sm="12" class="mb-3">
                        <b-form-group>
                            <input
                                type="file"
                                @change="onFileSelected"
                                label="Choose File"
                            />
                            <b-form-invalid-feedback
                                id="File-feedback"
                                class="d-block"
                                >{{
                                    $t("field_must_be_in_csv_format")
                                }}</b-form-invalid-feedback
                            >
                        </b-form-group>
                    </b-col>

                    <b-col md="6" sm="12">
                        <b-button
                            type="submit"
                            variant="primary"
                            :disabled="ImportProcessing"
                            size="sm"
                            block
                            >{{ $t("submit") }}</b-button
                        >
                        <div v-once class="typo__p" v-if="ImportProcessing">
                            <div class="spinner sm spinner-primary mt-3"></div>
                        </div>
                    </b-col>

                    <b-col md="6" sm="12">
                        <b-button
                            :href="'/import/exemples/import_clients.csv'"
                            variant="info"
                            size="sm"
                            block
                            >{{ $t("Download_exemple") }}</b-button
                        >
                    </b-col>

                    <b-col md="12" sm="12">
                        <table class="table table-bordered table-sm mt-4">
                            <tbody>
                                <tr>
                                    <td>{{ $t("Name") }}</td>
                                    <th>
                                        <span
                                            class="badge badge-outline-success"
                                            >{{ $t("Field_is_required") }}</span
                                        >
                                    </th>
                                </tr>

                                <tr>
                                    <td>{{ $t("Phone") }}</td>
                                </tr>

                                <tr>
                                    <td>{{ $t("Email") }}</td>
                                    <th>
                                        <span
                                            class="badge badge-outline-success"
                                        ></span>
                                    </th>
                                </tr>

                                <tr>
                                    <td>{{ $t("Country") }}</td>
                                </tr>

                                <tr>
                                    <td>{{ $t("City") }}</td>
                                </tr>

                                <tr>
                                    <td>{{ $t("Adress") }}</td>
                                </tr>
                                <tr>
                                    <td>{{ $t("Tax_Number") }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </b-col>
                </b-row>
            </b-form>
        </b-modal>
    </div>
</template>
