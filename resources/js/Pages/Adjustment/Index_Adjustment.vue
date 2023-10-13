<script setup>
import {ref} from "vue";
import Layout from "@/Layouts/Authenticated.vue";
import ExportBtn from "@/Components/buttons/ExportBtn.vue";
import DeleteDialog from "@/Components/buttons/DeleteDialog.vue";
import {router} from "@inertiajs/vue3";
import helper from "@/helpers";
import labels from "@/labels";

const props = defineProps({
    warehouses: Array,
    adjustments: Array,
    errors: Object,
});

const search = ref("");
const loading = ref(false);
const snackbar = ref(false);
const snackbarText = ref("");
const snackbarColor = ref("info");
const dialogDelete = ref(false);
const dialogDetail = ref(false);

const fields = ref([
    {title: labels.adjustment.date, key: "date"},
    {title: labels.adjustment.ref, key: "Ref"},
    {title: labels.adjustment.warehouse_id, key: "warehouse_name"},
    {title: labels.adjustment.items, key: "items"},
    {title: labels.actions, key: "actions"},
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
    loading.value = true;
    dialogDetail.value = false;
    axios
        .get("/adjustments/detail/" + id)
        .then(({data}) => {
            adjustment.value = data.adjustment;
            details.value = data.details;
            dialogDetail.value = true;
        })
        .catch((response) => {
            dialogDetail.value = true;
        })
        .finally(() => {
            loading.value = false;
        });
}

//-------------------------------- Reset Form -------------------------------\\
function reset_Form() {
    adjustment.value = {id: ""};
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
    snackbar.value = false;
    axios
        .delete("/adjustments/" + adjustment.value.id)
        .then(({data}) => {
            snackbar.value = true;
            snackbarColor.value = "success";
            snackbarText.value = labels.delete_message;
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
</script>
<template>
    <layout :loading="loading"
            :snackbar-view="snackbar"
            :snackbar-text="snackbarText"
            :snackbar-color="snackbarColor">
        <!-- Modal Remove Adjustment -->
        <delete-dialog
            :model="dialogDelete"
            :on-save="Remove_Adjustment"
            :on-close="onCloseDelete"
        ></delete-dialog>
        <!-- Show details -->
        <v-dialog v-model="dialogDetail" max-width="800px">
            <v-card>
                <v-toolbar border>
                    <v-toolbar-title>Detalle de Ajuste</v-toolbar-title>
                    <v-spacer></v-spacer>
                    <v-btn icon="mdi-close" size="small" variant="tonal"
                           @click="dialogDetail = false"></v-btn>
                </v-toolbar>
                <v-card-text>
                    <v-row>
                        <v-col cols="12" lg="5" md="12" sm="12">
                            <v-card variant="outlined">
                                <v-table hover>
                                    <tbody>
                                    <!-- date -->
                                    <tr>
                                        <td>Fecha</td>
                                        <td class="font-weight-bold">
                                            {{
                                                helper.formatDate(
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
                        <v-col cols="12" lg="7" md="12" sm="12">
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
                                                helper.formatNumber(
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
                    <hr v-if="adjustment.note" class="mt-4 mb-4"/>
                    <v-row>
                        <v-col md="12">
                            <p>{{ adjustment.note }}</p>
                        </v-col>
                    </v-row>
                </v-card-text>
            </v-card>
        </v-dialog>

        <v-row align="center">
            <v-col>
                <v-text-field
                    v-model="search"
                    prepend-icon="mdi-magnify"
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
                    name-file="Productos"
                ></ExportBtn>
                <v-btn
                    color="primary"
                    class="ma-1"
                    prepend-icon="mdi-account-plus"
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
                            class="ma-1"
                            color="info"
                            icon="mdi-eye"
                            size="x-small"
                            variant="outlined"
                            @click="showDetails(item.id)"
                        >
                        </v-btn>
                        <v-btn
                            class="ma-1"
                            color="primary"
                            icon="mdi-pencil"
                            size="x-small"
                            variant="outlined"
                            :disabled="helper.enableDay(item.updated_at)"
                            @click="router.visit('/adjustments/edit/' + item.id)"
                        >
                        </v-btn>
                        <v-btn
                            class="ma-1"
                            color="error"
                            icon="mdi-delete"
                            size="x-small"
                            variant="outlined"
                            :disabled="helper.enableDay(item.updated_at)"
                            @click="Delete_Item(item)"
                        >
                        </v-btn>
                    </template>
                </v-data-table>
            </v-col>
        </v-row>
    </layout>
</template>
