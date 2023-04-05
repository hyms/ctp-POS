<script setup>
import { ref } from "vue";
import Layout from "@/Layouts/Authenticated.vue";
import { VDataTableServer } from "vuetify/labs/VDataTable";
import JsonExcel from "vue-json-excel3";
import jsPDF from "jspdf";
import "jspdf-autotable";

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
    { title: "Acciones", key: "accions" },
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
    statut: "",
    role_id: "",
    is_all_warehouses: 1,
});
const assigned_warehouses = ref([]);
const userRules = ref({
    firstname: [
        (v) => !!v || "Nombre es requerido",
        (v) =>
            (v && v.length >= 3) || "Nombre tiene que ser mayor 3 caracteres",
        (v) =>
            (v && v.length <= 30) || "Nombre tiene que ser menor 30 caracteres",
    ],
    lastname: [
        (v) => !!v || "Apellido es requerido",
        (v) =>
            (v && v.length >= 3) || "Apellido tiene que ser mayor 3 caracteres",
        (v) =>
            (v && v.length <= 30) ||
            "Apellido tiene que ser menor 30 caracteres",
    ],
    username: "",
    password: "",
    NewPassword: null,
    email: "",
    phone: "",
    statut: "",
    role_id: "",
    is_all_warehouses: 1,
});
//------ Checked Status User
function isChecked(user) {
    loading.value = true;
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
            loading.value = false;
        });
}

function Selected_Warehouse(value) {
    if (!value.length) {
        assigned_warehouses.value = [];
    }
}

//--------------------------- Users PDF ---------------------------\\
function Users_PDF() {
    let pdf = new jsPDF("p", "pt");
    let columns = [
        { title: "Nombre", dataKey: "firstname" },
        { title: "Apellido", dataKey: "lastname" },
        { title: "Usuario", dataKey: "username" },
        { title: "Telefono", dataKey: "phone" },
    ];
    pdf.autoTable(columns, props.users);
    pdf.text("Lista de Usuarios", 40, 25);
    pdf.save("Lista_Usuarios.pdf");
}

//------------------------ Create User ---------------------------\\
function Create_User() {
    var self = this;
    self.SubmitProcessing = true;
    self.data.append("firstname", self.user.firstname);
    self.data.append("lastname", self.user.lastname);
    self.data.append("username", self.user.username);
    self.data.append("email", self.user.email);
    self.data.append("password", self.user.password);
    self.data.append("phone", self.user.phone);
    self.data.append("role", self.user.role_id);
    self.data.append("is_all_warehouses", self.user.is_all_warehouses);
    self.data.append("avatar", self.user.avatar);

    // append array assigned_warehouses
    if (self.assigned_warehouses.length) {
        for (var i = 0; i < self.assigned_warehouses.length; i++) {
            self.data.append(
                "assigned_to[" + i + "]",
                self.assigned_warehouses[i]
            );
        }
    } else {
        self.data.append("assigned_to", []);
    }

    axios
        .post("users", self.data)
        .then((response) => {
            self.SubmitProcessing = false;
            Fire.$emit("Event_User");

            this.makeToast(
                "success",
                this.$t("Create.TitleUser"),
                this.$t("Success")
            );
        })
        .catch((error) => {
            self.SubmitProcessing = false;
            if (error.errors.email.length > 0) {
                self.email_exist = error.errors.email[0];
            }
            this.makeToast("danger", this.$t("InvalidData"), this.$t("Failed"));
        });
}

//----------------------- Update User ---------------------------\\
function Update_User() {
    var self = this;
    self.SubmitProcessing = true;
    self.data.append("firstname", self.user.firstname);
    self.data.append("lastname", self.user.lastname);
    self.data.append("username", self.user.username);
    self.data.append("email", self.user.email);
    self.data.append("NewPassword", self.user.NewPassword);
    self.data.append("phone", self.user.phone);
    self.data.append("role", self.user.role_id);
    self.data.append("statut", self.user.statut);
    self.data.append("is_all_warehouses", self.user.is_all_warehouses);
    self.data.append("avatar", self.user.avatar);

    // append array assigned_warehouses
    if (self.assigned_warehouses.length) {
        for (var i = 0; i < self.assigned_warehouses.length; i++) {
            self.data.append(
                "assigned_to[" + i + "]",
                self.assigned_warehouses[i]
            );
        }
    } else {
        self.data.append("assigned_to", []);
    }
    self.data.append("_method", "put");

    axios
        .post("users/" + this.user.id, self.data)
        .then((response) => {
            this.makeToast(
                "success",
                this.$t("Update.TitleUser"),
                this.$t("Success")
            );
            Fire.$emit("Event_User");
            self.SubmitProcessing = false;
        })
        .catch((error) => {
            if (error.errors.email.length > 0) {
                self.email_exist = error.errors.email[0];
            }
            this.makeToast("danger", this.$t("InvalidData"), this.$t("Failed"));
            self.SubmitProcessing = false;
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
        statut: "",
        role_id: "",
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
function Edit_User(userRaw) {
    reset_Form();
    Get_Data_Warehouses(userRaw.id);
    user.value = userRaw;
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
    if (validate.valid) {
        dialog.value = false;
    }
}

function onClose() {
    dialog.value = false;
    reset_Form();
}
</script>

<template>
    <Layout>
        <v-snackbar
            v-model="snackbar"
            :color="snackbarColor"
            :location="'top right'"
            :timeout="4000"
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
                    :title="(editmode ? 'Modificar' : 'Nuevo') + ' Usuario'"
                >
                </v-toolbar>

                <v-card-text>
                    <v-form ref="form">
                        <v-row>
                            <!-- First name -->
                            <v-col md="6" sm="12">
                                <v-text-field
                                    label="Nombre *"
                                    v-model="user.firstname"
                                    placeholder="Nombre"
                                    :rules="userRules.firstname"
                                    variant="outlined"
                                    density="comfortable"
                                >
                                </v-text-field>
                            </v-col>

                            <!-- Last name -->
                            <v-col md="6" sm="12">
                                <v-text-field
                                    label="Apellido *"
                                    v-model="user.lastname"
                                    placeholder="Apellido"
                                    :rules="userRules.lastname"
                                    variant="outlined"
                                    density="comfortable"
                                >
                                </v-text-field>
                            </v-col>

                            <!--                            &lt;!&ndash; Username &ndash;&gt;-->
                            <!--                            <b-col md="6" sm="12">-->
                            <!--                                <validation-provider-->
                            <!--                                    name="username"-->
                            <!--                                    :rules="{ required: true, min: 3, max: 30 }"-->
                            <!--                                    v-slot="validationContext"-->
                            <!--                                >-->
                            <!--                                    <b-form-group-->
                            <!--                                        :label="$t('username') + ' ' + '*'"-->
                            <!--                                    >-->
                            <!--                                        <b-form-input-->
                            <!--                                            :state="-->
                            <!--                                                getValidationState(-->
                            <!--                                                    validationContext-->
                            <!--                                                )-->
                            <!--                                            "-->
                            <!--                                            aria-describedby="username-feedback"-->
                            <!--                                            label="username"-->
                            <!--                                            v-model="user.username"-->
                            <!--                                            :placeholder="$t('username')"-->
                            <!--                                        ></b-form-input>-->
                            <!--                                        <b-form-invalid-feedback-->
                            <!--                                            id="username-feedback"-->
                            <!--                                            >{{ validationContext.errors[0] }}-->
                            <!--                                        </b-form-invalid-feedback>-->
                            <!--                                    </b-form-group>-->
                            <!--                                </validation-provider>-->
                            <!--                            </b-col>-->

                            <!--                            &lt;!&ndash; Phone &ndash;&gt;-->
                            <!--                            <b-col md="6" sm="12">-->
                            <!--                                <validation-provider-->
                            <!--                                    name="Phone"-->
                            <!--                                    :rules="{ required: true }"-->
                            <!--                                    v-slot="validationContext"-->
                            <!--                                >-->
                            <!--                                    <b-form-group-->
                            <!--                                        :label="$t('Phone') + ' ' + '*'"-->
                            <!--                                    >-->
                            <!--                                        <b-form-input-->
                            <!--                                            :state="-->
                            <!--                                                getValidationState(-->
                            <!--                                                    validationContext-->
                            <!--                                                )-->
                            <!--                                            "-->
                            <!--                                            aria-describedby="Phone-feedback"-->
                            <!--                                            label="Phone"-->
                            <!--                                            v-model="user.phone"-->
                            <!--                                            :placeholder="$t('Phone')"-->
                            <!--                                        ></b-form-input>-->
                            <!--                                        <b-form-invalid-feedback-->
                            <!--                                            id="Phone-feedback"-->
                            <!--                                            >{{ validationContext.errors[0] }}-->
                            <!--                                        </b-form-invalid-feedback>-->
                            <!--                                    </b-form-group>-->
                            <!--                                </validation-provider>-->
                            <!--                            </b-col>-->

                            <!--                            &lt;!&ndash; Email &ndash;&gt;-->
                            <!--                            <b-col md="6" sm="12">-->
                            <!--                                <validation-provider-->
                            <!--                                    name="Email"-->
                            <!--                                    :rules="{ required: true }"-->
                            <!--                                    v-slot="validationContext"-->
                            <!--                                >-->
                            <!--                                    <b-form-group-->
                            <!--                                        :label="$t('Email') + ' ' + '*'"-->
                            <!--                                    >-->
                            <!--                                        <b-form-input-->
                            <!--                                            :state="-->
                            <!--                                                getValidationState(-->
                            <!--                                                    validationContext-->
                            <!--                                                )-->
                            <!--                                            "-->
                            <!--                                            aria-describedby="Email-feedback"-->
                            <!--                                            label="Email"-->
                            <!--                                            v-model="user.email"-->
                            <!--                                            :placeholder="$t('Email')"-->
                            <!--                                        ></b-form-input>-->
                            <!--                                        <b-form-invalid-feedback-->
                            <!--                                            id="Email-feedback"-->
                            <!--                                            >{{ validationContext.errors[0] }}-->
                            <!--                                        </b-form-invalid-feedback>-->
                            <!--                                        <b-alert-->
                            <!--                                            show-->
                            <!--                                            variant="danger"-->
                            <!--                                            class="error mt-1"-->
                            <!--                                            v-if="email_exist != ''"-->
                            <!--                                            >{{ email_exist }}-->
                            <!--                                        </b-alert>-->
                            <!--                                    </b-form-group>-->
                            <!--                                </validation-provider>-->
                            <!--                            </b-col>-->

                            <!--                            &lt;!&ndash; password &ndash;&gt;-->
                            <!--                            <b-col md="6" sm="12" v-if="!editmode">-->
                            <!--                                <validation-provider-->
                            <!--                                    name="password"-->
                            <!--                                    :rules="{ required: true, min: 6, max: 14 }"-->
                            <!--                                    v-slot="validationContext"-->
                            <!--                                >-->
                            <!--                                    <b-form-group-->
                            <!--                                        :label="$t('password') + ' ' + '*'"-->
                            <!--                                    >-->
                            <!--                                        <b-form-input-->
                            <!--                                            :state="-->
                            <!--                                                getValidationState(-->
                            <!--                                                    validationContext-->
                            <!--                                                )-->
                            <!--                                            "-->
                            <!--                                            aria-describedby="password-feedback"-->
                            <!--                                            label="password"-->
                            <!--                                            type="password"-->
                            <!--                                            v-model="user.password"-->
                            <!--                                            :placeholder="$t('password')"-->
                            <!--                                        ></b-form-input>-->
                            <!--                                        <b-form-invalid-feedback-->
                            <!--                                            id="password-feedback"-->
                            <!--                                            >{{ validationContext.errors[0] }}-->
                            <!--                                        </b-form-invalid-feedback>-->
                            <!--                                    </b-form-group>-->
                            <!--                                </validation-provider>-->
                            <!--                            </b-col>-->

                            <!--                            &lt;!&ndash; role &ndash;&gt;-->
                            <!--                            <b-col md="6" sm="12" class="mb-3">-->
                            <!--                                <validation-provider-->
                            <!--                                    name="role"-->
                            <!--                                    :rules="{ required: true }"-->
                            <!--                                >-->
                            <!--                                    <b-form-group-->
                            <!--                                        slot-scope="{ valid, errors }"-->
                            <!--                                        :label="$t('RoleName') + ' ' + '*'"-->
                            <!--                                    >-->
                            <!--                                        <v-select-->
                            <!--                                            :class="{-->
                            <!--                                                'is-invalid': !!errors.length,-->
                            <!--                                            }"-->
                            <!--                                            :state="-->
                            <!--                                                errors[0]-->
                            <!--                                                    ? false-->
                            <!--                                                    : valid-->
                            <!--                                                    ? true-->
                            <!--                                                    : null-->
                            <!--                                            "-->
                            <!--                                            v-model="user.role_id"-->
                            <!--                                            :reduce="(label) => label.value"-->
                            <!--                                            :placeholder="$t('PleaseSelect')"-->
                            <!--                                            :options="-->
                            <!--                                                roles.map((roles) => ({-->
                            <!--                                                    label: roles.name,-->
                            <!--                                                    value: roles.id,-->
                            <!--                                                }))-->
                            <!--                                            "-->
                            <!--                                        />-->
                            <!--                                        <b-form-invalid-feedback-->
                            <!--                                            >{{ errors[0] }}-->
                            <!--                                        </b-form-invalid-feedback>-->
                            <!--                                    </b-form-group>-->
                            <!--                                </validation-provider>-->
                            <!--                            </b-col>-->

                            <!--                            &lt;!&ndash; Avatar &ndash;&gt;-->
                            <!--                            <b-col md="6" sm="12" class="mb-3">-->
                            <!--                                <validation-provider-->
                            <!--                                    name="Avatar"-->
                            <!--                                    ref="Avatar"-->
                            <!--                                    rules="mimes:image/*|size:200"-->
                            <!--                                >-->
                            <!--                                    <b-form-group-->
                            <!--                                        slot-scope="{ validate, valid, errors }"-->
                            <!--                                        :label="$t('UserImage')"-->
                            <!--                                    >-->
                            <!--                                        <input-->
                            <!--                                            :state="-->
                            <!--                                                errors[0]-->
                            <!--                                                    ? false-->
                            <!--                                                    : valid-->
                            <!--                                                    ? true-->
                            <!--                                                    : null-->
                            <!--                                            "-->
                            <!--                                            :class="{-->
                            <!--                                                'is-invalid': !!errors.length,-->
                            <!--                                            }"-->
                            <!--                                            @change="onFileSelected"-->
                            <!--                                            label="Choose Avatar"-->
                            <!--                                            type="file"-->
                            <!--                                        />-->
                            <!--                                        <b-form-invalid-feedback-->
                            <!--                                            id="Avatar-feedback"-->
                            <!--                                            >{{ errors[0] }}-->
                            <!--                                        </b-form-invalid-feedback>-->
                            <!--                                    </b-form-group>-->
                            <!--                                </validation-provider>-->
                            <!--                            </b-col>-->

                            <!--                            &lt;!&ndash; New Password &ndash;&gt;-->
                            <!--                            <b-col md="6" v-if="editmode" class="mb-3">-->
                            <!--                                <validation-provider-->
                            <!--                                    name="New password"-->
                            <!--                                    :rules="{ min: 6, max: 14 }"-->
                            <!--                                    v-slot="validationContext"-->
                            <!--                                >-->
                            <!--                                    <b-form-group :label="$t('Newpassword')">-->
                            <!--                                        <b-form-input-->
                            <!--                                            :state="-->
                            <!--                                                getValidationState(-->
                            <!--                                                    validationContext-->
                            <!--                                                )-->
                            <!--                                            "-->
                            <!--                                            aria-describedby="Nawpassword-feedback"-->
                            <!--                                            :placeholder="$t('LeaveBlank')"-->
                            <!--                                            label="New password"-->
                            <!--                                            v-model="user.NewPassword"-->
                            <!--                                        ></b-form-input>-->
                            <!--                                        <b-form-invalid-feedback-->
                            <!--                                            id="Nawpassword-feedback"-->
                            <!--                                            >{{ validationContext.errors[0] }}-->
                            <!--                                        </b-form-invalid-feedback>-->
                            <!--                                    </b-form-group>-->
                            <!--                                </validation-provider>-->
                            <!--                            </b-col>-->

                            <!--                            &lt;!&ndash; assigned_warehouses &ndash;&gt;-->
                            <!--                            <b-col md="4" sm="4">-->
                            <!--                                <h5>{{ $t("Assigned_warehouses") }}</h5>-->
                            <!--                            </b-col>-->

                            <!--                            <b-col md="8" sm="8">-->
                            <!--                                <label class="checkbox checkbox-primary mb-3"-->
                            <!--                                    ><input-->
                            <!--                                        type="checkbox"-->
                            <!--                                        v-model="user.is_all_warehouses" />-->
                            <!--                                    <h5>-->
                            <!--                                        {{ $t("All_Warehouses") }}-->
                            <!--                                        <i-->
                            <!--                                            v-b-tooltip.hover.bottom-->
                            <!--                                            title="If 'All Warehouses' Selected , User Can access all data for the selected Warehouses"-->
                            <!--                                            class="text-info font-weight-bold i-Speach-BubbleAsking"-->
                            <!--                                        ></i>-->
                            <!--                                    </h5>-->
                            <!--                                    <span class="checkmark"></span-->
                            <!--                                ></label>-->

                            <!--                                <b-form-group-->
                            <!--                                    class="mt-2"-->
                            <!--                                    :label="$t('Some_warehouses')"-->
                            <!--                                >-->
                            <!--                                    <v-select-->
                            <!--                                        multiple-->
                            <!--                                        v-model="assigned_warehouses"-->
                            <!--                                        @input="Selected_Warehouse"-->
                            <!--                                        :reduce="(label) => label.value"-->
                            <!--                                        :placeholder="$t('PleaseSelect')"-->
                            <!--                                        :options="-->
                            <!--                                            warehouses.map((warehouses) => ({-->
                            <!--                                                label: warehouses.name,-->
                            <!--                                                value: warehouses.id,-->
                            <!--                                            }))-->
                            <!--                                        "-->
                            <!--                                    />-->
                            <!--                                </b-form-group>-->
                            <!--                            </b-col>-->

                            <!--                            <b-col md="12" class="mt-3">-->
                            <!--                                <b-button-->
                            <!--                                    variant="primary"-->
                            <!--                                    type="submit"-->
                            <!--                                    :disabled="SubmitProcessing"-->
                            <!--                                    >{{ $t("submit") }}-->
                            <!--                                </b-button>-->
                            <!--                                <div-->
                            <!--                                    v-once-->
                            <!--                                    class="typo__p"-->
                            <!--                                    v-if="SubmitProcessing"-->
                            <!--                                >-->
                            <!--                                    <div-->
                            <!--                                        class="spinner sm spinner-primary mt-3"-->
                            <!--                                    ></div>-->
                            <!--                                </div>-->
                            <!--                            </b-col>-->
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
                    @click="Users_PDF()"
                    size="small"
                    class="ma-1"
                    variant="outlined"
                    color="success"
                    prepend-icon="mdi-file-pdf-box"
                >
                    PDF
                </v-btn>
                <v-btn
                    size="small"
                    class="ma-1"
                    variant="outlined"
                    color="error"
                    prepend-icon="mdi-file-excel-box"
                >
                    <json-excel
                        :data="props.users"
                        :fields="jsonFields"
                        worksheet="Usuarios"
                        name="usuarios.xls"
                    >
                        Exportar
                    </json-excel>
                </v-btn>
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
        </v-row>
        <v-row>
            <v-col>
                <v-data-table-server
                    :headers="fields"
                    :items="users"
                    :items-length="users.length"
                    :loading="loading"
                    :search="search"
                    class="elevation-2"
                    density="compact"
                    loading-text="Cargando... "
                    no-data-text="No existen datos a mostrar"
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
                    <template v-slot:item.accions="{ item }">
                        <div class="row-actions">
                            <v-btn
                                class="ma-1"
                                color="primary"
                                icon="mdi-pencil"
                                size="x-small"
                                variant="outlined"
                                @click="Edit_User(item.raw)"
                            >
                            </v-btn>
                        </div>
                    </template>
                    <template v-slot:column.name="{ column }">
                        {{ column.title.toUpperCase() }}
                    </template>
                </v-data-table-server>
            </v-col>
        </v-row>
    </Layout>
</template>
