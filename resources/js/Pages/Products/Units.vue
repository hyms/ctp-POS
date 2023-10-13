<script setup>
import {ref} from "vue";
import Layout from "@/Layouts/Authenticated.vue";
import Snackbar from "@/Components/snackbar.vue";
import helper from "@/helpers";
import {router} from "@inertiajs/vue3";
import DeleteDialog from "@/Components/buttons/DeleteDialog.vue";
import NewBtn from "@/Components/buttons/NewBtn.vue";
import labels from "@/labels";

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
  {title: labels.unit.name, key: "name"},
  {title: labels.unit.ShortName, key: "ShortName"},
  {title: labels.unit.base_unit_name, key: "base_unit_name"},
  {title: labels.unit.operator, key: "operator"},
  {title: labels.unit.operator_value, key: "operator_value"},
  {title: labels.actions, key: "actions"},
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
      .then(({data}) => {
        snackbar.value = true;
        snackbarColor.value = "success";
        snackbarText.value = "Proceso exitoso";
        router.reload({
          preserveState: true,
          preserveScroll: true,
        });

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
      .then(({data}) => {
        snackbar.value = true;
        snackbarColor.value = "success";
        snackbarText.value = "Proceso exitoso";
        router.reload({
          preserveState: true,
          preserveScroll: true,
        });

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
      .then(({data}) => {
        snackbar.value = true;
        snackbarColor.value = "success";
        snackbarText.value = "Borrado exitoso";
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
  <Layout
      :snackbar-view="snackbar"
      :snackbar-color="snackbarColor"
      :snackbar-text="snackbarText">
    <delete-dialog
        :model="dialogDelete"
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
                    :rules="helper.required.concat(helper.max(15))"
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
                    :rules="helper.required.concat(helper.max(15))"
                    hide-details="auto"
                >
                </v-text-field>
              </v-col>
              <!-- Base unit -->
              <v-col cols="12">
                <v-select
                    @update:modelValue="Selected_Base_Unit"
                    v-model="unit.base_unit"
                    :items="units_base"
                    :label="unitLabels.base_unit"
                    item-title="title"
                    item-value="value"
                    hide-details="auto"
                    clearable
                ></v-select>
              </v-col>
              <!-- operator  -->
              <v-col cols="12" v-if="show_operator">
                <v-select
                    v-model="unit.operator"
                    :items="helper.getOperatorUnit()"
                    :label="unitLabels.operator"
                    item-title="title"
                    item-value="value"
                    hide-details="auto"
                ></v-select>
              </v-col>

              <!-- Operation Value -->
              <v-col cols="12" v-if="show_operator">
                <v-text-field
                    :label="unitLabels.operator_value + ' *'"
                    v-model="unit.operator_value"
                    :placeholder="unitLabels.operator_value"
                    :rules="helper.required.concat(helper.numberWithDecimal)"
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
            prepend-icon="mdi-magnify"
            hide-details
            label="Buscar"
            single-line
            variant="underlined"
        ></v-text-field>
      </v-col>
      <v-col cols="12" sm="6" class="text-right">
        <new-btn :on-click="New_Unit" label="Añadir"></new-btn>
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
            no-data-text="No existen datos a mostrar"
            :loading="loading"
            loading-text="Cargando..."
        >
          <template v-slot:item.actions="{ item }">
            <v-btn
                class="ma-1"
                color="primary"
                icon="mdi-pencil"
                size="x-small"
                variant="outlined"
                @click="Edit_Unit(item)"
            >
            </v-btn>
            <v-btn
                class="ma-1"
                color="error"
                icon="mdi-delete"
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
