<script setup>
import { onMounted, ref } from "vue";
import Layout from "@/Layouts/Authenticated.vue";
import ExportBtn from "@/Components/buttons/ExportBtn.vue";
import { router } from "@inertiajs/vue3";
import DeleteDialog from "@/Components/dialogs/DeleteDialog.vue";
import Snackbar from "@/Components/snackbar.vue";
import { api, globals, helpers, labels } from "@/helpers";

const props = defineProps({
    errors: Object,
});
const enableDays = globals.oldDay();

const warehouses = ref([]);
const adjustments = ref([]);
const search = ref("");
const loading = ref(false);
const snackbar = ref({
    view: false,
    color: "",
    text: "",
});
const dialogDelete = ref(false);
const dialogDetail = ref(false);

const fields = ref([
    { title: labels.adjustment.date, key: "date" },
    { title: labels.adjustment.ref, key: "Ref" },
    { title: labels.adjustment.warehouse_id, key: "warehouse_name" },
    { title: labels.adjustment.items, key: "items" },
    { title: labels.actions, key: "actions" },
]);
const jsonFields = ref({
    Fecha: "date",
    Codigo: "Ref",
    Agencia: "warehouse_name",
    Items: "items",
});
const details = ref([]);
const adjustment = ref({});

//---------------Get Details Adjustement ----------------------\\
function showDetails(id) {
    dialogDetail.value = false;
    api.get({
        url: "/adjustments/detail/" + id,
        loadingItem: loading,
        snackbar,
        onSuccess: (data) => {
            adjustment.value = data.adjustment;
            details.value = data.details;
            dialogDetail.value = true;
        },
    });
}

//-------------------------------- Reset Form -------------------------------\\
function reset_Form() {
    adjustment.value = { id: "" };
}

//---------------------------------- Remove Adjustement----------------------\\
function onCloseDelete() {
    reset_Form();
    dialogDelete.value = false;
}

function Delete_Item(item) {
    reset_Form();
    adjustment.value = item;
    dialogDelete.value = true;
}

function Remove_Adjustment() {
    api.delete({
        url: "/adjustments/" + adjustment.value.id,
        loadingItem: loading,
        snackbar,
        onSuccess: (data) => {
            snackbar.value.text = labels.delete_message;
            loadData();
            dialogDelete.value = false;
        },
    });
}

function loadData() {
    api.get({
        url: "/adjustments/list",
        loadingItem: loading,
        onSuccess: (data) => {
            console.log(data);
            warehouses.value = data.warehouses;
            adjustments.value = data.adjustments;
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
        <!-- Modal Remove Adjustment -->
        <delete-dialog
            v-model="dialogDelete"
            :on-save="Remove_Adjustment"
            :on-close="onCloseDelete"
        ></delete-dialog>
        <!-- Show details -->
        <v-dialog v-model="dialogDetail" max-width="800px">
            <v-card>
                <v-toolbar border>
                    <v-toolbar-title>Detalle de Ajuste</v-toolbar-title>
                    <v-spacer></v-spacer>
                    <v-btn
                        icon="fas fa-times"
                        size="small"
                        variant="tonal"
                        @click="dialogDetail = false"
                    ></v-btn>
                </v-toolbar>
                <v-card-text>
                    <v-row>
                        <v-col cols="12" md="5">
                            <v-card variant="outlined">
                                <v-table hover>
                                    <tbody>
                                        <!-- date -->
                                        <tr>
                                            <td>Fecha</td>
                                            <td class="font-weight-bold">
                                                {{
                                                    helpers.formatDate(
                                                        adjustment.date
                                                    )
                                                }}
                                            </td>
                                        </tr>
                                        <!-- Reference -->
                                        <tr>
                                            <td>Codigo</td>
                                            <td class="font-weight-bold">
                                                {{ adjustment.Ref }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <!-- warehouse -->
                                            <td>Ajencia</td>
                                            <td class="font-weight-bold">
                                                {{ adjustment.warehouse }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </v-table>
                            </v-card>
                        </v-col>
                        <v-col cols="12" md="7">
                            <v-card variant="outlined">
                                <v-table hover>
                                    <thead>
                                        <tr>
                                            <th>Producto</th>
                                            <th>Codigo</th>
                                            <th>Cantidad</th>
                                            <th>Tipo</th>
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
                                            <td v-if="detail.type === 'add'">
                                                Añadido
                                            </td>
                                            <td v-if="detail.type === 'sub'">
                                                Quitado
                                            </td>
                                        </tr>
                                    </tbody>
                                </v-table>
                            </v-card>
                        </v-col>
                    </v-row>
                </v-card-text>
                <v-divider v-if="adjustment.note"></v-divider>
                <v-card-actions>
                    <v-row>
                        <v-col cols="12">{{ adjustment.note }}</v-col></v-row
                    >
                </v-card-actions>
            </v-card>
        </v-dialog>

        <v-row align="center">
            <v-col>
                <v-text-field
                    v-model="search"
                    prepend-icon="fas fa-search"
                    hide-details
                    :label="labels.search"
                    single-line
                    variant="underlined"
                ></v-text-field>
            </v-col>
            <v-spacer></v-spacer>
            <v-col cols="auto" class="text-right">
                <ExportBtn
                    :data="adjustments"
                    :fields="jsonFields"
                    name-file="Ajuste Stock"
                ></ExportBtn>
                <v-btn
                    v-if="globals.userPermision(['adjustment_add'])"
                    color="primary"
                    class="ma-1"
                    prepend-icon="fas fa-user-plus"
                    @click="router.visit('/adjustments/create')"
                >
                    {{ labels.add }}
                </v-btn>
            </v-col>
        </v-row>
        <v-row>
            <v-col>
                <v-data-table
                    :headers="fields"
                    :items="adjustments"
                    :search="search"
                    hover
                    class="elevation-2"
                    :no-data-text="labels.no_data_table"
                >
                    <template v-slot:item.actions="{ item }">
                        <v-btn
                            v-if="globals.userPermision(['adjustment_view'])"
                            class="ma-1"
                            color="info"
                            icon="fas fa-eye"
                            size="x-small"
                            variant="outlined"
                            @click="showDetails(item.id)"
                        >
                        </v-btn>
                        <v-btn
                            v-if="globals.userPermision(['adjustment_edit'])"
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
                            @click="
                                router.visit('/adjustments/edit/' + item.id)
                            "
                        >
                        </v-btn>
                        <v-btn
                            v-if="globals.userPermision(['adjustment_delete'])"
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
    </layout>
</template>
