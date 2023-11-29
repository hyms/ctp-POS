<script setup>
import { ref } from "vue";
import Layout from "@/Layouts/Authenticated.vue";
import Snackbar from "@/Components/Snackbar2.vue";
import helper from "@/helpers";
import labels from "@/labels";
import DeleteDialog from "@/Components/buttons/DeleteDialog.vue";
import api from "@/api";

const props = defineProps({
    warehouses: Array,
    errors: Object,
});

//declare variable
const form = ref(null);
const search = ref("");
const loading = ref(false);
const snackbar = ref({
    view: false,
    color: "",
    text: "",
});
const dialog = ref(false);
const dialogDelete = ref(false);
const editmode = ref(false);

const fields = ref([
    { title: labels.warehouse.name, key: "name" },
    { title: labels.warehouse.mobile, key: "mobile" },
    { title: labels.warehouse.city, key: "city" },
    { title: labels.warehouse.email, key: "email" },
    { title: labels.actions, key: "actions" },
]);

//form
const warehouse = ref({
    id: "",
    name: "",
    mobile: "",
    email: "",
    country: "",
    city: "",
});

//------------------------------ Modal (create Warehouse) -------------------------------\\
function New_Warehouse() {
    reset_Form();
    editmode.value = false;
    dialog.value = true;
}

//------------------------------ Modal (Update Warehouse) -------------------------------\\
function Edit_Warehouse(item) {
    reset_Form();
    warehouse.value = item;
    editmode.value = true;
    dialog.value = true;
}

//------------------------------- Create Warehouse ------------------------\\
function Create_Warehouse() {
    api.post({
        url: "/warehouses",
        params: {
            name: warehouse.value.name,
            mobile: warehouse.value.mobile,
            email: warehouse.value.email,
            city: warehouse.value.city,
        },
        loadingItem: loading,
        snackbar,
    });
}

//------------------------------- Update Warehouse ------------------------\\
function Update_Warehouse() {
    api.put({
        url: "/warehouses/" + warehouse.value.id,
        params: {
            name: warehouse.value.name,
            mobile: warehouse.value.mobile,
            email: warehouse.value.email,
            city: warehouse.value.city,
        },
        loadingItem: loading,
        snackbar,
        Success: () => {
            dialog.value = false;
        },
    });
}

//------------------------------- reset Form ------------------------\\
function reset_Form() {
    warehouse.value = {
        id: "",
        name: "",
        mobile: "",
        email: "",
        country: "",
        city: "",
    };
}

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

//---------------------- delete modal  ------------------------------\\
function Delete_Warehouse(item) {
    reset_Form();
    warehouse.value = item;
    dialogDelete.value = true;
}

//------------------------------- Delete Warehouse ------------------------\\
function Remove_Warehouse() {
    api.delete({
        url: "/warehouses/" + warehouse.value.id,
        loadingItem: loading,
        snackbar,
        Success: () => {
            dialog.value = false;
        },
    });
}

function onCloseDelete() {
    reset_Form();
    dialogDelete.value = false;
}
</script>

<template>
    <Layout>
        <snackbar
            v-model="snackbar.view"
            :text="snackbar.text"
            :color="snackbar.color"
        ></snackbar>
        <delete-dialog
            :model="dialogDelete"
            :on-save="Remove_Warehouse"
            :on-close="onCloseDelete"
        >
        </delete-dialog>
        <v-dialog
            v-model="dialog"
            max-width="600px"
            scrollable
            @update:modelValue="dialog === false ? reset_Form() : dialog"
        >
            <v-card>
                <v-form ref="form" @submit.prevent="onSave">
                    <v-toolbar
                        border
                        :title="(editmode ? 'Modificar' : 'Nuevo') + ' Agencia'"
                    >
                    </v-toolbar>

                    <v-card-text>
                        <v-row>
                            <!-- First name -->
                            <v-col cols="12" md="6">
                                <v-text-field
                                    :label="labels.warehouse.name + ' *'"
                                    v-model="warehouse.name"
                                    :placeholder="labels.warehouse.name"
                                    :rules="helper.required"
                                    hide-details="auto"
                                >
                                </v-text-field>
                            </v-col>

                            <!-- Last name -->
                            <v-col cols="12" md="6">
                                <v-text-field
                                    :label="labels.warehouse.mobile"
                                    v-model="warehouse.mobile"
                                    :placeholder="labels.warehouse.mobile"
                                    hide-details="auto"
                                >
                                </v-text-field>
                            </v-col>

                            <!-- Username -->
                            <v-col cols="12" md="6">
                                <v-text-field
                                    :label="labels.warehouse.city"
                                    v-model="warehouse.city"
                                    :placeholder="labels.warehouse.city"
                                    hide-details="auto"
                                >
                                </v-text-field>
                            </v-col>

                            <!-- Phone -->
                            <v-col cols="12" md="6">
                                <v-text-field
                                    :label="labels.warehouse.email"
                                    v-model="warehouse.email"
                                    :placeholder="labels.warehouse.email"
                                    hide-details="auto"
                                >
                                </v-text-field>
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
                            type="submit"
                            color="primary"
                            variant="elevated"
                            class="ma-1"
                            @click="onSave"
                            :loading="loading"
                            :disabled="loading"
                        >
                            {{ labels.submit }}
                        </v-btn>
                    </v-card-actions>
                </v-form>
            </v-card>
        </v-dialog>
        <v-row>
            <v-col cols="12" sm="6">
                <v-text-field
                    v-model="search"
                    prepend-icon="mdi-magnify"
                    hide-details
                    :label="labels.search"
                    single-line
                    variant="underlined"
                ></v-text-field>
            </v-col>
            <v-col cols="12" sm="6" class="text-right">
                <v-btn
                    color="primary"
                    class="ma-1"
                    prepend-icon="mdi-account-plus"
                    @click="New_Warehouse"
                >
                    {{ labels.add }}
                </v-btn>
            </v-col>
            <v-col cols="12">
                <v-card>
                    <v-data-table
                        :headers="fields"
                        :items="warehouses"
                        :search="search"
                        hover
                        :no-data-text="labels.no_data_table"
                        :loading="loading"
                    >
                        <template v-slot:item.actions="{ item }">
                            <v-btn
                                class="ma-1"
                                color="primary"
                                icon="mdi-pencil"
                                size="x-small"
                                variant="outlined"
                                @click="Edit_Warehouse(item)"
                            >
                            </v-btn>
                            <v-btn
                                class="ma-1"
                                color="error"
                                icon="mdi-delete"
                                size="x-small"
                                variant="outlined"
                                @click="Delete_Warehouse(item)"
                            >
                            </v-btn>
                        </template>
                    </v-data-table>
                </v-card>
            </v-col>
        </v-row>
    </Layout>
</template>
