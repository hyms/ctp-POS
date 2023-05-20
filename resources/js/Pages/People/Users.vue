<script setup>
import { ref } from "vue";
import Layout from "@/Layouts/Authenticated.vue";
import Snackbar from "@/Components/snackbar.vue";
import ExportBtn from "@/Components/ExportBtn.vue";

import helper from "@/helpers";
import { router } from "@inertiajs/vue3";

const props = defineProps({
    users: Array,
    warehouses: Array,
    roles: Array,
    errors: Object,
});

//declare variable
const form = ref(null);
const search = ref("");
const loading = ref(false);
const alertType = ref("info");
const alertText = ref("");
const alert = ref(false);
const snackbar = ref(false);
const snackbarText = ref("");
const snackbarColor = ref("info");
const dialog = ref(false);
const editmode = ref(false);

const fields = ref([
    { title: "Usuario", key: "username" },
    { title: "Apellido", key: "lastname" },
    { title: "Nombre", key: "firstname" },
    { title: "Telefono", key: "phone" },
    { title: "Estado", key: "statut" },
    { title: "Acciones", key: "actions" },
]);
const jsonFields = ref({
    Usuario: "username",
    Apellido: "lastname",
    Nombre: "firstname",
    Telefono: "phone",
    Estado: "statut",
});
//form
const user = ref({
    firstname: "",
    lastname: "",
    username: "",
    password: "",
    NewPassword: null,
    email: "",
    phone: "",
    ci: "",
    statut: "",
    role: "",
    is_all_warehouses: 1,
});
const assigned_warehouses = ref([]);
const userLabels = ref({
    firstname: "Nombre",
    lastname: "Apellido",
    username: "Usuario",
    password: "Contraseña",
    NewPassword: "Nueva Contraseña",
    email: "Correo",
    phone: "Telefono",
    ci: "CI",
    statut: "Estado",
    role: "Role",
    is_all_warehouses: "Todas las sucursales",
});

//------ Checked Status User
function isChecked(user) {
    loading.value = true;
    snackbar.value = false;
    axios
        .post("users_switch_activated/" + user.id, {
            statut: !!!user.statut,
            id: user.id,
        })
        .then(({ data }) => {
            snackbar.value = true;
            if (data.success) {
                snackbarColor.value = "success";
                if (!!!user.statut) {
                    user.statut = 1;
                    snackbarText.value = "Usuario activado";
                } else {
                    user.statut = 0;
                    snackbarText.value = "Usuario desactivado";
                }
            } else {
                user.statut = 1;
                snackbarColor.value = "warning";
                snackbarText.value = "algo salio mal";
            }
        })
        .catch((errors) => {
            user.statut = 1;
            snackbar.value = true;
            snackbarColor.value = "warning";
            snackbarText.value = "algo salio mal";
        })
        .finally(() => {
            setTimeout(() => {
                loading.value = false;
            }, 1000);
        });
}

//------------------------ Create User ---------------------------\\
function Create_User() {
    loading.value = true;
    snackbar.value = false;
    let data = new FormData();
    data.append("firstname", user.value.firstname);
    data.append("lastname", user.value.lastname);
    data.append("username", user.value.username);
    data.append("email", user.value.email);
    data.append("password", user.value.password);
    data.append("phone", user.value.phone);
    data.append("role", user.value.role);
    data.append("ci", user.value.ci);
    data.append("is_all_warehouses", user.value.is_all_warehouses);

    // append array assigned_warehouses
    if (assigned_warehouses.value.length) {
        for (let i = 0; i < assigned_warehouses.value.length; i++) {
            data.append("assigned_to[" + i + "]", assigned_warehouses.value[i]);
        }
    } else {
        data.append("assigned_to", []);
    }
    axios
        .post("users", data)
        .then(({ data }) => {
            dialog.value = false;
            snackbar.value = true;
            snackbarColor.value = "success";
            snackbarText.value = "Proceso exitoso";
            router.reload();
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

//----------------------- Update User ---------------------------\\
function Update_User() {
    snackbar.value = false;
    loading.value = true;
    let data = new FormData();

    data.append("firstname", user.value.firstname);
    data.append("lastname", user.value.lastname);
    data.append("username", user.value.username);
    data.append("email", user.value.email);
    data.append("NewPassword", user.value.NewPassword);
    data.append("phone", user.value.phone);
    data.append("role", user.value.role);
    data.append("ci", user.value.ci);
    data.append("statut", user.value.statut);
    data.append("is_all_warehouses", user.value.is_all_warehouses);
    // append array assigned_warehouses
    if (assigned_warehouses.value.length) {
        for (let i = 0; i < assigned_warehouses.value.length; i++) {
            data.append("assigned_to[" + i + "]", assigned_warehouses.value[i]);
        }
    } else {
        data.append("assigned_to", []);
    }
    data.append("_method", "put");
    axios
        .post("users/" + user.value.id, data)
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

//----------------------------- Reset Form ---------------------------\\
function reset_Form() {
    user.value = {
        id: "",
        firstname: "",
        lastname: "",
        username: "",
        password: "",
        NewPassword: null,
        email: "",
        phone: "",
        ci: "",
        statut: "",
        role: "",
        is_all_warehouses: 1,
    };
    assigned_warehouses.value = [];
}

//------------------------------ Show Modal (Create User) -------------------------------\\
function New_User() {
    reset_Form();
    editmode.value = false;
    dialog.value = true;
}

//------------------------------ Show Modal (Update User) -------------------------------\\
function Edit_User(item) {
    reset_Form();
    Get_Data_Warehouses(item.id);
    user.value = item;
    user.value.NewPassword = null;
    editmode.value = true;
    dialog.value = true;
}

//---------------------- Get_Data_Warehouses  ------------------------------\\
function Get_Data_Warehouses(id) {
    axios
        .get("/users/" + id + "/edit")
        .then(({ data }) => {
            assigned_warehouses.value = data.assigned_warehouses;
        })
        .catch((error) => {});
}

//---------------------- modal  ------------------------------\\
async function onSave() {
    const validate = await form.value.validate();
    if (validate.valid)
        if (!editmode.value) {
            Create_User();
        } else {
            Update_User();
        }
}

function onClose() {
    dialog.value = false;
    reset_Form();
}
</script>

<template>
    <Layout>
        <Snackbar
            :snackbar="snackbar"
            :snackbar-color="snackbarColor"
            :snackbar-text="snackbarText"
        ></Snackbar>
        <v-dialog
            v-model="dialog"
            max-width="600px"
            scrollable
            @update:modelValue="dialog === false ? reset_Form() : dialog"
        >
            <v-card>
                <v-toolbar
                    border
                    density="compact"
                    :title="(editmode ? 'Modificar' : 'Nuevo') + ' Usuario'"
                >
                </v-toolbar>

                <v-card-text>
                    <v-form ref="form">
                        <v-row>
                            <!-- First name -->
                            <v-col cols="12" md="6">
                                <v-text-field
                                    :label="userLabels.firstname + ' *'"
                                    v-model="user.firstname"
                                    :placeholder="userLabels.firstname"
                                    :rules="
                                        helper.required
                                            .concat(helper.min(3))
                                            .concat(helper.max(30))
                                    "
                                    variant="outlined"
                                    density="comfortable"
                                    hide-details="auto"
                                >
                                </v-text-field>
                            </v-col>

                            <!-- Last name -->
                            <v-col cols="12" md="6">
                                <v-text-field
                                    :label="userLabels.lastname + ' *'"
                                    v-model="user.lastname"
                                    :placeholder="userLabels.lastname"
                                    :rules="
                                        helper.required
                                            .concat(helper.min(3))
                                            .concat(helper.max(30))
                                    "
                                    variant="outlined"
                                    density="comfortable"
                                    hide-details="auto"
                                >
                                </v-text-field>
                            </v-col>

                            <!-- Username -->
                            <v-col cols="12" md="6">
                                <v-text-field
                                    :label="userLabels.username + ' *'"
                                    v-model="user.username"
                                    :placeholder="userLabels.username"
                                    :rules="
                                        helper.required
                                            .concat(helper.min(3))
                                            .concat(helper.max(30))
                                    "
                                    variant="outlined"
                                    density="comfortable"
                                    hide-details="auto"
                                >
                                </v-text-field>
                            </v-col>

                            <!-- Phone -->
                            <v-col cols="12" md="6">
                                <v-text-field
                                    :label="userLabels.phone + ' *'"
                                    v-model="user.phone"
                                    :placeholder="userLabels.phone"
                                    :rules="
                                        helper.required.concat(helper.number)
                                    "
                                    variant="outlined"
                                    density="comfortable"
                                    hide-details="auto"
                                >
                                </v-text-field>
                            </v-col>

                            <!-- Email -->
                            <v-col cols="12" md="6">
                                <v-text-field
                                    :label="userLabels.email + ' *'"
                                    v-model="user.email"
                                    :placeholder="userLabels.email"
                                    :rules="helper.required"
                                    variant="outlined"
                                    density="comfortable"
                                    hide-details="auto"
                                    type="mail"
                                >
                                </v-text-field>
                            </v-col>

                            <!-- password -->
                            <v-col md="6" sm="12" v-if="!editmode">
                                <v-text-field
                                    :label="userLabels.password + ' *'"
                                    v-model="user.password"
                                    :placeholder="userLabels.password"
                                    :rules="
                                        helper.required
                                            .concat(helper.min(6))
                                            .concat(helper.max(14))
                                    "
                                    variant="outlined"
                                    density="comfortable"
                                    hide-details="auto"
                                    type="password"
                                >
                                </v-text-field>
                            </v-col>

                            <!-- role -->
                            <v-col cols="12" md="6">
                                <v-select
                                    v-model="user.role"
                                    :items="roles"
                                    :rules="helper.required"
                                    :label="userLabels.role"
                                    item-title="title"
                                    item-value="value"
                                    variant="outlined"
                                    density="comfortable"
                                    hide-details="auto"
                                ></v-select>
                            </v-col>

                            <!-- New Password -->
                            <v-col cols="12" md="6" v-if="editmode">
                                <v-text-field
                                    :label="userLabels.NewPassword + ' *'"
                                    v-model="user.NewPassword"
                                    :placeholder="userLabels.NewPassword"
                                    :rules="
                                        helper.min(6).concat(helper.max(14))
                                    "
                                    variant="outlined"
                                    density="comfortable"
                                    hide-details="auto"
                                    type="password"
                                >
                                </v-text-field>
                            </v-col>
                            <v-col cols="12" md="6">
                                <v-text-field
                                    :label="userLabels.ci"
                                    v-model="user.ci"
                                    :placeholder="userLabels.ci"
                                    :rules="helper.number"
                                    variant="outlined"
                                    density="comfortable"
                                    hide-details="auto"
                                >
                                </v-text-field>
                            </v-col>
                            <!-- assigned_warehouses -->
                            <v-col cols="12" sm="4">
                                <p class="font-weight-bold mt-3">
                                    Sucursales Asignadas
                                </p>
                            </v-col>

                            <v-col cols="12" sm="8">
                                <v-checkbox
                                    v-model="user.is_all_warehouses"
                                    :model-value="!!user.is_all_warehouses"
                                    :label="userLabels.is_all_warehouses"
                                    density="comfortable"
                                    hide-details="auto"
                                ></v-checkbox>
                                <v-select
                                    v-model="assigned_warehouses"
                                    :items="warehouses"
                                    label="Algunas sucursales"
                                    item-title="title"
                                    item-value="value"
                                    multiple
                                    chips
                                    variant="outlined"
                                    density="comfortable"
                                    hide-details="auto"
                                ></v-select>
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
                    :data="users"
                    :fields="jsonFields"
                    name-file="Usuarios"
                ></ExportBtn>
                <v-btn
                    size="small"
                    color="primary"
                    class="ma-1"
                    prepend-icon="mdi-account-plus"
                    @click="New_User"
                >
                    Añadir
                </v-btn>
            </v-col>
            <v-col cols="12">
                <v-data-table
                    :headers="fields"
                    :items="users"
                    :search="search"
                    hover
                    class="elevation-2"
                    density="compact"
                    no-data-text="No existen datos a mostrar"
                    :loading="loading"
                    loading-text="Cargando..."
                >
                    <template v-slot:item.statut="{ item }">
                        <v-switch
                            :model-value="!!item.raw.statut"
                            color="primary"
                            density="compact"
                            hide-details
                            @change="isChecked(item.raw)"
                        ></v-switch>
                    </template>
                    <template v-slot:item.actions="{ item }">
                        <v-btn
                            class="ma-1"
                            color="primary"
                            icon="mdi-pencil"
                            size="x-small"
                            variant="outlined"
                            @click="Edit_User(item.raw)"
                        >
                        </v-btn>
                    </template>
                </v-data-table>
            </v-col>
        </v-row>
    </Layout>
</template>
