<script setup>
import {computed, ref} from "vue";
import Layout from "@/Layouts/Authenticated.vue";
import ExportBtn from "@/Components/buttons/ExportBtn.vue";
import {router, usePage} from "@inertiajs/vue3";
import helper from "@/helpers";
import labels from "@/labels";

const props = defineProps({
  report: Object,
})


const loading = ref(false);
const menu = ref(false);
const search = ref("");
// const clients = ref([]);


const fields = ref([
  {title: "Nombre", key: "name"},
  {title: "Telefono", key: "phone"},
  {title: "Total Ventas", key: "total_sales"},
  {title: "Monto Total", key: "total_amount"},
  {title: "Total de Pago", key: "total_paid"},
  {title: "Deuda", key: "due"},
  // {title: "Deuda Retorno", key: "Total_Sell_Return_Due"},
  {title: "Actions", key: "actions"},
]);
const jsonFields = ref({
  "Nombre": "name",
  "Telefono": "phone",
  "Total Ventas": "total_sales",
  "Monto Total": "total_amount",
  "Total de Pago": "total_paid",
  "Deuda": "due",
// {title: "Deuda Retorno", key: "Total_Sell_Return_Due"},
});

function sumCount(rowObj) {
  let sum = 0;
  for (let i = 0; i < rowObj.length; i++) {
    sum += rowObj[i].total_amount;
  }
  return sum;
}

function sumCount2(rowObj) {
  let sum = 0;
  for (let i = 0; i < rowObj.length; i++) {
    sum += rowObj[i].total_paid;
  }
  return sum;
}

function sumCount3(rowObj) {
  let sum = 0;
  for (let i = 0; i < rowObj.length; i++) {
    sum += rowObj[i].due;
  }
  return sum;
}

function sumCount4(rowObj) {
  let sum = 0;
  for (let i = 0; i < rowObj.length; i++) {
    sum += rowObj[i].return_Due;
  }
  return sum;
}

//--------------------------- Get Customer Report -------------\\

function Get_Client_Report(page) {
  router.get("/report/client",
      {filter: form.value},
      {
        preserveState: true,
        onStart: page => {
          loading.value = true;
        },
        onSuccess: page => {
          menu.value = false;
        },
        onFinish: visit => {
          loading.value = false;
        },
      }
  )
}
</script>
<template>
  <layout>
    <v-row align="center" class="mb-3">
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
      <v-col cols="auto" class="text-right">
        <ExportBtn
            :data="report"
            :fields="jsonFields"
            name-file="Clientes"
        ></ExportBtn>
      </v-col>
    </v-row>
    <v-card>
      <v-data-table
          :headers="fields"
          :items="report"
          :search="search"
          hover

          :no-data-text="labels.no_data_table"
          :loading="loading"
      >
        <template v-slot:item.actions="{ item }">
          <v-btn
              class="ma-1"
              color="primary"
              icon="mdi-eye"
              size="x-small"
              variant="outlined"
              @click="helper.linkVisit('/report/client/'+item.id)"
          >
          </v-btn>
        </template>
      </v-data-table>
    </v-card>
  </layout>


</template>
