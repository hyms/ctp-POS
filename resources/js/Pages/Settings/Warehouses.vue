<script setup>
import { ref } from "vue";
import Layout from "@/Layouts/Authenticated.vue";
import Snackbar from "@/Components/snackbar.vue";
import ruleForm from "@/rules";
import { router } from "@inertiajs/vue3";
import DeleteDialog from "@/Components/DeleteDialog.vue";

const props = defineProps({
    warehouses: Array,
    errors: Object,
});

//declare variable
const form = ref(null);
const search = ref("");
const loading = ref(false);
const snackbar = ref(false);
const snackbarText = ref("");
const snackbarColor = ref("info");
const dialog = ref(false);
const dialogDelete = ref(false);
const editmode = ref(false);

const fields = ref([
    { title: "Nombre", key: "name" },
    { title: "Tefono", key: "mobile" },
    { title: "Ciudad", key: "city" },
    { title: "Correo", key: "email" },
    { title: "Acciones", key: "actions" },
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
const warehouseLabels = ref({
    name: "Nombre",
    mobile: "Telefono",
    email: "Correo",
    country: "Pais",
    city: "Ciudad",
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
    loading.value = true;
    snackbar.value = false;
    axios
        .post("warehouses", {
            name: warehouse.value.name,
            mobile: warehouse.value.mobile,
            email: warehouse.value.email,
            city: warehouse.value.city,
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
            snackbarText.value = error.response.data.message;
        })
        .finally(() => {
            setTimeout(() => {
                loading.value = false;
            }, 1000);
        });
}

//------------------------------- Update Warehouse ------------------------\\
function Update_Warehouse() {
    loading.value = true;
    snackbar.value = false;
    axios
        .put("warehouses/" + warehouse.value.id, {
            name: warehouse.value.name,
            mobile: warehouse.value.mobile,
            email: warehouse.value.email,
            city: warehouse.value.city,
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
            snackbarText.value = error.response.data.message;
        })
        .finally(() => {
            setTimeout(() => {
                loading.value = false;
            }, 1000);
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
    loading.value = true;
    snackbar.value = false;
    axios
        .delete("warehouses/" + warehouse.value.id)
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
            setTimeout(() => {
                loading.value = false;
            }, 1000);
        });
}

function onCloseDelete() {
    reset_Form();
    dialogDelete.value = false;
}
</script>

<template>
    <Layout :loading="loading">
        <snackbar
            :snackbar="snackbar"
            :snackbar-color="snackbarColor"
            :snackbar-text="snackbarText"
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
                <v-toolbar
                    border
                    density="comfortable"
                    :title="(editmode ? 'Modificar' : 'Nuevo') + ' Almacen'"
                >
                </v-toolbar>

                <v-card-text>
                    <v-form ref="form">
                        <v-row>
                            <!-- First name -->
                            <v-col md="6" sm="12">
                                <v-text-field
                                    :label="warehouseLabels.name + ' *'"
                                    v-model="warehouse.name"
                                    :placeholder="warehouseLabels.name"
                                    :rules="ruleForm.required"
                                    variant="outlined"
                                    density="comfortable"
                                    hide-details="auto"
                                >
                                </v-text-field>
                            </v-col>

                            <!-- Last name -->
                            <v-col md="6" sm="12">
                                <v-text-field
                                    :label="warehouseLabels.mobile"
                                    v-model="warehouse.mobile"
                                    :placeholder="warehouseLabels.mobile"
                                    variant="outlined"
                                    density="comfortable"
                                    hide-details="auto"
                                >
                                </v-text-field>
                            </v-col>

                            <!-- Username -->
                            <v-col md="6" sm="12">
                                <v-text-field
                                    :label="warehouseLabels.city"
                                    v-model="warehouse.city"
                                    :placeholder="warehouseLabels.city"
                                    variant="outlined"
                                    density="comfortable"
                                    hide-details="auto"
                                >
                                </v-text-field>
                            </v-col>

                            <!-- Phone -->
                            <v-col md="6" sm="12">
                                <v-text-field
                                    :label="warehouseLabels.email"
                                    v-model="warehouse.email"
                                    :placeholder="warehouseLabels.email"
                                    variant="outlined"
                                    density="comfortable"
                                    hide-details="auto"
                                >
                                </v-text-field>
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
                        variant="elevated"
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
                    color="primary"
                    class="ma-1"
                    prepend-icon="mdi-account-plus"
                    @click="New_Warehouse"
                >
                    Añadir
                </v-btn>
            </v-col>
        </v-row>
        <v-row>
            <v-col>
                <v-data-table
                    :headers="fields"
                    :items="warehouses"
                    :search="search"
                    hover
                    class="elevation-2"
                    density="compact"
                    no-data-text="No existen datos a mostrar"
                >
                    <template v-slot:item.actions="{ item }">
                        <v-btn
                            class="ma-1"
                            color="primary"
                            icon="mdi-pencil"
                            size="x-small"
                            variant="outlined"
                            @click="Edit_Warehouse(item.raw)"
                        >
                        </v-btn>
                        <v-btn
                            class="ma-1"
                            color="error"
                            icon="mdi-delete"
                            size="x-small"
                            variant="outlined"
                            @click="Delete_Warehouse(item.raw)"
                        >
                        </v-btn>
                    </template>
                </v-data-table>
            </v-col>
        </v-row>
    </Layout>
</template>
