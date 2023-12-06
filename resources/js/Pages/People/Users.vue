<script setup>
import { ref } from "vue";
import Layout from "@/Layouts/Authenticated.vue";
import ExportBtn from "@/Components/buttons/ExportBtn.vue";
import Snackbar from "@/Components/Snackbar2.vue";

import {rules} from "@/helpers";
import labels from "@/labels";
import api from "@/api";

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
const alert = ref(false);
const snackbar = ref({
    view: false,
    color: "",
    text: "",
});
const dialog = ref(false);
const editmode = ref(false);

const fields = ref([
    { title: labels.user.username, key: "username" },
    { title: labels.user.lastname, key: "lastname" },
    { title: labels.user.firstname, key: "firstname" },
    { title: labels.user.phone, key: "phone" },
    { title: labels.user.statut, key: "statut" },
    { title: labels.actions, key: "actions" },
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
    is_all_warehouses: true,
});
const assigned_warehouses = ref([]);

//------ Checked Status User
function isChecked(user) {
    api.post({
        url: "/users_switch_activated/" + user.id,
        params: {
            statut: user.statut === 1,
        },
        loadingItem: loading,
        snackbar,
        Success: (data) => {
            if (data.success) {
                snackbar.value.color = "success";
                if (user.statut === 0) {
                    user.statut = 1;
                    snackbar.value.text = "Usuario activado";
                } else {
                    user.statut = 0;
                    snackbar.value.text = "Usuario desactivado";
                }
            } else {
                user.statut = 1;
                snackbar.value.color = "warning";
                snackbar.value.text = labels.warning_message;
            }
        },
    });
}

//------------------------ Create User ---------------------------\\
function Create_User() {
    let assignedWarehouses = [];
    // append array assigned_warehouses
    if (assigned_warehouses.value.length) {
        for (let i = 0; i < assigned_warehouses.value.length; i++) {
            assignedWarehouses[i] = assigned_warehouses.value[i];
        }
    }
    api.post({
        url: "/users",
        params: {
            firstname: user.value.firstname,
            lastname: user.value.lastname,
            username: user.value.username,
            email: user.value.email,
            password: user.value.password,
            phone: user.value.phone,
            role: user.value.role,
            ci: user.value.ci,
            is_all_warehouses: user.value.is_all_warehouses ? 1 : 0,
            assigned_to: assignedWarehouses,
        },
        loadingItem: loading,
        snackbar,
        Success: () => {
            dialog.value = false;
        },
    });
}

//----------------------- Update User ---------------------------\\
function Update_User() {
    let assignedWarehouses = [];
    // append array assigned_warehouses
    if (assigned_warehouses.value.length) {
        for (let i = 0; i < assigned_warehouses.value.length; i++) {
            assignedWarehouses[i] = assigned_warehouses.value[i];
        }
    }
    api.put({
        url: "/users/" + user.value.id,
        params: {
            firstname: user.value.firstname,
            lastname: user.value.lastname,
            username: user.value.username,
            email: user.value.email,
            NewPassword: user.value.NewPassword,
            phone: user.value.phone,
            role: user.value.role,
            ci: user.value.ci,
            statut: user.value.statut,
            is_all_warehouses: user.value.is_all_warehouses,
            assigned_to: assignedWarehouses,
        },
        loadingItem: loading,
        snackbar,
        Success: () => {
            dialog.value = false;
        },
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
        .get("/users/edit/"+id)
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
        <snackbar
            :text="snackbar.text"
            v-model="snackbar.view"
            :color="snackbar.color"
        ></snackbar>
        <v-dialog
            v-model="dialog"
            max-width="600px"
            scrollable
            @update:modelValue="dialog === false ? reset_Form() : dialog"
        >
            <v-card>
                <v-form ref="form">
                    <v-toolbar
                        border
                        :title="(editmode ? 'Modificar' : 'Nuevo') + ' Usuario'"
                    >
                    </v-toolbar>
                    <v-card-text>
                        <v-row>
                            <!-- First name -->
                            <v-col cols="12" md="6">
                                <v-text-field
                                    :label="labels.user.firstname + ' *'"
                                    v-model="user.firstname"
                                    :placeholder="labels.user.firstname"
                                    :rules="
                                        rules.required
                                            .concat(rules.min(3))
                                            .concat(rules.max(30))
                                    "
                                    hide-details="auto"
                                >
                                </v-text-field>
                            </v-col>

                            <!-- Last name -->
                            <v-col cols="12" md="6">
                                <v-text-field
                                    :label="labels.user.lastname + ' *'"
                                    v-model="user.lastname"
                                    :placeholder="labels.user.lastname"
                                    :rules="
                                        rules.required
                                            .concat(rules.min(3))
                                            .concat(rules.max(30))
                                    "
                                    hide-details="auto"
                                >
                                </v-text-field>
                            </v-col>

                            <!-- Username -->
                            <v-col cols="12" md="6">
                                <v-text-field
                                    :label="labels.user.username + ' *'"
                                    v-model="user.username"
                                    :placeholder="labels.user.username"
                                    :rules="
                                        rules.required
                                            .concat(rules.min(3))
                                            .concat(rules.max(30))
                                    "
                                    hide-details="auto"
                                >
                                </v-text-field>
                            </v-col>

                            <!-- Phone -->
                            <v-col cols="12" md="6">
                                <v-text-field
                                    :label="labels.user.phone + ' *'"
                                    v-model="user.phone"
                                    :placeholder="labels.user.phone"
                                    :rules="
                                        rules.required.concat(rules.number)
                                    "
                                    hide-details="auto"
                                >
                                </v-text-field>
                            </v-col>

                            <!-- Email -->
                            <v-col cols="12" md="6">
                                <v-text-field
                                    :label="labels.user.email + ' *'"
                                    v-model="user.email"
                                    :placeholder="labels.user.email"
                                    :rules="rules.required"
                                    hide-details="auto"
                                    type="mail"
                                >
                                </v-text-field>
                            </v-col>

                            <!-- password -->
                            <v-col md="6" sm="12" v-if="!editmode">
                                <v-text-field
                                    :label="labels.user.password + ' *'"
                                    v-model="user.password"
                                    :placeholder="labels.user.password"
                                    :rules="
                                        rules.required
                                            .concat(rules.min(6))
                                            .concat(rules.max(14))
                                    "
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
                                    :rules="rules.required"
                                    :label="labels.user.role"
                                    item-title="name"
                                    item-value="id"
                                    hide-details="auto"
                                ></v-select>
                            </v-col>

                            <!-- New Password -->
                            <v-col cols="12" md="6" v-if="editmode">
                                <v-text-field
                                    :label="labels.user.NewPassword + ' *'"
                                    v-model="user.NewPassword"
                                    :placeholder="labels.user.NewPassword"
                                    :rules="
                                        rules.min(6).concat(rules.max(14))
                                    "
                                    hide-details="auto"
                                    type="password"
                                >
                                </v-text-field>
                            </v-col>
                            <v-col cols="12" md="6">
                                <v-text-field
                                    :label="labels.user.ci"
                                    v-model="user.ci"
                                    :placeholder="labels.user.ci"
                                    :rules="rules.number"
                                    hide-details="auto"
                                >
                                </v-text-field>
                            </v-col>
                            <!-- assigned_warehouses -->
                            <v-col cols="12" sm="4">
                                <p class="font-weight-bold mt-3">
                                    {{ labels.warehouse_assign }}
                                </p>
                            </v-col>

                            <v-col cols="12" sm="8">
                                <v-checkbox
                                    v-model="user.is_all_warehouses"
                                    :model-value="!!user.is_all_warehouses"
                                    :label="labels.user.is_all_warehouses"
                                    hide-details="auto"
                                ></v-checkbox>
                                <v-select
                                    v-model="assigned_warehouses"
                                    :items="warehouses"
                                    :label="labels.warehouse_some"
                                    item-title="name"
                                    item-value="id"
                                    multiple
                                    chips
                                    hide-details="auto"
                                ></v-select>
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
                            variant="flat"
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

        <v-row align="center">
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
            <v-spacer></v-spacer>
            <v-col cols="12" sm="auto" class="text-right">
                <ExportBtn
                    :data="users"
                    :fields="jsonFields"
                    name-file="Usuarios"
                ></ExportBtn>
                <v-btn
                    color="primary"
                    class="ma-1"
                    prepend-icon="mdi-account-plus"
                    @click="New_User"
                >
                    {{ labels.add }}
                </v-btn>
            </v-col>
            <v-col cols="12">
                <v-data-table
                    :headers="fields"
                    :items="users"
                    :search="search"
                    hover
                    class="elevation-2"
                    :no-data-text="labels.no_data_table"
                    :loading="loading"
                >
                    <template v-slot:item.statut="{ item }">
                        <v-switch
                            :model-value="!!item.statut"
                            color="primary"
                            hide-details
                            @change="isChecked(item)"
                        ></v-switch>
                    </template>
                    <template v-slot:item.actions="{ item }">
                        <v-btn
                            class="ma-1"
                            color="primary"
                            icon="mdi-pencil"
                            size="x-small"
                            variant="outlined"
                            @click="Edit_User(item)"
                        >
                        </v-btn>
                    </template>
                </v-data-table>
            </v-col>
        </v-row>
    </Layout>
</template>
