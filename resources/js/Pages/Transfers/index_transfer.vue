<script setup>
import { onMounted, ref } from "vue";
import Layout from "@/Layouts/Authenticated.vue";
import ExportBtn from "@/Components/buttons/ExportBtn.vue";
import { router } from "@inertiajs/vue3";
import DeleteDialog from "@/Components/dialogs/DeleteDialog.vue";
import InvoiceTransferDialog from "@/Components/dialogs/InvoiceTransferDialog.vue";
import Snackbar from "@/Components/snackbar.vue";
import { api, globals, helpers, labels } from "@/helpers";

const currency = globals.currency();
const enableDays = globals.oldDay();

const props = defineProps({
    errors: Object,
});
const warehouses = ref([]);
const transfers = ref([]);
const search = ref("");
const loading = ref(false);
const snackbar = ref({
    view: false,
    color: "",
    text: "",
});
const dialogDelete = ref(false);
const dialogDetail = ref(false);
const details = ref([]);
const transfer = ref({ GrandTotal: "" });

const fields = ref([
    { title: labels.transfer.date, key: "date" },
    { title: labels.transfer.Ref, key: "Ref" },
    { title: labels.transfer.from_warehouse, key: "from_warehouse" },
    { title: labels.transfer.to_warehouse, key: "to_warehouse" },
    { title: labels.transfer.items, key: "items" },
    { title: labels.transfer.GrandTotal, key: "GrandTotal" },
    { title: labels.transfer.statut, key: "statut" },
    { title: labels.actions, key: "actions" },
]);
const jsonFields = ref({
    Fecha: "date",
    Codigo: "Ref",
    de_Agencia: "from_warehouse",
    a_Agencia: "to_warehouse",
    Items: "items",
    Total: "GrandTotal",
    Estado: "statut",
});

const invoice_pos = ref({
    transfer: {
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
const dialogInvoice = ref(false);
const loadingInvoice = ref(false);

//----------------------------------- Get Details Transfer ------------------------------\\
function showDetails(id) {
    dialogDetail.value = false;
    api.get({
        url: "/transfers/item/" + id,
        loadingItem: loading,
        snackbar,
        onSuccess: (data) => {
            transfer.value = data.transfer;
            details.value = data.details;
            dialogDetail.value = true;
        },
        onError: () => {
            dialogDetail.value = true;
        },
    });
}

//-------------------------------- Reset Form -------------------------------\\
function reset_Form() {
    transfer.value = { id: "" };
}

//---------------------------------- Delete Transfer ----------------------\\
function onCloseDelete() {
    reset_Form();
    dialogDelete.value = false;
}

function Delete_Item(item) {
    reset_Form();
    transfer.value = item;
    dialogDelete.value = true;
}

function Remove_Transfer(id) {
    api.delete({
        url: "/transfers/" + transfer.value.id,
        loadingItem: loading,
        snackbar,
        onSuccess: (data) => {
            snackbar.value.text = labels.delete_message;
            loadData();
            dialogDelete.value = false;
        },
    });
}

function Invoice_POS(id) {
    dialogInvoice.value = false;
    api.get({
        url: "/transfer_print_invoice/" + id,
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
function loadData() {
    api.get({
        url: "/transfers/list",
        loadingItem: loading,
        snackbar,
        onSuccess: (data) => {
            warehouses.value = data.warehouses;
            transfers.value = data.transfers;
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
        <invoice-transfer-dialog
            v-model="dialogInvoice"
            :invoice_pos="invoice_pos"
            :loading="loadingInvoice"
        ></invoice-transfer-dialog>
        <!-- Modal Remove Adjustment -->
        <delete-dialog
            :model="dialogDelete"
            :on-save="Remove_Transfer"
            :on-close="onCloseDelete"
        ></delete-dialog>
        <!-- Show details -->
        <v-dialog v-model="dialogDetail" max-width="800px">
            <v-card>
                <v-toolbar title="Detalle de Transferencia" border></v-toolbar>
                <v-card-text>
                    <v-row>
                        <v-col md="5" cols="12" class="mt-2">
                            <v-table hover>
                                <tbody>
                                    <!-- date -->
                                    <tr>
                                        <td>{{ labels.transfer.date }}</td>
                                        <td class="font-weight-bold">
                                            {{ transfer.date }}
                                        </td>
                                    </tr>
                                    <!-- Reference -->
                                    <tr>
                                        <td>{{ labels.transfer.Ref }}</td>
                                        <td class="font-weight-bold">
                                            {{ transfer.Ref }}
                                        </td>
                                    </tr>
                                    <!-- From warehouse -->
                                    <tr>
                                        <td>
                                            {{ labels.transfer.from_warehouse }}
                                        </td>
                                        <td class="font-weight-bold">
                                            {{ transfer.from_warehouse }}
                                        </td>
                                    </tr>
                                    <!-- To warehouse -->
                                    <tr>
                                        <td>
                                            {{ labels.transfer.to_warehouse }}
                                        </td>
                                        <td class="font-weight-bold">
                                            {{ transfer.to_warehouse }}
                                        </td>
                                    </tr>
                                    <!-- Grand Total -->
                                    <tr>
                                        <td>
                                            {{ labels.transfer.GrandTotal }}
                                        </td>
                                        <td class="font-weight-bold">
                                            {{ currency
                                            }}{{
                                                helpers.formatNumber(
                                                    transfer.GrandTotal,
                                                    2
                                                )
                                            }}
                                        </td>
                                    </tr>
                                    <!-- Status -->
                                    <tr>
                                        <td>{{ labels.transfer.statut }}</td>
                                        <td class="font-weight-bold">
                                            <v-chip
                                                :color="
                                                    helpers.statutTransferColor(
                                                        transfer.statut
                                                    )
                                                "
                                                variant="tonal"
                                                size="x-small"
                                                >{{
                                                    helpers.statutTransfer(
                                                        transfer.statut
                                                    )
                                                }}
                                            </v-chip>
                                        </td>
                                    </tr>
                                </tbody>
                            </v-table>
                        </v-col>
                        <v-col md="7" cols="12" class="mt-2">
                            <v-table border hover>
                                <thead>
                                    <tr>
                                        <th scope="col">
                                            {{ labels.transfer_detail.product }}
                                        </th>
                                        <th scope="col">
                                            {{ labels.transfer_detail.code }}
                                        </th>
                                        <th scope="col">
                                            {{ labels.transfer_detail.qty }}
                                        </th>
                                        <th scope="col">
                                            {{
                                                labels.transfer_detail.sub_total
                                            }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="detail in details">
                                        <td>{{ detail.name }}</td>
                                        <td>{{ detail.code }}</td>
                                        <td>
                                            {{
                                                helpers.formatNumber(
                                                    detail.quantity,
                                                    2
                                                )
                                            }}
                                            {{ detail.unit }}
                                        </td>
                                        <td>
                                            {{ currency }}
                                            {{ detail.total.toFixed(2) }}
                                        </td>
                                    </tr>
                                </tbody>
                            </v-table>
                        </v-col>
                    </v-row>
                    <v-divider v-show="transfer.note"></v-divider>
                    <v-row class="mt-3">
                        <v-col cols="12">
                            <p>{{ transfer.note }}</p>
                        </v-col>
                    </v-row>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn
                        variant="elevated"
                        color="primary"
                        class="ma-1"
                        @click="dialogDetail = false"
                        >Aceptar
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
        <v-row align="center">
            <v-col>
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
            <v-col cols="auto" class="text-right">
                <ExportBtn
                    :data="transfers"
                    :fields="jsonFields"
                    name-file="Transferencias"
                ></ExportBtn>
                <v-btn
                    v-if="globals.userPermision(['transfer_add'])"
                    color="primary"
                    class="ma-1"
                    prepend-icon="fas fa-user-plus"
                    @click="router.visit('/transfers/create')"
                >
                    {{ labels.add }}
                </v-btn>
            </v-col>
        </v-row>
        <v-row>
            <v-col>
                <v-data-table
                    :headers="fields"
                    :items="transfers"
                    :search="search"
                    hover
                    class="elevation-2"
                    no-data-text="No existen datos a mostrar"
                    :loading="loading"
                >
                    <template v-slot:item.statut="{ item }">
                        <v-chip
                            :color="helpers.statutTransferColor(item.statut)"
                            variant="tonal"
                            size="x-small"
                            >{{ helpers.statutTransfer(item.statut) }}
                        </v-chip>
                    </template>
                    <template v-slot:item.actions="{ item }">
                        <v-btn
                            v-if="globals.userPermision(['transfer_view'])"
                            class="ma-1"
                            color="secundary"
                            icon="fas fa-print"
                            size="x-small"
                            variant="outlined"
                            @click="Invoice_POS(item.id)"
                        >
                        </v-btn>
                        <v-btn
                            v-if="globals.userPermision(['transfer_view'])"
                            class="ma-1"
                            color="info"
                            icon="fas fa-eye"
                            size="x-small"
                            variant="outlined"
                            @click="showDetails(item.id)"
                        >
                        </v-btn>
                        <v-btn
                            v-if="globals.userPermision(['transfer_edit'])"
                            class="ma-1"
                            color="primary"
                            icon="fas fa-pen"
                            size="x-small"
                            variant="outlined"
                            :disabled="
                                helpers.maxEnableButtons(
                                    item.updated_at,
                                    enableDays
                                )
                            "
                            @click="router.visit('/transfers/edit/' + item.id)"
                        >
                        </v-btn>
                        <v-btn
                            v-if="globals.userPermision(['transfer_delete'])"
                            class="ma-1"
                            color="error"
                            icon="fas fa-trash"
                            size="x-small"
                            variant="outlined"
                            :disabled="
                                helpers.maxEnableButtons(
                                    item.updated_at,
                                    enableDays
                                )
                            "
                            @click="Delete_Item(item)"
                        >
                        </v-btn>
                    </template>
                </v-data-table>
            </v-col>
        </v-row>

        <!--    &lt;!&ndash; multiple filters &ndash;&gt;-->
        <!--    <v-sidebar id="sidebar-right" :title="$t('Filter')" bg-variant="white" right shadow>-->
        <!--      <div class="px-3 py-2">-->
        <!--        <v-row>-->
        <!--          &lt;!&ndash; Reference  &ndash;&gt;-->
        <!--          <v-col md="12">-->
        <!--            <v-form-group :label="$t('Reference')">-->
        <!--              <v-form-input label="Reference" :placeholder="$t('Reference')" v-model="Filter_Ref"></v-form-input>-->
        <!--            </v-form-group>-->
        <!--          </v-col>-->

        <!--          &lt;!&ndash; From warehouse  &ndash;&gt;-->
        <!--          <v-col md="12">-->
        <!--            <v-form-group :label="$t('FromWarehouse')">-->
        <!--              <v-select-->
        <!--                :reduce="label => label.value"-->
        <!--                :placeholder="$t('Choose_Warehouse')"-->
        <!--                v-model="Filter_From"-->
        <!--                :options="warehouses.map(warehouses => ({label: warehouses.name, value: warehouses.id}))"-->
        <!--              />-->
        <!--            </v-form-group>-->
        <!--          </v-col>-->

        <!--          &lt;!&ndash; To warehouse  &ndash;&gt;-->
        <!--          <v-col md="12">-->
        <!--            <v-form-group :label="$t('ToWarehouse')">-->
        <!--              <v-select-->
        <!--                :reduce="label => label.value"-->
        <!--                :placeholder="$t('Choose_Warehouse')"-->
        <!--                v-model="Filter_To"-->
        <!--                :options="warehouses.map(warehouses => ({label: warehouses.name, value: warehouses.id}))"-->
        <!--              />-->
        <!--            </v-form-group>-->
        <!--          </v-col>-->

        <!--          &lt;!&ndash; Status  &ndash;&gt;-->
        <!--          <v-col md="12">-->
        <!--            <v-form-group :label="$t('Status')">-->
        <!--              <v-select-->
        <!--                v-model="Filter_status"-->
        <!--                :reduce="label => label.value"-->
        <!--                :placeholder="$t('Choose_Status')"-->
        <!--                :options="-->
        <!--                      [-->
        <!--                        {label: 'Completed', value: 'completed'},-->
        <!--                        {label: 'Sent', value: 'sent'},-->
        <!--                        {label: 'Pending', value: 'pending'},-->
        <!--                      ]"-->
        <!--              ></v-select>-->
        <!--            </v-form-group>-->
        <!--          </v-col>-->
    </Layout>
</template>
