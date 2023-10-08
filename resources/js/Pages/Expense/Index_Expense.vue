<script setup>
import {ref} from "vue";
import Layout from "@/Layouts/Authenticated.vue";
import ExportBtn from "@/Components/buttons/ExportBtn.vue";
import FilterForm from "./filter_form.vue";
import labels from "@/labels";
import {router} from "@inertiajs/vue3";
import DeleteDialog from "@/Components/buttons/DeleteDialog.vue";

const props = defineProps({
  expenses: Object,
  Expenses_category: Object,
  warehouses: Object,
  filter_form: Object,
  errors: Object,
});
//declare variable
const form = ref(null);
const search = ref("");
const loading = ref(false);
const snackbar = ref(false);
const snackbarText = ref("");
const snackbarColor = ref("info");
const dialogDelete = ref(false);

const fields = ref([
  {title: "Fecha", key: "date"},
  {title: "Codigo", key: "Ref"},
  {title: "Detalle", key: "details"},
  {title: "Monto", key: "amount"},
  {title: "Categoria", key: "category_name"},
  {title: "Sucursal", key: "warehouse_name"},
  {title: "Acciones", key: "actions"},
]);
const jsonFields = ref({
  Fecha: "date",
  Codigo: "code",
  Detalle: "details",
  Monto: "amount",
  Categoria: "category_name",
  Sucursal: "warehouse_name",
});
const expense = ref({
  id: "",
});

//------------------------------- Remove Expense -------------------------\\

function Remove_Expense() {
  loading.value = true;
  snackbar.value = false;
  axios
      .delete("/expenses/" + expense.value.id)
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

function onCloseDelete() {
  dialogDelete.value = false;
}

function Delete_Expense(id) {
  expense.value.id = id;
  dialogDelete.value = true;
}
</script>
<template>
  <layout
      :snackbar-view="snackbar"
      :snackbar-text="snackbarText"
      :snackbar-color="snackbarColor"
  >
    <!-- Modal Remove Expense -->
    <delete-dialog
        :model="dialogDelete"
        :on-save="Remove_Expense"
        :on-close="onCloseDelete"
    ></delete-dialog>
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
        <FilterForm :warehouses="warehouses" :categories="Expenses_category" :filter_form="filter_form"></FilterForm>
        <ExportBtn
            :data="expenses"
            :fields="jsonFields"
            name-file="Gastos"
        ></ExportBtn>
        <v-btn
            color="primary"
            class="ma-1"
            prepend-icon="mdi-plus"
            @click="router.visit('/expenses/create')"
        >
          {{ labels.add }}
        </v-btn>
      </v-col>
    </v-row>
    <v-row>
      <v-col>
        <v-data-table
            :headers="fields"
            :items="expenses"
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
                icon="mdi-pencil"
                size="x-small"
                variant="outlined"
                @click="router.visit('/expenses/edit/' + item.id)"
            >
            </v-btn>
            <v-btn
                class="ma-1"
                color="error"
                icon="mdi-delete"
                size="x-small"
                variant="outlined"
                @click="Delete_Expense(item.id)"
            >
            </v-btn>
          </template>
        </v-data-table>
      </v-col>
    </v-row>
  </layout>
</template>
