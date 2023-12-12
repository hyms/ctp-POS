<script setup>
import { onMounted, ref } from "vue";
import Layout from "@/Layouts/Authenticated.vue";
import { api, globals, helpers, labels, rules } from "@/helpers";
import DeleteDialog from "@/Components/dialogs/DeleteDialog.vue";
import Snackbar from "@/Components/snackbar.vue";

const props = defineProps({
    errors: Object,
});
//declare variable
const units = ref([]);
const units_base = ref([""]);
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
const show_operator = ref(false);

const fields = ref([
    { title: labels.unit.name, key: "name" },
    { title: labels.unit.ShortName, key: "ShortName" },
    { title: labels.unit.base_unit_name, key: "base_unit_name" },
    { title: labels.unit.operator, key: "operator" },
    { title: labels.unit.operator_value, key: "operator_value" },
    { title: labels.actions, key: "actions" },
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
    setToStrings();
    api.post({
        url: "/products/units/",
        params: {
            name: unit.value.name,
            ShortName: unit.value.ShortName,
            base_unit: unit.value.base_unit,
            operator: unit.value.operator,
            operator_value: unit.value.operator_value,
        },
        snackbar,
        loadingItem: loading,
        onSuccess: () => {
            snackbar.value.text = labels.success_message;
            loadData();
            dialog.value = false;
        },
    });
}

//--------------- Send Request with axios ( Update Unit) --------------------\\
function Update_Unit() {
    setToStrings();
    api.put({
        url: "/products/units/" + unit.value.id,
        params: {
            name: unit.value.name,
            ShortName: unit.value.ShortName,
            base_unit: unit.value.base_unit,
            operator: unit.value.operator,
            operator_value: unit.value.operator_value,
        },
        snackbar,
        loadingItem: loading,
        onSuccess: () => {
            snackbar.value.text = labels.success_message;
            loadData();
            dialog.value = false;
        },
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
    api.delete({
        url: "/products/units/" + unit.value.id,
        snackbar,
        loadingItem: loading,
        onSuccess: () => {
            snackbar.value.text = labels.success_message;
            loadData();
            dialogDelete.value = false;
        },
    });
}

function loadData() {
    api.get({
        url: "/products/units/list",
        snackbar,
        loadingItem: loading,
        onSuccess: (data) => {
            units.value = data.units;
            units_base.value = data.units_base;
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
        <delete-dialog
            v-model="dialogDelete"
            :on-save="Remove_Unit"
            :on-close="onCloseDelete"
        >
        </delete-dialog>
        <v-dialog
            v-model="dialog"
            max-width="400px"
            scrollable
            @update:modelValue="dialog === false ? reset_Form() : dialog"
        >
            <v-card>
                <v-toolbar
                    border
                    :title="(editmode ? 'Modificar' : 'Nueva') + ' Unidad'"
                >
                </v-toolbar>
                <v-form ref="form">
                    <v-card-text>
                        <v-row>
                            <!-- Name -->
                            <v-col cols="12">
                                <v-text-field
                                    :label="unitLabels.name + ' *'"
                                    v-model="unit.name"
                                    :placeholder="unitLabels.name"
                                    :rules="
                                        rules.required.concat(rules.max(15))
                                    "
                                    hide-details="auto"
                                >
                                </v-text-field>
                            </v-col>
                            <!-- ShortName -->
                            <v-col cols="12">
                                <v-text-field
                                    :label="unitLabels.ShortName + ' *'"
                                    v-model="unit.ShortName"
                                    :placeholder="unitLabels.ShortName"
                                    :rules="
                                        rules.required.concat(rules.max(15))
                                    "
                                    hide-details="auto"
                                >
                                </v-text-field>
                            </v-col>
                            <!-- Base unit -->
                            <v-col cols="12">
                                <v-select
                                    @update:modelValue="Selected_Base_Unit"
                                    v-model="unit.base_unit"
                                    :items="helpers.toArraySelect(units_base)"
                                    :label="unitLabels.base_unit"
                                    hide-details="auto"
                                    clearable
                                ></v-select>
                            </v-col>
                            <!-- operator  -->
                            <v-col cols="12" v-if="show_operator">
                                <v-select
                                    v-model="unit.operator"
                                    :items="helpers.getOperatorUnit()"
                                    :label="unitLabels.operator"
                                    hide-details="auto"
                                ></v-select>
                            </v-col>

                            <!-- Operation Value -->
                            <v-col cols="12" v-if="show_operator">
                                <v-text-field
                                    :label="unitLabels.operator_value + ' *'"
                                    v-model="unit.operator_value"
                                    :placeholder="unitLabels.operator_value"
                                    :rules="
                                        rules.required.concat(
                                            rules.numberWithDecimal
                                        )
                                    "
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
                            Cancelar
                        </v-btn>
                        <v-btn
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
                </v-form>
            </v-card>
        </v-dialog>

        <v-row align="center">
            <v-col cols="12" sm="6">
                <v-text-field
                    v-model="search"
                    prepend-icon="fas fa-search"
                    hide-details
                    label="Buscar"
                    single-line
                    variant="underlined"
                ></v-text-field>
            </v-col>
            <v-col cols="12" sm="6" class="text-right">
                <v-btn
                    v-if="globals.userPermision(['unit'])"
                    color="primary"
                    class="ma-1"
                    prepend-icon="fas fa-plus"
                    @click="New_Unit"
                >
                    {{ labels.add }}
                </v-btn>
            </v-col>
        </v-row>
        <v-row>
            <v-col cols="12">
                <v-data-table
                    :headers="fields"
                    :items="units"
                    :search="search"
                    hover
                    class="elevation-2"
                    :no-data-text="labels.no_data_table"
                    :loading="loading"
                >
                    <template v-slot:item.actions="{ item }">
                        <v-btn
                            class="ma-1"
                            color="primary"
                            icon="fas fa-pen"
                            size="x-small"
                            variant="outlined"
                            @click="Edit_Unit(item)"
                        >
                        </v-btn>
                        <v-btn
                            class="ma-1"
                            color="error"
                            icon="fas fa-trash"
                            size="x-small"
                            variant="outlined"
                            @click="Delete_item(item)"
                        >
                        </v-btn>
                    </template>
                </v-data-table>
            </v-col>
        </v-row>
    </Layout>
</template>
