<script setup>
import {onMounted, ref} from "vue";
import Layout from "@/Layouts/Authenticated.vue";
import Snackbar from "@/Components/snackbar.vue";
import ExportBtn from "@/Components/ExportBtn.vue";
import {router} from "@inertiajs/vue3";
import helper from "@/helpers";
import labels from "@/labels";
import moment from 'moment'

const props = defineProps({
  payments:Object,
  sales:Object,
  clients:Object,
})
const loading = ref(false);
const today_mode = ref(true);

const form = ref({
  startDate: "",
  endDate: "",
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
//     return {
//       Filter_client: "",
//       Filter_Ref: "",
//       Filter_sale: "",
//       Filter_Reg: "",
//     };
//
function sumCount(rowObj) {

  let sum = 0;
  for (let i = 0; i < rowObj.children.length; i++) {
    sum += rowObj.children[i].montant;
  }
  return sum;
}

//     //------ Reset Filter
//     Reset_Filter() {
//       this.search = "";
//       this.Filter_client = "";
//       this.Filter_Ref = "";
//       this.Filter_sale = "";
//       this.Filter_Reg = "";
//       this.Payments_Sales(this.serverParams.page);
//     },
//
//---------------------------------------- Set To Strings-------------------------\\
function setToStrings() {
  // Simply replaces null values with strings=''
  if (this.Filter_client === null) {
    this.Filter_client = "";
  } else if (this.Filter_sale === null) {
    this.Filter_sale = "";
  }
}

function get_data_loaded() {
  if (today_mode.value) {
    let today = new Date()

    form.value.startDate = today.getFullYear();
    form.value.endDate = new Date().toJSON().slice(0, 10);
  }
}

//-------------------------------- Get All Payments Sales ---------------------\\
function Payments_Sales() {
  // Start the progress bar.
  loading.value = true;
  setToStrings();
  get_data_loaded();

  // axios
  //     .get(
  //         "/payment_sale?page=" +
  //         page +
  //         "&Ref=" +
  //         this.Filter_Ref +
  //         "&client_id=" +
  //         this.Filter_client +
  //         "&sale_id=" +
  //         this.Filter_sale +
  //         "&Reglement=" +
  //         this.Filter_Reg +
  //         "&SortField=" +
  //         this.serverParams.sort.field +
  //         "&SortType=" +
  //         this.serverParams.sort.type +
  //         "&search=" +
  //         this.search +
  //         "&limit=" +
  //         this.limit +
  //         "&to=" +
  //         this.endDate +
  //         "&from=" +
  //         this.startDate
  //     )
  //
  //     .then(response => {
  //       this.payments = response.data.payments;
  //       this.clients = response.data.clients;
  //       this.sales = response.data.sales;
  //       this.totalRows = response.data.totalRows;
  //       this.rows[0].children = this.payments;
  //       // Complete the animation of theprogress bar.
  //       NProgress.done();
  //       this.isLoading = false;
  //       this.today_mode = false;
  //     })
  //     .catch(response => {
  //       // Complete the animation of theprogress bar.
  //       NProgress.done();
  //       setTimeout(() => {
  //         this.isLoading = false;
  //         this.today_mode = false;
  //       }, 500);
  //     });
}

//   //----------------------------- Created function-------------------\\
//   created: function() {
//     this.Payments_Sales(1);
//   }
// };
</script>
<template>
  <layout :loading="loading">
    <v-row>
      <v-spacer></v-spacer>
      <v-col cols="12" sm="4" md="3" class="text-center">
        <v-text-field v-model="form.startDate"
                      variant="outlined"
                      density="compact"
                      clearable
                      hide-details="auto"
                      type="date"
                      :label="labels.start_date"
        ></v-text-field>
      </v-col>
      <v-col cols="12" sm="4" md="3" class="text-center">
        <v-text-field v-model="form.endDate"
                      variant="outlined"
                      density="compact"
                      clearable
                      hide-details="auto"
                      type="date"
                      :label="labels.end_date"></v-text-field>
      </v-col>
      <v-spacer></v-spacer>
    </v-row>

    <v-row align="center" class="mb-3">
      <v-spacer></v-spacer>
      <v-col cols="auto" class="text-right">
        <!--        <filter_form-->
        <!--            :filter_form="filter_form"-->
        <!--            :customers="customers"-->
        <!--            :sales_types="sales_types"-->
        <!--        ></filter_form>-->
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
          hover
          density="compact"
          :no-data-text="labels.no_data_table"
          :loading="loading"
      >
      </v-data-table>
    </v-card>
  </layout>


  <!--        <div slot="table-actions" class="mt-2 mb-3">-->
  <!--          <b-button variant="outline-info ripple m-1" size="sm" v-b-toggle.sidebar-right>-->
  <!--            <i class="i-Filter-2"></i>-->
  <!--            {{ $t("Filter") }}-->
  <!--          </b-button>-->
  <!--          <b-button @click="Payment_PDF()" size="sm" variant="outline-success ripple m-1">-->
  <!--            <i class="i-File-Copy"></i> PDF-->
  <!--          </b-button>-->
  <!--          <vue-excel-xlsx-->
  <!--              class="btn btn-sm btn-outline-danger ripple m-1"-->
  <!--              :data="payments"-->
  <!--              :columns="columns"-->
  <!--              :file-name="'payments'"-->
  <!--              :file-type="'xlsx'"-->
  <!--              :sheet-name="'payments'"-->
  <!--              >-->
  <!--              <i class="i-File-Excel"></i> EXCEL-->
  <!--          </vue-excel-xlsx>-->
  <!--        </div>-->
  <!--      </vue-good-table>-->
  <!--    </v-card>-->

  <!--    &lt;!&ndash; Sidebar Filter &ndash;&gt;-->
  <!--    <b-sidebar id="sidebar-right" :title="$t('Filter')" bg-variant="white" right shadow>-->
  <!--      <div class="px-3 py-2">-->
  <!--        <b-row>-->
  <!--          &lt;!&ndash; Reference &ndash;&gt;-->
  <!--          <v-col md="12">-->
  <!--            <b-form-group :label="$t('Reference')">-->
  <!--              <b-form-input label="Reference" :placeholder="$t('Reference')" v-model="Filter_Ref"></b-form-input>-->
  <!--            </b-form-group>-->
  <!--          </v-col>-->

  <!--          &lt;!&ndash; Customers  &ndash;&gt;-->
  <!--          <v-col md="12">-->
  <!--            <b-form-group :label="$t('Customer')">-->
  <!--              <v-select-->
  <!--                :reduce="label => label.value"-->
  <!--                :placeholder="$t('Choose_Customer')"-->
  <!--                v-model="Filter_client"-->
  <!--                :options="clients.map(clients => ({label: clients.name, value: clients.id}))"-->
  <!--              />-->
  <!--            </b-form-group>-->
  <!--          </v-col>-->

  <!--          &lt;!&ndash; Sale  &ndash;&gt;-->
  <!--          <v-col md="12">-->
  <!--            <b-form-group :label="$t('Sale')">-->
  <!--              <v-select-->
  <!--                :reduce="label => label.value"-->
  <!--                :placeholder="$t('PleaseSelect')"-->
  <!--                v-model="Filter_sale"-->
  <!--                :options="sales.map(sales => ({label: sales.Ref, value: sales.id}))"-->
  <!--              />-->
  <!--            </b-form-group>-->
  <!--          </v-col>-->

  <!--          &lt;!&ndash; Payment choice &ndash;&gt;-->
  <!--          <v-col md="12">-->
  <!--            <b-form-group :label="$t('Paymentchoice')">-->
  <!--              <v-select-->
  <!--                v-model="Filter_Reg"-->
  <!--                :reduce="label => label.value"-->
  <!--                :placeholder="$t('PleaseSelect')"-->
  <!--                :options="-->
  <!--                          [-->
  <!--                          {label: 'Cash', value: 'Cash'},-->
  <!--                          {label: 'cheque', value: 'cheque'},-->
  <!--                          {label: 'TPE', value: 'tpe'},-->
  <!--                          {label: 'Western Union', value: 'Western Union'},-->
  <!--                          {label: 'bank transfer', value: 'bank transfer'},-->
  <!--                          {label: 'credit card', value: 'credit card'},-->
  <!--                          {label: 'other', value: 'other'},-->
  <!--                          ]"-->
  <!--              ></v-select>-->
  <!--            </b-form-group>-->
  <!--          </v-col>-->

  <!--          <v-col md="6" sm="12">-->
  <!--            <b-button-->
  <!--              @click="Payments_Sales(serverParams.page)"-->
  <!--              variant="primary ripple m-1"-->
  <!--              size="sm"-->
  <!--              block-->
  <!--            >-->
  <!--              <i class="i-Filter-2"></i>-->
  <!--              {{ $t("Filter") }}-->
  <!--            </b-button>-->
  <!--          </v-col>-->
  <!--          <v-col md="6" sm="12">-->
  <!--            <b-button @click="Reset_Filter()" variant="danger ripple m-1" size="sm" block>-->
  <!--              <i class="i-Power-2"></i>-->
  <!--              {{ $t("Reset") }}-->
  <!--            </b-button>-->
  <!--          </v-col>-->
  <!--        </b-row>-->
  <!--      </div>-->
  <!--    </b-sidebar>-->
  <!--  </div>-->
</template>
