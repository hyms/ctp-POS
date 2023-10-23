<script setup>
import {computed, ref} from "vue";
import Layout from "@/Layouts/Authenticated.vue";
import ExportBtn from "@/Components/buttons/ExportBtn.vue";
import {router, usePage} from "@inertiajs/vue3";
import helper from "@/helpers";
import labels from "@/labels";

const tab = ref(null);

const totalRows_sales = ref("");
const totalRows_transfers = ref("");
const totalRows_adjustments = ref("");

//       totalRows_quotations: "",
//       totalRows_sales_return: "",
//       totalRows_purchases_return: "",
//       totalRows_purchases: "",

const search_sales = ref("");
const search_transfers = ref("");
const search_adjustments = ref("");
//       search_purchases:"",
//       search_quotations:"",
//       search_return_sales:"",
//       search_return_purchases:"",

const props = defineProps({
  id: Number,
})
const loading = ref(false);
const menu = ref(false);
const formFilter = ref({
  warehouse_id: ""
});
const product = ref({});
const sales = ref([]);
const transfers = ref([]);
const adjustments = ref([]);
//       purchases: [],
//       quotations: [],
//       sales_return: [],
//       purchases_return: [],

//     columns_quotations() {
//       return [
//          {
//           label: this.$t("date"),
//           field: "date",
//           tdClass: "text-left",
//           thClass: "text-left"
//         },
//         {
//           label: this.$t("Reference"),
//           field: "Ref",
//           tdClass: "text-left",
//           thClass: "text-left"
//         },
//         {
//           label: this.$t("product_name"),
//           field: "product_name",
//           tdClass: "text-left",
//           thClass: "text-left",
//           sortable: false
//         },
//         {
//           label: this.$t("Customer"),
//           field: "client_name",
//           tdClass: "text-left",
//           thClass: "text-left",
//           sortable: false
//         },
//         {
//           label: this.$t("warehouse"),
//           field: "warehouse_name",
//           tdClass: "text-left",
//           thClass: "text-left",
//           sortable: false
//         },
//         {
//           label: this.$t("Quantity"),
//           field: "quantity",
//           tdClass: "text-left",
//           thClass: "text-left",
//           sortable: false
//         },
//         {
//           label: this.$t("SubTotal"),
//           field: "total",
//           tdClass: "text-left",
//           thClass: "text-left",
//           sortable: false
//         },
//       ];
//     },
const fields_sales = ref([
  {title: labels.sale.date, key: "date"},
  {title: labels.sale.sales_type_id, key: "sale_type"},
  {title: labels.sale.Ref, key: "Ref"},
  {title: labels.sale.details.product, key: "product_name"},
  {title: labels.sale.client_id, key: "client_name"},
  {title: labels.sale.warehouse_id, key: "warehouse_name"},
  {title: labels.sale.details.qty, key: "quantity"},
  {title: labels.sale.details.sub_total, key: "total"},
]);
//     columns_sales_return() {
//       return [
//         {
//           label: this.$t("date"),
//           field: "date",
//           tdClass: "text-left",
//           thClass: "text-left"
//         },
//         {
//           label: this.$t("Reference"),
//           field: "Ref",
//           tdClass: "text-left",
//           thClass: "text-left"
//         },
//         {
//           label: this.$t("product_name"),
//           field: "product_name",
//           tdClass: "text-left",
//           thClass: "text-left",
//           sortable: false
//         },
//         {
//           label: this.$t("Customer"),
//           field: "client_name",
//           tdClass: "text-left",
//           thClass: "text-left",
//           sortable: false
//         },
//         {
//           label: this.$t("warehouse"),
//           field: "warehouse_name",
//           tdClass: "text-left",
//           thClass: "text-left",
//           sortable: false
//         },
//         {
//           label: this.$t("Quantity"),
//           field: "quantity",
//           tdClass: "text-left",
//           thClass: "text-left",
//           sortable: false
//         },
//         {
//           label: this.$t("SubTotal"),
//           field: "total",
//           tdClass: "text-left",
//           thClass: "text-left",
//           sortable: false
//         },
//       ];
//     },
//     columns_purchases() {
//       return [
//         {
//           label: this.$t("date"),
//           field: "date",
//           tdClass: "text-left",
//           thClass: "text-left"
//         },
//         {
//           label: this.$t("Reference"),
//           field: "Ref",
//           tdClass: "text-left",
//           thClass: "text-left"
//         },
//         {
//           label: this.$t("product_name"),
//           field: "product_name",
//           tdClass: "text-left",
//           thClass: "text-left",
//           sortable: false
//         },
//         {
//           label: this.$t("Supplier"),
//           field: "provider_name",
//           tdClass: "text-left",
//           thClass: "text-left",
//           sortable: false
//         },
//         {
//           label: this.$t("warehouse"),
//           field: "warehouse_name",
//           tdClass: "text-left",
//           thClass: "text-left",
//           sortable: false
//         },
//         {
//           label: this.$t("Quantity"),
//           field: "quantity",
//           tdClass: "text-left",
//           thClass: "text-left",
//           sortable: false
//         },
//         {
//           label: this.$t("SubTotal"),
//           field: "total",
//           tdClass: "text-left",
//           thClass: "text-left",
//           sortable: false
//         },
//       ];
//     },
//     columns_purchase_return() {
//       return [
//         {
//           label: this.$t("date"),
//           field: "date",
//           tdClass: "text-left",
//           thClass: "text-left"
//         },
//         {
//           label: this.$t("Reference"),
//           field: "Ref",
//           tdClass: "text-left",
//           thClass: "text-left"
//         },
//         {
//           label: this.$t("product_name"),
//           field: "product_name",
//           tdClass: "text-left",
//           thClass: "text-left",
//           sortable: false
//         },
//         {
//           label: this.$t("Supplier"),
//           field: "provider_name",
//           tdClass: "text-left",
//           thClass: "text-left",
//           sortable: false
//         },
//         {
//           label: this.$t("warehouse"),
//           field: "warehouse_name",
//           tdClass: "text-left",
//           thClass: "text-left",
//           sortable: false
//         },
//         {
//           label: this.$t("Quantity"),
//           field: "quantity",
//           tdClass: "text-left",
//           thClass: "text-left",
//           sortable: false
//         },
//         {
//           label: this.$t("SubTotal"),
//           field: "total",
//           tdClass: "text-left",
//           thClass: "text-left",
//           sortable: false
//         },
//       ];
//     },
const fields_transfers = ref([
  {title: labels.transfer.date, key: "date"},
  {title: labels.transfer.Ref, key: "Ref"},
  {title: labels.transfer_detail.product, key: "product_name"},
  {title: labels.transfer.from_warehouse, key: "from_warehouse"},
  {title: labels.transfer.to_warehouse, key: "to_warehouse"},
  {title: labels.sale.details.qty, key: "quantity"},
  {title: labels.sale.details.sub_total, key: "total"},
]);
const fields_adjustments = ref([
  {title: labels.adjustment.date, key: "date"},
  {title: labels.adjustment.Ref, key: "Ref"},
  {title: labels.adjustment.product, key: "product_name"},
  {title: labels.warehouse.name, key: "warehouse_name"},
]);

//----------------------------------- Get Details Product ------------------------------\\
function showDetails() {
  axios
      .get(`/get_product_detail/${props.id}`)
      .then(response => {
        product.value = response.data;
      })
      .catch(response => {
      });
}


//--------------------------- get_sales_by_product -------------\\
function Get_Sales(page = 1) {
  loading.value = true;
  axios.get("/report/get_sales_by_product?page=" +
      page +
      "&limit=" +
      "&search=" +
      "&id=" +
      props.id
  )
      .then(({data}) => {
        sales.value = data.sales;
      })
      .finally(() => {
        loading.value = false;
      });
}

//     //--------------------------- Get Purchases By product -------------\\
//     Get_Purchases(page) {
//       axios
//         .get(
//           "report/get_purchases_by_product?page=" +
//             page +
//             "&limit=" +
//             this.limit_purchases +
//             "&search=" +
//             this.search_purchases +
//             "&id=" +
//             this.$route.params.id
//         )
//         .then(response => {
//           this.purchases = response.data.purchases;
//           this.totalRows_purchases = response.data.totalRows;
//           this.isLoading = false;
//         })
//         .catch(response => {
//            setTimeout(() => {
//             this.isLoading = false;
//           }, 500);
//         });
//     },
//
//     //--------------------------- Get Quotations By product -------------\\
//     Get_Quotations(page) {
//       axios
//         .get(
//           "report/get_quotations_by_product?page=" +
//             page +
//             "&limit=" +
//             this.limit_quotations +
//             "&search=" +
//             this.search_quotations +
//             "&id=" +
//             this.$route.params.id
//         )
//         .then(response => {
//           this.quotations = response.data.quotations;
//           this.totalRows_quotations = response.data.totalRows;
//
//         })
//         .catch(response => {
//
//         });
//     },
//
//     },
//
//--------------------------- Get Transfers By product -------------\\
function Get_Transfers(page = 1) {
  axios
      .get(
          "/report/get_transfer_by_product?page=" +
          page +
          "&limit=" +
          "&search=" +
          "&id=" +
          props.id
      )
      .then(({data}) => {
        transfers.value = data.transfers;
      })
      .finally(() => {
        loading.value = false;
      });
}

//--------------------------- Get adjustment By product -------------\\
function Get_adjustments(page = 1) {
  axios
      .get(
          "/report/get_adjustment_by_product?page=" +
          page +
          "&limit=" +
          "&search=" +
          "&id=" +
          props.id
      )
      .then(({data}) => {
        adjustments.value = data.adjustments;
      })
      .finally(() => {
        loading.value = false;
      });
}

//
//     //--------------------------- Get sales Returns By product -------------\\
//     Get_Sales_Return(page) {
//       axios
//         .get(
//           "/report/get_sales_return_by_product?page=" +
//             page +
//             "&limit=" +
//             this.limit_sales_return +
//             "&search=" +
//             this.search_return_sales +
//             "&id=" +
//             this.$route.params.id
//         )
//         .then(response => {
//           this.sales_return = response.data.sales_return;
//           this.totalRows_sales_return = response.data.totalRows;
//         })
//         .catch(response => {});
//     },
//
//     //--------------------------- Get purchases Returns By product -------------\\
//     Get_Purchases_Return(page) {
//       axios
//         .get(
//           "/report/get_purchase_return_by_product?page=" +
//             page +
//             "&limit=" +
//             this.limit_purchases_return +
//             "&search=" +
//             this.search_return_purchases +
//             "&id=" +
//             this.$route.params.id
//         )
//         .then(response => {
//           this.purchases_return = response.data.purchases_return;
//           this.totalRows_purchases_return = response.data.totalRows;
//         })
//         .catch(response => {});
//     }
//   }, //end Methods
//
const tabVal = computed({
  get() {
    switch (tab.value) {
      case 'sales':
        Get_Sales();
        break;
      case 'transfer':
        Get_Transfers();
        break;
      case 'adjustment':
        Get_adjustments();
        break;
        // case 'quotations':
        //   Get_Quotations();
        // break;
        // case 'returns':
        //   Get_Returns();
        // break;
    }
    return tab.value;
  },
  set(val) {
    tab.value = val;
  }
});
</script>
<template>
  <layout>
    <v-card>
      <v-tabs v-model="tabVal" color="primary">
        <v-tab value="sales">Ventas</v-tab>
        <v-tab value="transfer">Transferencias</v-tab>
        <v-tab value="adjustment">Ajustes</v-tab>
      </v-tabs>
      <v-card-text>
        <v-window v-model="tabVal">
          <v-window-item value="sales">
            <v-row align="center" class="mb-1">
              <v-col cols="12" sm="6">
                <v-text-field
                    v-model="search_sales"
                    prepend-icon="mdi-magnify"
                    hide-details
                    :label="labels.search"
                    single-line
                    variant="underlined"
                ></v-text-field>
              </v-col>
              <v-spacer></v-spacer>
              <v-col cols="auto" class="text-right">
                <!--                <ExportBtn-->
                <!--                    :data="sales"-->
                <!--                    :fields="fields_sales_export"-->
                <!--                    name-file="Ventas"-->
                <!--                ></ExportBtn>-->
              </v-col>
            </v-row>
            <v-data-table
                :headers="fields_sales"
                :items="sales"
                :search="search_sales"
                hover
                :no-data-text="labels.no_data_table"
                :loading="loading"
            >
              <template v-slot:item.statut="{ item }">
                <v-chip
                    :color="helper.statutSaleColor(item.statut)"
                    variant="tonal"
                    size="x-small"
                >{{ helper.statutSale(item.statut) }}
                </v-chip>
              </template>
              <template v-slot:item.payment_status="{ item }">
                <v-chip
                    :color="helper.statusPaymentColor(item.payment_status)"
                    variant="tonal"
                    size="x-small"
                >{{ helper.statusPayment(item.payment_status) }}
                </v-chip>
              </template>
              <template v-slot:item.Ref="{ item }">
                <v-btn
                    variant="tonal"
                    size="x-small"
                    color="default"
                    :text="item.Ref"
                    @click="router.visit('/sales/detail/'+item.id)"
                ></v-btn>
              </template>
            </v-data-table>
          </v-window-item>
          <v-window-item value="transfer">
            <v-row align="center" class="mb-1">
              <v-col cols="12" sm="6">
                <v-text-field
                    v-model="search_transfers"
                    prepend-icon="mdi-magnify"
                    hide-details
                    :label="labels.search"
                    single-line
                    variant="underlined"
                ></v-text-field>
              </v-col>
              <v-spacer></v-spacer>
              <v-col cols="auto" class="text-right">
                <!--                <ExportBtn-->
                <!--                    :data="sales"-->
                <!--                    :fields="fields_sales_export"-->
                <!--                    name-file="Ventas"-->
                <!--                ></ExportBtn>-->
              </v-col>
            </v-row>
            <v-data-table
                :headers="fields_transfers"
                :items="transfers"
                :search="search_transfers"
                hover
                :no-data-text="labels.no_data_table"
                :loading="loading"
            >
            </v-data-table>
          </v-window-item>
          <v-window-item value="adjustment">
            <v-row align="center" class="mb-1">
              <v-col cols="12" sm="6">
                <v-text-field
                    v-model="search_sales"
                    prepend-icon="mdi-magnify"
                    hide-details
                    :label="labels.search"
                    single-line
                    variant="underlined"
                ></v-text-field>
              </v-col>
              <v-spacer></v-spacer>
              <v-col cols="auto" class="text-right">
                <!--                <ExportBtn-->
                <!--                    :data="sales"-->
                <!--                    :fields="fields_sales_export"-->
                <!--                    name-file="Ventas"-->
                <!--                ></ExportBtn>-->
              </v-col>
            </v-row>
            <v-data-table
                :headers="fields_adjustments"
                :items="adjustments"
                :search="search_adjustments"
                hover
                :no-data-text="labels.no_data_table"
                :loading="loading"
            >
            </v-data-table>
          </v-window-item>
        </v-window>
      </v-card-text>
    </v-card>
  </layout>

  <!--    <b-row v-if="!isLoading">-->
  <!--        <b-col lg="12">-->
  <!--            <h3 class="text-center">{{product.name}}</h3>-->
  <!--        </b-col>-->
  <!--      &lt;!&ndash; Warehouse Quantity &ndash;&gt;-->
  <!--          <b-col md="5" class="mt-4">-->
  <!--          -->
  <!--            <table class="table table-hover table-sm">-->
  <!--              <thead>-->
  <!--                <tr>-->
  <!--                  <th>{{$t('warehouse')}}</th>-->
  <!--                  <th>{{$t('Quantity')}}</th>-->
  <!--                </tr>-->
  <!--              </thead>-->
  <!--              <tbody>-->
  <!--                <tr v-for="PROD_W in product.CountQTY">-->
  <!--                  <td>{{PROD_W.mag}}</td>-->
  <!--                  <td>{{formatNumber(PROD_W.qte ,2)}} {{product.unit}}</td>-->
  <!--                </tr>-->
  <!--              </tbody>-->
  <!--            </table>-->
  <!--          </b-col>-->
  <!--          &lt;!&ndash; Warehouse Variants Quantity &ndash;&gt;-->
  <!--          <b-col md="7" v-if="product.is_variant == 'yes'" class="mt-4">-->
  <!--            <table class="table table-hover table-sm">-->
  <!--              <thead>-->
  <!--                <tr>-->
  <!--                  <th>{{$t('warehouse')}}</th>-->
  <!--                  <th>{{$t('Variant')}}</th>-->
  <!--                  <th>{{$t('Quantity')}}</th>-->
  <!--                </tr>-->
  <!--              </thead>-->
  <!--              <tbody>-->
  <!--                <tr v-for="PROD_V in product.CountQTY_variants">-->
  <!--                  <td>{{PROD_V.mag}}</td>-->
  <!--                  <td>{{PROD_V.variant}}</td>-->
  <!--                  <td>{{formatNumber(PROD_V.qte ,2)}} {{product.unit}}</td>-->
  <!--                </tr>-->
  <!--              </tbody>-->
  <!--            </table>-->
  <!--          </b-col>-->


  <!--                <template slot="table-row" slot-scope="props">-->
  <!--                  <div v-if="props.column.field == 'Ref'">-->
  <!--                    <router-link-->
  <!--                      :to="'/app/sales/detail/'+props.row.sale_id"-->
  <!--                    >-->
  <!--                      <span class="ul-btn__text ml-1">{{props.row.Ref}}</span>-->
  <!--                    </router-link>-->
  <!--                  </div>-->
  <!--                 -->
  <!--                  <div v-else-if="props.column.field == 'total'">-->
  <!--                    <span>{{currentUser.currency}} {{props.row.total}}</span>-->
  <!--                  </div>-->

  <!--                </template>-->
  <!--              </vue-good-table>-->
  <!--            </b-tab>-->

  <!--             &lt;!&ndash; Quotations Table &ndash;&gt;-->
  <!--            <b-tab :title="$t('Quotations')">-->
  <!--              <vue-good-table-->
  <!--                mode="remote"-->
  <!--                :columns="columns_quotations"-->
  <!--                :totalRows="totalRows_quotations"-->
  <!--                :rows="quotations"-->
  <!--                @on-page-change="PageChangeQuotation"-->
  <!--                @on-per-page-change="onPerPageChangeQuotation"-->
  <!--                @on-search="onSearch_quotations"-->
  <!--                :search-options="{-->
  <!--                  placeholder: $t('Search_this_table'),-->
  <!--                  enabled: true,-->
  <!--                }"-->
  <!--                :pagination-options="{-->
  <!--                  enabled: true,-->
  <!--                  mode: 'records',-->
  <!--                  nextLabel: 'next',-->
  <!--                  prevLabel: 'prev',-->
  <!--                }"-->
  <!--                styleClass="tableOne table-hover vgt-table"-->
  <!--              >-->
  <!--              <div slot="table-actions" class="mt-2 mb-3">-->
  <!--                <b-button @click="Quotation_PDF()" size="sm" variant="outline-success ripple m-1">-->
  <!--                  <i class="i-File-Copy"></i> PDF-->
  <!--                </b-button>-->

  <!--                <vue-excel-xlsx-->
  <!--                    class="btn btn-sm btn-outline-danger ripple m-1"-->
  <!--                    :data="quotations"-->
  <!--                    :columns="columns_quotations"-->
  <!--                    :file-name="'Quotation_report'"-->
  <!--                    :file-type="'xlsx'"-->
  <!--                    :sheet-name="'Quotation_report'"-->
  <!--                    >-->
  <!--                    <i class="i-File-Excel"></i> EXCEL-->
  <!--                </vue-excel-xlsx>-->
  <!--              </div>-->
  <!--                <template slot="table-row" slot-scope="props">-->
  <!--                  -->
  <!--                   <div v-if="props.column.field == 'Ref'">-->
  <!--                    <router-link-->
  <!--                      :to="'/app/quotations/detail/'+props.row.quotation_id"-->
  <!--                    >-->
  <!--                      <span class="ul-btn__text ml-1">{{props.row.Ref}}</span>-->
  <!--                    </router-link>-->
  <!--                  </div>-->

  <!--                  <div v-else-if="props.column.field == 'total'">-->
  <!--                    <span>{{currentUser.currency}} {{props.row.total}}</span>-->
  <!--                  </div>-->

  <!--                </template>-->
  <!--              </vue-good-table>-->
  <!--            </b-tab>-->

  <!--            &lt;!&ndash; Purchases Table &ndash;&gt;-->
  <!--            <b-tab :title="$t('Purchases')">-->
  <!--              <vue-good-table-->
  <!--                mode="remote"-->
  <!--                :columns="columns_purchases"-->
  <!--                :totalRows="totalRows_purchases"-->
  <!--                :rows="purchases"-->
  <!--                @on-page-change="PageChangePurchases"-->
  <!--                @on-per-page-change="onPerPageChangePurchases"-->
  <!--                @on-search="onSearch_purchases"-->
  <!--                :search-options="{-->
  <!--                  placeholder: $t('Search_this_table'),-->
  <!--                  enabled: true,-->
  <!--                }"-->
  <!--                :pagination-options="{-->
  <!--                  enabled: true,-->
  <!--                  mode: 'records',-->
  <!--                  nextLabel: 'next',-->
  <!--                  prevLabel: 'prev',-->
  <!--                }"-->
  <!--                styleClass="tableOne table-hover vgt-table"-->
  <!--              >-->
  <!--              <div slot="table-actions" class="mt-2 mb-3">-->
  <!--                <b-button @click="Purchase_PDF()" size="sm" variant="outline-success ripple m-1">-->
  <!--                  <i class="i-File-Copy"></i> PDF-->
  <!--                </b-button>-->

  <!--                <vue-excel-xlsx-->
  <!--                    class="btn btn-sm btn-outline-danger ripple m-1"-->
  <!--                    :data="purchases"-->
  <!--                    :columns="columns_purchases"-->
  <!--                    :file-name="'purchases_report'"-->
  <!--                    :file-type="'xlsx'"-->
  <!--                    :sheet-name="'purchases_report'"-->
  <!--                    >-->
  <!--                    <i class="i-File-Excel"></i> EXCEL-->
  <!--                </vue-excel-xlsx>-->
  <!--              </div>-->
  <!--                <template slot="table-row" slot-scope="props">-->
  <!--                   <div v-if="props.column.field == 'Ref'">-->
  <!--                    <router-link-->
  <!--                      :to="'/app/purchases/detail/'+props.row.purchase_id"-->
  <!--                    >-->
  <!--                      <span class="ul-btn__text ml-1">{{props.row.Ref}}</span>-->
  <!--                    </router-link>-->
  <!--                  </div>-->

  <!--                  <div v-else-if="props.column.field == 'total'">-->
  <!--                    <span>{{currentUser.currency}} {{props.row.total}}</span>-->
  <!--                  </div>-->

  <!--                </template>-->
  <!--              </vue-good-table>-->
  <!--            </b-tab>-->

  <!--            &lt;!&ndash; Sales Return Table &ndash;&gt;-->
  <!--            <b-tab :title="$t('SalesReturn')">-->
  <!--              <vue-good-table-->
  <!--                mode="remote"-->
  <!--                :columns="columns_sales_return"-->
  <!--                :totalRows="totalRows_sales_return"-->
  <!--                :rows="sales_return"-->
  <!--                @on-page-change="Page_Change_sales_Return"-->
  <!--                @on-per-page-change="onPerPage_Change_sales_Return"-->
  <!--                @on-search="onSearch_return_sales"-->
  <!--                :search-options="{-->
  <!--                  placeholder: $t('Search_this_table'),-->
  <!--                  enabled: true,-->
  <!--                }"-->
  <!--                :pagination-options="{-->
  <!--                  enabled: true,-->
  <!--                  mode: 'records',-->
  <!--                  nextLabel: 'next',-->
  <!--                  prevLabel: 'prev',-->
  <!--                }"-->
  <!--                styleClass="tableOne table-hover vgt-table"-->
  <!--              >-->
  <!--              <div slot="table-actions" class="mt-2 mb-3">-->
  <!--                <b-button @click="Sale_Return_PDF()" size="sm" variant="outline-success ripple m-1">-->
  <!--                  <i class="i-File-Copy"></i> PDF-->
  <!--                </b-button>-->

  <!--                <vue-excel-xlsx-->
  <!--                    class="btn btn-sm btn-outline-danger ripple m-1"-->
  <!--                    :data="sales_return"-->
  <!--                    :columns="columns_sales_return"-->
  <!--                    :file-name="'sales_return_report'"-->
  <!--                    :file-type="'xlsx'"-->
  <!--                    :sheet-name="'sales_return_report'"-->
  <!--                    >-->
  <!--                    <i class="i-File-Excel"></i> EXCEL-->
  <!--                </vue-excel-xlsx>-->
  <!--              </div>-->
  <!--                <template slot="table-row" slot-scope="props">-->
  <!--                  -->
  <!--                  <div v-if="props.column.field == 'Ref'">-->
  <!--                    <router-link-->
  <!--                      :to="'/app/sale_return/detail/'+props.row.return_sale_id"-->
  <!--                    >-->
  <!--                      <span class="ul-btn__text ml-1">{{props.row.Ref}}</span>-->
  <!--                    </router-link>-->
  <!--                  </div>-->

  <!--                  <div v-else-if="props.column.field == 'total'">-->
  <!--                    <span>{{currentUser.currency}} {{props.row.total}}</span>-->
  <!--                  </div>-->
  <!--                </template>-->
  <!--              </vue-good-table>-->
  <!--            </b-tab>-->

  <!--             &lt;!&ndash; Purchase Return Table &ndash;&gt;-->
  <!--            <b-tab :title="$t('PurchasesReturn')">-->
  <!--              <vue-good-table-->
  <!--                mode="remote"-->
  <!--                :columns="columns_purchase_return"-->
  <!--                :totalRows="totalRows_purchase_return"-->
  <!--                :rows="purchases_return"-->
  <!--                @on-page-change="Page_Change_purchases_Return"-->
  <!--                @on-per-page-change="onPerPage_Change_purchases_Return"-->
  <!--                @on-search="onSearch_return_purchases"-->
  <!--                :search-options="{-->
  <!--                  placeholder: $t('Search_this_table'),-->
  <!--                  enabled: true,-->
  <!--                }"-->
  <!--                :pagination-options="{-->
  <!--                  enabled: true,-->
  <!--                  mode: 'records',-->
  <!--                  nextLabel: 'next',-->
  <!--                  prevLabel: 'prev',-->
  <!--                }"-->
  <!--                styleClass="tableOne table-hover vgt-table"-->
  <!--              >-->
  <!--               <div slot="table-actions" class="mt-2 mb-3">-->
  <!--                <b-button @click="Returns_Purchase_PDF()" size="sm" variant="outline-success ripple m-1">-->
  <!--                  <i class="i-File-Copy"></i> PDF-->
  <!--                </b-button>-->

  <!--                <vue-excel-xlsx-->
  <!--                    class="btn btn-sm btn-outline-danger ripple m-1"-->
  <!--                    :data="purchases_return"-->
  <!--                    :columns="columns_purchase_return"-->
  <!--                    :file-name="'purchases_return_report'"-->
  <!--                    :file-type="'xlsx'"-->
  <!--                    :sheet-name="'purchases_return_report'"-->
  <!--                    >-->
  <!--                    <i class="i-File-Excel"></i> EXCEL-->
  <!--                </vue-excel-xlsx>-->
  <!--              </div>-->
  <!--                <template slot="table-row" slot-scope="props">-->
  <!--                  -->
  <!--                  <div v-if="props.column.field == 'Ref'">-->
  <!--                    <router-link-->
  <!--                      :to="'/app/purchase_return/detail/'+props.row.return_purchase_id"-->
  <!--                    >-->
  <!--                      <span class="ul-btn__text ml-1">{{props.row.Ref}}</span>-->
  <!--                    </router-link>-->
  <!--                  </div>-->

  <!--                  <div v-else-if="props.column.field == 'total'">-->
  <!--                    <span>{{currentUser.currency}} {{props.row.total}}</span>-->
  <!--                  </div>-->
  <!--                </template>-->
  <!--              </vue-good-table>-->
  <!--            </b-tab>-->

  <!--             &lt;!&ndash; Transfers Table &ndash;&gt;-->
  <!--            <b-tab :title="$t('StockTransfers')">-->
  <!--              <vue-good-table-->
  <!--                mode="remote"-->
  <!--                :columns="columns_transfers"-->
  <!--                :totalRows="totalRows_transfers"-->
  <!--                :rows="transfers"-->
  <!--                @on-page-change="PageChangeTransfer"-->
  <!--                @on-per-page-change="onPerPageChangeTransfer"-->
  <!--                @on-search="onSearch_transfers"-->
  <!--                :search-options="{-->
  <!--                  placeholder: $t('Search_this_table'),-->
  <!--                  enabled: true,-->
  <!--                }"-->
  <!--                :pagination-options="{-->
  <!--                  enabled: true,-->
  <!--                  mode: 'records',-->
  <!--                  nextLabel: 'next',-->
  <!--                  prevLabel: 'prev',-->
  <!--                }"-->
  <!--                styleClass="tableOne table-hover vgt-table"-->
  <!--              >-->
  <!--              <div slot="table-actions" class="mt-2 mb-3">-->
  <!--                <b-button @click="Transfer_PDF()" size="sm" variant="outline-success ripple m-1">-->
  <!--                  <i class="i-File-Copy"></i> PDF-->
  <!--                </b-button>-->
  <!--              </div>-->
  <!--               -->
  <!--              </vue-good-table>-->
  <!--            </b-tab>-->

  <!--             &lt;!&ndash; Adjustment Table &ndash;&gt;-->
  <!--            <b-tab :title="$t('Adjustment')">-->
  <!--              <vue-good-table-->
  <!--                mode="remote"-->
  <!--                :columns="columns_adjustments"-->
  <!--                :totalRows="totalRows_adjustments"-->
  <!--                :rows="adjustments"-->
  <!--                @on-page-change="PageChangeAdjustment"-->
  <!--                @on-per-page-change="onPerPageChangeAdjustment"-->
  <!--                @on-search="onSearch_adjustments"-->
  <!--                :search-options="{-->
  <!--                  placeholder: $t('Search_this_table'),-->
  <!--                  enabled: true,-->
  <!--                }"-->
  <!--                :pagination-options="{-->
  <!--                  enabled: true,-->
  <!--                  mode: 'records',-->
  <!--                  nextLabel: 'next',-->
  <!--                  prevLabel: 'prev',-->
  <!--                }"-->
  <!--                styleClass="tableOne table-hover vgt-table"-->
  <!--              >-->
  <!--               <div slot="table-actions" class="mt-2 mb-3">-->
  <!--                <b-button @click="Adjustment_PDF()" size="sm" variant="outline-success ripple m-1">-->
  <!--                  <i class="i-File-Copy"></i> PDF-->
  <!--                </b-button>-->
  <!--              </div>-->
  <!--              </vue-good-table>-->
  <!--            </b-tab>-->

  <!--             -->

  <!--          </b-tabs>-->
  <!--        </b-card>-->
  <!--      </b-col>-->
  <!--    </b-row>-->
  <!--  </div>-->
</template>
