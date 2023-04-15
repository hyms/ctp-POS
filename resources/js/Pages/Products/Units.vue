<script setup>
import { ref } from "vue";
import Layout from "@/Layouts/Authenticated.vue";
import Snackbar from "@/Components/snackbar.vue";
import ruleForm from "@/rules";
import { router } from "@inertiajs/vue3";

const props = defineProps({
    units: Array,
    units_base: Array,
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
const show_operator = ref(false);

const fields = ref([
    { title: "Nombre", key: "name" },
    { title: "Nombre corto", key: "ShortName" },
    { title: "Unidad base", key: "base_unit" },
    { title: "Operador", key: "operator" },
    { title: "Valor de Operacion", key: "operator_value" },
    { title: "Acciones", key: "actions" },
]);
const unit = ref({
    id: "",
    name: "",
    ShortName: "",
    base_unit: "",
    base_unit_name: "",
    operator: "*",
    operator_value: 1,
});
const unitLabels = ref({
    name: "Nombre",
    ShortName: "Nombre corto",
    base_unit: "Unidad base",
    base_unit_name: "Nombre unidad base",
    operator: "Operador",
    operator_value: "Valor de Operacion",
});

//------------- Submit Validation Create & Edit Unit
async function Submit_Unit() {
    const validate = await form.value.validate();
    if (validate.valid)
        if (!editmode.value) {
            Create_Unit();
        } else {
            Update_Unit();
        }
}

//------------------------------ Modal (create Unit) -------------------------------\\
function New_Unit() {
    reset_Form();
    show_operator.value = false;
    editmode.value = false;
    dialog.value = true;
}

function onClose() {
    dialog.value = false;
    reset_Form();
}

//------------------------------ Modal (Update Unit) -------------------------------\\
function Edit_Unit(item) {
    reset_Form();
    unit.value = item;
    show_operator.value = unit.value.base_unit !== "";
    editmode.value = true;
    dialog.value = true;
}

function Selected_Base_Unit(value) {
    show_operator.value = value != null;
}

//---------------------------------------- Set To Strings-------------------------\\
function setToStrings() {
    // Simply replaces null values with strings=''
    if (unit.value.base_unit === null) {
        unit.value.base_unit = "";
    }
}

//---------------- Send Request with axios ( Create Unit) --------------------\\
function Create_Unit() {
    loading.value = true;
    snackbar.value = false;
    setToStrings();
    axios
        .post("units", {
            name: unit.value.name,
            ShortName: unit.value.ShortName,
            base_unit: unit.value.base_unit,
            operator: unit.value.operator,
            operator_value: unit.value.operator_value,
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

//--------------- Send Request with axios ( Update Unit) --------------------\\
function Update_Unit() {
    loading.value = true;
    snackbar.value = false;
    setToStrings();
    axios
        .put("units/" + unit.value.id, {
            name: unit.value.name,
            ShortName: unit.value.ShortName,
            base_unit: unit.value.base_unit,
            operator: unit.value.operator,
            operator_value: unit.value.operator_value,
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

//------------------------------ reset Form ------------------------------\\
function reset_Form() {
    unit.value = {
        id: "",
        name: "",
        ShortName: "",
        base_unit: "",
        base_unit_name: "",
        operator: "*",
        operator_value: 1,
    };
}

//---------------------- delete modal  ------------------------------\\
function Delete_item(item) {
    reset_Form();
    unit.value = item;
    dialogDelete.value = true;
}

function onCloseDelete() {
    reset_Form();
    dialogDelete.value = false;
}

//--------------------------------- Remove Unit --------------------\\
function Remove_Unit() {
    axios
        .delete("units/" + unit.value.id)
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
</script>
<template>
    <Layout :loading="loading">
        <snackbar
            :snackbar="snackbar"
            :snackbar-color="snackbarColor"
            :snackbar-text="snackbarText"
        ></snackbar>
        <v-dialog v-model="dialogDelete" max-width="300px">
            <v-card>
                <v-card-text class="text-h5 text-center"
                    >Estas seguro?
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn
                        small
                        variant="elevated"
                        color="primary"
                        class="ma-1"
                        @click="Remove_Unit"
                        >Si
                    </v-btn>
                    <v-btn
                        variant="elevated"
                        small
                        color="error"
                        class="ma-1"
                        @click="onCloseDelete"
                        >Cancelar
                    </v-btn>

                    <v-spacer></v-spacer>
                </v-card-actions>
            </v-card>
        </v-dialog>
        <v-dialog
            v-model="dialog"
            max-width="400px"
            scrollable
            @update:modelValue="dialog === false ? reset_Form() : dialog"
        >
            <v-card>
                <v-toolbar
                    border
                    density="comfortable"
                    :title="(editmode ? 'Modificar' : 'Nueva') + ' Unidad'"
                >
                </v-toolbar>

                <v-card-text>
                    <v-form ref="form">
                        <v-row>
                            <!-- Name -->
                            <v-col md="12">
                                <v-text-field
                                    :label="unitLabels.name + ' *'"
                                    v-model="unit.name"
                                    :placeholder="unitLabels.name"
                                    :rules="
                                        ruleForm.required.concat(
                                            ruleForm.max(15)
                                        )
                                    "
                                    variant="outlined"
                                    density="comfortable"
                                    hide-details="auto"
                                >
                                </v-text-field>
                            </v-col>
                            <!-- ShortName -->
                            <v-col md="12">
                                <v-text-field
                                    :label="unitLabels.ShortName + ' *'"
                                    v-model="unit.ShortName"
                                    :placeholder="unitLabels.ShortName"
                                    :rules="
                                        ruleForm.required.concat(
                                            ruleForm.max(15)
                                        )
                                    "
                                    variant="outlined"
                                    density="comfortable"
                                    hide-details="auto"
                                >
                                </v-text-field>
                            </v-col>
                            <!-- Base unit -->
                            <v-col md="12">
                                <v-select
                                    @update:modelValue="Selected_Base_Unit"
                                    v-model="unit.base_unit"
                                    :items="units_base"
                                    :label="unitLabels.base_unit"
                                    item-title="title"
                                    item-value="value"
                                    variant="outlined"
                                    density="comfortable"
                                    hide-details="auto"
                                    clearable
                                ></v-select>
                            </v-col>
                            <!-- operator  -->
                            <v-col md="12" v-if="show_operator">
                                <v-select
                                    v-model="unit.operator"
                                    :items="[
                                        {
                                            title: 'Multiplicar (*)',
                                            value: '*',
                                        },
                                        { title: 'Dividir (/)', value: '/' },
                                    ]"
                                    :label="unitLabels.operator"
                                    item-title="title"
                                    item-value="value"
                                    variant="outlined"
                                    density="comfortable"
                                    hide-details="auto"
                                ></v-select>
                            </v-col>

                            <!-- Operation Value -->
                            <v-col md="12" v-if="show_operator">
                                <v-text-field
                                    :label="unitLabels.operator_value + ' *'"
                                    v-model="unit.operator_value"
                                    :placeholder="unitLabels.operator_value"
                                    :rules="
                                        ruleForm.required.concat(
                                            ruleForm.numberWithDecimal
                                        )
                                    "
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
                        @click="Submit_Unit"
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
                    @click="New_Unit"
                >
                    Añadir
                </v-btn>
            </v-col>
        </v-row>
        <v-row>
            <v-col>
                <v-data-table
                    :headers="fields"
                    :items="units"
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
                            @click="Edit_Unit(item.raw)"
                        >
                        </v-btn>
                        <v-btn
                            class="ma-1"
                            color="error"
                            icon="mdi-delete"
                            size="x-small"
                            variant="outlined"
                            @click="Delete_item(item.raw)"
                        >
                        </v-btn>
                    </template>
                </v-data-table>
            </v-col>
        </v-row>
    </Layout>
</template>