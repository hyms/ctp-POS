<script setup>
import {computed, ref} from "vue";
import Layout from "@/Layouts/Authenticated.vue";
import ExportBtn from "@/Components/ExportBtn.vue";
import {router, usePage} from "@inertiajs/vue3";
import helper from "@/helpers";
import labels from "@/labels";

const props = defineProps({
  payments: Object,
  sales: Object,
  clients: Object,
})
const currency = computed(() => usePage().props.currency);

const loading = ref(false);
const menu = ref(false);
const search = ref("");
const clientFilter = ref([]);

const form = ref({
  startDate: "",
  endDate: "",
  client: "",
  Ref: "",
  sale: "",
  Reg: "",
});

const fields = ref([
  {title: "Fecha", key: "date"},
  {title: "Codigo", key: "Ref"},
  {title: "Codigo Venta", key: "Ref_Sale"},
  {title: "Cliente", key: "client_name"},
  {title: "Forma de Pago", key: "Reglement"},
  {title: "Monto", key: "montant"},
]);
const jsonFields = ref({
  "Fecha": "date",
  "Codigo": "Ref",
  "Codigo Venta": "Ref_Sale",
  "Cliente": "client_name",
  "Forma de Pago": "Reglement",
  "Monto": "montant",
});

function sumCount(rowObj) {

  let sum = 0;
  for (let i = 0; i < rowObj.length; i++) {
    sum += rowObj[i].montant;
  }
  return sum;
}

function querySelections(v) {
  clientFilter.value = props.clients.filter((e) => {
    return (
        (e.name || "").toLowerCase().indexOf((v || "").toLowerCase()) > -1
    );
  });
}

//-------------------------------- Get All Payments Sales ---------------------\\
function Payments_Sales() {
  // Start the progress bar.

  router.get("/payment_sale",
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
      <v-col md="3" sm="6" cols="12">
        <v-card class="mb-30 text-center">
          <v-card-text>
            <v-row align="center" no-gutters>
              <v-col cols="6" class="text-right">
                <v-icon
                    color="primary"
                    icon="mdi-cart-outline"
                    size="68"
                ></v-icon>
              </v-col>
              <v-col
                  class="text-h6"
                  cols="6"
              >
                <p class="text-disabled mt-2 mb-0">{{ labels.sale.GrandTotal }} {{ currency }}</p>
                <p class="text-primary text-24 line-height-1 mb-2">
                  {{ sumCount(payments) }}
                </p>
              </v-col>
            </v-row>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
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
        <v-menu v-model="menu" :close-on-content-click="false" location="bottom">
          <template v-slot:activator="{ props }">
            <v-btn
                color="primary"
                variant="outlined"
                size="small"
                elevation="1"
                class="mr-2 my-1"
                v-bind="props"
                append-icon="mdi-magnify"
            >
              {{ labels.filters }}
            </v-btn>
          </template>

          <v-card max-width="500">
            <v-form @submit.prevent="Payments_Sales">
              <v-card-text>
                <v-row>
                  <v-col cols="12" sm="6">
                    <v-text-field
                        v-model="form.startDate"
                        variant="outlined"
                        clearable
                        hide-details="auto"
                        type="date"
                        :label="labels.start_date"
                    ></v-text-field>
                  </v-col>
                  <v-col cols="12" sm="6">
                    <v-text-field
                        v-model="form.endDate"
                        variant="outlined"
                        clearable
                        hide-details="auto"
                        type="date"
                        :label="labels.end_date"
                    ></v-text-field>
                  </v-col>
                  <v-col cols="12" sm="6">
                    <v-text-field
                        v-model="form.Ref"
                        variant="outlined"
                        clearable
                        hide-details="auto"
                        type="text"
                        :label="labels.payment.Ref"
                    ></v-text-field>
                  </v-col>
                  <v-col cols="12" sm="6">
                    <v-text-field
                        v-model="form.sale"
                        variant="outlined"
                        clearable
                        hide-details="auto"
                        type="text"
                        :label="labels.sale.Ref"
                    ></v-text-field>
                  </v-col>
                  <v-col cols="12" sm="6">
                    <v-select
                        v-model="form.Reg"
                        :items="helper.reglamentPayment()"
                        :label="labels.payment.Reglement"
                        item-title="title"
                        item-value="value"
                        variant="outlined"
                        hide-details="auto"
                        clearable
                    ></v-select>
                  </v-col>
                  <v-col cols="12" sm="6">
                    <v-autocomplete
                        v-model="form.client"
                        @update:search="querySelections"
                        item-title="name"
                        item-value="id"
                        variant="outlined"
                        clearable
                        hide-details="auto"
                        :items="clientFilter"
                        :label="labels.sale.client_id"
                    ></v-autocomplete>
                  </v-col>
                </v-row>
              </v-card-text>
              <v-divider></v-divider>
              <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn
                    variant="text"
                    size="small"
                    color="error"
                    @click="menu = false"
                >
                  {{ labels.cancel }}
                </v-btn>
                <v-btn
                    type="submit"
                    variant="tonal"
                    size="small"
                    color="primary"
                    @click="Payments_Sales"
                >
                  {{ labels.search }}
                </v-btn>
              </v-card-actions>
            </v-form>
          </v-card>
        </v-menu>
        <ExportBtn
            :data="payments"
            :fields="jsonFields"
            name-file="Ventas"
        ></ExportBtn>
      </v-col>
    </v-row>
    <v-card>
      <v-data-table
          :headers="fields"
          :items="payments"
          :search="search"
          hover
          density="compact"
          :no-data-text="labels.no_data_table"
          :loading="loading"
      >
        <template v-slot:item.Reglement="{ item }">
          {{ helper.getReglamentPayment(itemsnackbarText.value.Reglement)[0].title }}
        </template>

      </v-data-table>
    </v-card>
  </layout>
</template>
