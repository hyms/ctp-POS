<script setup>
import { computed, onMounted, ref } from "vue";
import Layout from "@/Layouts/Authenticated.vue";
import Snackbar from "@/Components/snackbar.vue";
import { api, helpers, labels, labelsNew } from "@/helpers";

import { use } from "echarts/core";
import { CanvasRenderer } from "echarts/renderers";
import { BarChart, LineChart, PieChart } from "echarts/charts";
import {
    GridComponent,
    LegendComponent,
    TooltipComponent,
} from "echarts/components";
import VChart from "vue-echarts";
import { router } from "@inertiajs/vue3";

const props = defineProps({
    errors: Object,
});
const snackbar = ref({
    view: false,
    color: "",
    text: "",
});

use([
    BarChart,
    PieChart,
    LineChart,
    LegendComponent,
    TooltipComponent,
    GridComponent,
    CanvasRenderer,
]);

const Stock_Count = ref({});
const Stock_value = ref({});
// const search_quotation = ref("");
const search_sale = ref("");
const search_expense = ref("");
// const search_return_Sale = ref("");
// const search_return_Purchase = ref("");
const loading = ref(false);
const loadingTable = ref(false);
const tab = ref(null);
const Filter_warehouse = ref("");
const sales = ref([]);
// const quotations=ref([]);
const warehouses = ref([]);
const expenses = ref([]);
// const returns_sale=ref([]);
// const returns_purchase=ref([]);
const total = ref({
    sales: "",
    purchases: "",
    ReturnPurchase: "",
    ReturnSale: "",
});

//     columns_quotations() {
//       return [
//         {
//           label: this.$t("date"),
//           field: "date",
//         },
//         {
//           label: this.$t("Reference"),
//           field: "Ref",
//         },
//         {
//           label: this.$t("Customer"),
//           field: "client_name",
//         },
//         {
//           label: this.$t("warehouse"),
//           field: "warehouse_name",
//         },
//         {
//           label: this.$t("Total"),
//           field: "GrandTotal",
//         },
//         {
//           label: this.$t("Status"),
//           field: "statut",
//         }
//       ];
//     },
const columns_sales = ref([
    { title: labels.sale.Ref, key: "Ref" },
    { title: labels.sale.client_id, key: "client_name" },
    { title: labels.sale.warehouse_id, key: "warehouse_name" },
    { title: labels.sale.GrandTotal, key: "GrandTotal" },
    { title: labels.sale.paid_amount, key: "paid_amount" },
    { title: labels.sale.due, key: "due" },
    { title: labels.sale.statut, key: "statut" },
    { title: labels.sale.payment_status, key: "payment_status" },
    // { title: labels.sale.shipping_status, key: "shipping_status" },
]);
//     columns_returns_sale() {
//       return [
//         {
//           label: this.$t("Reference"),
//           field: "Ref",
//         },
//         {
//           label: this.$t("Customer"),
//           field: "client_name",
//         },
//         {
//           label: this.$t("Sale_Ref"),
//           field: "sale_ref",
//         },
//         {
//           label: this.$t("warehouse"),
//           field: "warehouse_name",
//         },
//
//         {
//           label: this.$t("Total"),
//           field: "GrandTotal",
//         },
//         {
//           label: this.$t("Paid"),
//           field: "paid_amount",
//         },
//         {
//           label: this.$t("Due"),
//           field: "due",
//         },
//          {
//           label: this.$t("Status"),
//           field: "statut",
//         },
//         {
//           label: this.$t("PaymentStatus"),
//           field: "payment_status",
//         }
//       ];
//     },
//     columns_returns_purchase() {
//       return [
//         {
//           label: this.$t("Reference"),
//           field: "Ref",
//         },
//         {
//           label: this.$t("Supplier"),
//           field: "provider_name",
//         },
//         {
//           label: this.$t("warehouse"),
//           field: "warehouse_name",
//         },
//         {
//           label: this.$t("Purchase_Ref"),
//           field: "purchase_ref",
//         },
//
//         {
//           label: this.$t("Total"),
//           field: "GrandTotal",
//         },
//         {
//           label: this.$t("Paid"),
//           field: "paid_amount",
//         },
//         {
//           label: this.$t("Due"),
//           field: "due",
//         },
//         {
//           label: this.$t("Status"),
//           field: "statut",
//         },
//         {
//           label: this.$t("PaymentStatus"),
//           field: "payment_status",
//         }
//       ];
//     },
const columns_Expense = ref([
    { title: labels.expense.date, key: "date" },
    { title: labels.expense.ref, key: "Ref" },
    { title: labels.expense.warehouse_id, key: "warehouse_name" },
    { title: labels.expense.details, key: "details" },
    { title: labels.expense.amount, key: "amount" },
    { title: labels.expense.category_id, key: "category_name" },
]);

//---------------------- Event Select Warehouse ------------------------------\\
function Selected_Warehouse(value) {
    Filter_warehouse.value = value;
    if (value === null) {
        Filter_warehouse.value = "";
    }

    Get_Reports();
    switch (tab.value) {
        case "sales":
            Get_Sales(1);
            break;
        case "expenses":
            Get_Expenses(1);
            break;
        // case 'quotations':
        //   Get_Quotations();
        // break;
        // case 'returns':
        //   Get_Returns();
        // break;
    }
    // Get_Quotations(1);
    // Get_Returns_Sale(1);
    // Get_Returns_Purchase(1);
}

//------------------------------ Show Reports -------------------------\\
function Get_Reports() {
    api.get({
        url: "/report/warehouse_report/list",
        params: {
            warehouse_id: Filter_warehouse.value,
        },
        loadingItem: loading,
        snackbar,
        onSuccess: (data) => {
            total.value = data.data;
            warehouses.value = data.warehouses;
        },
    });
}

//--------------------------- Get sales By warehouse -------------\\
function Get_Sales(page) {
    api.get({
        url: "/report/sales_warehouse",
        params: {
            page,
            limit: "",
            warehouse_id: Filter_warehouse.value,
            search: search_sale.value,
        },
        loadingItem: loadingTable,
        snackbar,
        onSuccess: (data) => {
            sales.value = data.sales;
        },
    });
}

//     //--------------------------- Get Quotations By Warehouse -------------\\
//     Get_Quotations(page) {
//       axios
//         .get(
//           "/report/quotations_warehouse?page=" +
//             page +
//             "&limit=" +
//             this.limit_quotations +
//             "&warehouse_id=" +
//             this.Filter_warehouse +
//             "&search=" +
//             this.search_quotation
//         )
//         .then(response => {
//           this.quotations = response.data.quotations;
//           this.totalRows_quotations = response.data.totalRows;
//         })
//         .catch(response => {});
//     },
//
//     //--------------------------- Get Returns Sale By warehouse -------------\\
//     Get_Returns_Sale(page) {
//       axios
//         .get(
//           "/report/returns_sale_warehouse?page=" +
//             page +
//             "&limit=" +
//             this.limit_returns_Sale +
//             "&warehouse_id=" +
//             this.Filter_warehouse +
//             "&search=" +
//             this.search_return_Sale
//         )
//         .then(response => {
//           this.returns_sale = response.data.returns_sale;
//           this.totalRows_Return_sale = response.data.totalRows;
//         })
//         .catch(response => {});
//     },
//
//     //--------------------------- Get Returns Purchase By warehouse -------------\\
//     Get_Returns_Purchase(page) {
//       axios
//         .get(
//           "/report/returns_purchase_warehouse?page=" +
//             page +
//             "&limit=" +
//             this.limit_returns_Purchase +
//             "&warehouse_id=" +
//             this.Filter_warehouse +
//             "&search=" +
//             this.search_return_Purchase
//         )
//         .then(response => {
//           this.returns_purchase = response.data.returns_purchase;
//           this.totalRows_Return_purchase = response.data.totalRows;
//         })
//         .catch(response => {});
//     },
//
//--------------------------- Get Expenses By warehouse -------------\\
function Get_Expenses(page) {
    api.get({
        url: "/report/expenses_warehouse",
        params: {
            page,
            limit: "",
            warehouse_id: Filter_warehouse.value,
            search: search_expense.value,
        },
        loadingItem: loadingTable,
        snackbar,
        onSuccess: (data) => {
            expenses.value = data.expenses;
        },
    });
}
const tabVal = computed({
    get() {
        switch (tab.value) {
            case "sales":
                Get_Sales(1);
                break;
            case "expenses":
                Get_Expenses(1);
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
    },
});
//---------------------------------- Report Warhouse Count Stock
function report_with_echart() {
    api.get({
        url: `/report/warhouse_count_stock`,
        loadingItem: loading,
        snackbar,
        onSuccess: (responseData) => {
            const dark_heading = "#c2c6dc";

            Stock_Count.value = {
                color: ["#3c858d", "#05828e", "#588d93", "#8fa8ab", "#0E3B42"],
                tooltip: {
                    show: true,
                    backgroundColor: "rgba(0, 0, 0, .9)",
                    formatter: function (params) {
                        return `(${params.value} Items)<br />(${params.data.value1} Cant)`;
                    },
                },
                legend: {
                    orient: "vertical",
                    left: "left",
                    data: responseData["warehouses"],
                },
                series: [
                    {
                        name: "Agencia Stock",
                        type: "pie",
                        radius: "50%",
                        center: "50%",

                        data: responseData["stock_count"],
                        emphasis: {
                            itemStyle: {
                                shadowBlur: 10,
                                shadowOffsetX: 0,
                                shadowColor: "rgba(0, 0, 0, 0.5)",
                            },
                        },
                    },
                ],
            };
            Stock_value.value = {
                color: ["#3c858d", "#05828e", "#588d93", "#8fa8ab", "#0E3B42"],
                tooltip: {
                    show: true,
                    backgroundColor: "rgba(0, 0, 0, .9)",
                    formatter: function (params) {
                        return `(Stock valorXprecio : ${params.value})<br />(Stock valorXcosto : ${params.data.value1})`;
                    },
                },

                legend: {
                    orient: "vertical",
                    left: "left",
                    data: responseData["warehouses"],
                },

                series: [
                    {
                        name: "Agencia Stock",
                        type: "pie",
                        radius: "50%",
                        center: "50%",

                        data: responseData["stock_value"],
                        emphasis: {
                            itemStyle: {
                                shadowBlur: 10,
                                shadowOffsetX: 0,
                                shadowColor: "rgba(0, 0, 0, 0.5)",
                            },
                        },
                    },
                ],
            };
        },
    });
}
//end Methods
//

//----------------------------- Created function------------------- \\

onMounted(() => {
    report_with_echart();
    Get_Reports();
    // Get_Quotations(1);
    // Get_Returns_Sale(1);
    // Get_Returns_Purchase(1);
    // Get_Expenses(1);
});
</script>
<template>
    <layout>
        <snackbar
            v-model="snackbar.view"
            :text="snackbar.text"
            :color="snackbar.color"
        ></snackbar>

        <v-row class="mb-5">
            <!-- warehouse -->
            <v-col lg="3" md="6" cols="12">
                <v-select
                    @update:modelValue="Selected_Warehouse"
                    :model-value="Filter_warehouse"
                    :items="helpers.toArraySelect(warehouses)"
                    :label="labels.filter_by_warehouse"
                    hide-details="auto"
                    clearable
                ></v-select>
            </v-col>
            <v-col cols="12">
                <v-row>
                    <!-- ICON BG -->
                    <v-col md="3" sm="6" cols="12">
                        <v-card class="mb-30 text-center" :loading="loading">
                            <v-card-text>
                                <v-row align="center" no-gutters>
                                    <v-col class="text-center">
                                        <v-icon
                                            color="primary"
                                            icon="fas fa-shopping-cart"
                                            size="68"
                                        ></v-icon>
                                    </v-col>
                                    <v-col class="text-h6">
                                        <p class="text-disabled mt-2 mb-0">
                                            {{ labelsNew.Sales }}
                                        </p>
                                        <p
                                            class="text-primary text-24 line-height-1 mb-2"
                                        >
                                            {{ loading ? "-" : total.sales }}
                                        </p>
                                    </v-col>
                                </v-row>
                            </v-card-text>
                        </v-card>
                    </v-col>
                    <!--      <v-col lg="3" md="6" sm="12">-->
                    <!--        <v-card class="card-icon-bg card-icon-bg-primary o-hidden mb-30 text-center">-->
                    <!--          <i class="i-Checkout-Basket"></i>-->
                    <!--          <div class="content">-->
                    <!--            <p class="text-muted mt-2 mb-0">{{$t('Purchases')}}</p>-->
                    <!--            <p class="text-primary text-24 line-height-1 mb-2">{{total.purchases}}</p>-->
                    <!--          </div>-->
                    <!--        </v-card>-->
                    <!--      </v-col>-->
                    <!--      <v-col lg="3" md="6" sm="12">-->
                    <!--        <v-card class="card-icon-bg card-icon-bg-primary o-hidden mb-30 text-center">-->
                    <!--          <i class="i-Turn-Left"></i>-->
                    <!--          <div class="content">-->
                    <!--            <p class="text-muted mt-2 mb-0">{{$t('PurchasesReturn')}}</p>-->
                    <!--            <p class="text-primary text-24 line-height-1 mb-2">{{total.ReturnPurchase}}</p>-->
                    <!--          </div>-->
                    <!--        </v-card>-->
                    <!--      </v-col>-->
                    <!--      <v-col lg="3" md="6" sm="12">-->
                    <!--        <v-card class="card-icon-bg card-icon-bg-primary o-hidden mb-30 text-center">-->
                    <!--          <i class="i-Turn-Right"></i>-->
                    <!--          <div class="content">-->
                    <!--            <p class="text-muted mt-2 mb-0">{{$t('SalesReturn')}}</p>-->
                    <!--            <p class="text-primary text-24 line-height-1 mb-2">{{total.ReturnSale}}</p>-->
                    <!--          </div>-->
                    <!--        </v-card>-->
                    <!--      </v-col>-->
                </v-row>

                <v-row>
                    <v-col cols="12">
                        <v-card class="mb-30">
                            <v-tabs v-model="tabVal" color="primary">
                                <!--                                <v-tab value="quotations">{{ labelsNew.Quotations }}</v-tab>-->
                                <v-tab value="sales">{{
                                    labelsNew.Sales
                                }}</v-tab>
                                <v-tab value="expenses">{{
                                    labelsNew.Expenses
                                }}</v-tab>
                            </v-tabs>
                            <v-card-text>
                                <v-window v-model="tabVal">
                                    <v-window-item value="sales">
                                        <v-row align="center" class="mb-1">
                                            <v-col cols="12" sm="6">
                                                <v-text-field
                                                    v-model="search_sale"
                                                    prepend-icon="fas fa-search"
                                                    hide-details
                                                    :label="labels.search"
                                                    single-line
                                                    variant="underlined"
                                                ></v-text-field>
                                            </v-col>
                                            <v-spacer></v-spacer>
                                        </v-row>
                                        <v-data-table
                                            :headers="columns_sales"
                                            :items="sales"
                                            :search="search_sale"
                                            hover
                                            :no-data-text="labels.no_data_table"
                                            :loading="loading"
                                        >
                                            <template
                                                v-slot:item.statut="{ item }"
                                            >
                                                <v-chip
                                                    :color="
                                                        helpers.statutSaleColor(
                                                            item.statut
                                                        )
                                                    "
                                                    variant="tonal"
                                                    size="x-small"
                                                    >{{
                                                        helpers.statutSale(
                                                            item.statut
                                                        )
                                                    }}
                                                </v-chip>
                                            </template>
                                            <template
                                                v-slot:item.payment_status="{
                                                    item,
                                                }"
                                            >
                                                <v-chip
                                                    :color="
                                                        helpers.statusPaymentColor(
                                                            item.payment_status
                                                        )
                                                    "
                                                    variant="tonal"
                                                    size="x-small"
                                                    >{{
                                                        helpers.statusPayment(
                                                            item.payment_status
                                                        )
                                                    }}
                                                </v-chip>
                                            </template>
                                            <template
                                                v-slot:item.Ref="{ item }"
                                            >
                                                <v-btn
                                                    variant="tonal"
                                                    size="x-small"
                                                    color="default"
                                                    :text="item.Ref"
                                                    @click="
                                                        router.visit(
                                                            '/sales/detail/' +
                                                                item.id
                                                        )
                                                    "
                                                ></v-btn>
                                            </template>
                                        </v-data-table>
                                    </v-window-item>
                                    <v-window-item value="expenses">
                                        <v-row align="center" class="mb-1">
                                            <v-col cols="12" sm="6">
                                                <v-text-field
                                                    v-model="search_expense"
                                                    prepend-icon="fas fa-search"
                                                    hide-details
                                                    :label="labels.search"
                                                    single-line
                                                    variant="underlined"
                                                ></v-text-field>
                                            </v-col>
                                            <v-spacer></v-spacer>
                                        </v-row>
                                        <v-data-table
                                            :headers="columns_Expense"
                                            :items="expenses"
                                            :search="search_expense"
                                            hover
                                            :no-data-text="labels.no_data_table"
                                            :loading="loading"
                                        >
                                        </v-data-table>
                                    </v-window-item>
                                </v-window>
                            </v-card-text>
                            <!--          <v-tabs active-nav-item-class="nav nav-tabs" content-class="mt-3">-->
                            <!--            &lt;!&ndash; Quotations Table &ndash;&gt;-->
                            <!--            <v-tab :title="$t('Quotations')">-->
                            <!--              <vue-good-table-->
                            <!--                mode="remote"-->
                            <!--                :columns="columns_quotations"-->
                            <!--                :totalRows="totalRows_quotations"-->
                            <!--                :rows="quotations"-->
                            <!--                @on-page-change="PageChangeQuotation"-->
                            <!--                @on-per-page-change="onPerPageChangeQuotation"-->
                            <!--                @on-search="onSearch_Quotations"-->
                            <!--                :search-options="{-->
                            <!--                    placeholder: $t('Search_this_table'),-->
                            <!--                    enabled: true,-->
                            <!--                }"-->
                            <!--                :pagination-options="{-->
                            <!--                  enabled: true,-->
                            <!--                  mode: 'records',-->
                            <!--                  nextLabel: 'next',-->
                            <!--                  prevLabel: 'prev',-->
                            <!--                }"-->
                            <!--                styleClass="order-table vgt-table mt-2"-->
                            <!--              >-->
                            <!--               <div slot="table-actions" class="mt-2 mb-3">-->
                            <!--                <v-button @click="Quotation_PDF()" size="sm" variant="outline-success ripple m-1">-->
                            <!--                  <i class="i-File-Copy"></i> PDF-->
                            <!--                </v-button>-->
                            <!--              </div>-->
                            <!--                <template slot="table-row" slot-scope="props">-->
                            <!--                  <div v-if="props.column.field == 'statut'">-->
                            <!--                    <span-->
                            <!--                      v-if="props.row.statut == 'sent'"-->
                            <!--                      class="badge badge-outline-success"-->
                            <!--                    >{{$t('Sent')}}</span>-->
                            <!--                    <span v-else class="badge badge-outline-info">{{$t('Pending')}}</span>-->
                            <!--                  </div>-->
                            <!--                   <div v-else-if="props.column.field == 'Ref'">-->
                            <!--                    <router-link-->
                            <!--                      :to="'/app/quotations/detail/'+props.row.id"-->
                            <!--                    >-->
                            <!--                      <span class="ul-btn__text ml-1">{{props.row.Ref}}</span>-->
                            <!--                    </router-link>-->
                            <!--                  </div>-->
                            <!--                </template>-->
                            <!--              </vue-good-table>-->
                            <!--            </v-tab>-->

                            <!--            &lt;!&ndash; Returns Sale Table &ndash;&gt;-->
                            <!--            <v-tab :title="$t('SalesReturn')">-->
                            <!--              <vue-good-table-->
                            <!--                mode="remote"-->
                            <!--                :columns="columns_returns_sale"-->
                            <!--                :totalRows="totalRows_Return_sale"-->
                            <!--                :rows="returns_sale"-->
                            <!--                @on-page-change="PageChangeReturn_Customer"-->
                            <!--                @on-per-page-change="onPerPageChangeReturn_Sale"-->
                            <!--                :pagination-options="{-->
                            <!--                    enabled: true,-->
                            <!--                    mode: 'records',-->
                            <!--                    nextLabel: 'next',-->
                            <!--                    prevLabel: 'prev',-->
                            <!--                  }"-->
                            <!--                @on-search="onSearch_Return_Sale"-->
                            <!--                :search-options="{-->
                            <!--                    placeholder: $t('Search_this_table'),-->
                            <!--                    enabled: true,-->
                            <!--                }"-->
                            <!--                styleClass="order-table vgt-table mt-2"-->
                            <!--              >-->
                            <!--               <div slot="table-actions" class="mt-2 mb-3">-->
                            <!--                <v-button @click="Sale_Return_PDF()" size="sm" variant="outline-success ripple m-1">-->
                            <!--                  <i class="i-File-Copy"></i> PDF-->
                            <!--                </v-button>-->
                            <!--              </div>-->
                            <!--                <template slot="table-row" slot-scope="props">-->
                            <!--                  <div v-if="props.column.field == 'statut'">-->
                            <!--                    <span-->
                            <!--                      v-if="props.row.statut == 'received'"-->
                            <!--                      class="badge badge-outline-success"-->
                            <!--                    >{{$t('Received')}}</span>-->
                            <!--                    <span v-else class="badge badge-outline-info">{{$t('Pending')}}</span>-->
                            <!--                  </div>-->

                            <!--                  <div v-else-if="props.column.field == 'payment_status'">-->
                            <!--                    <span-->
                            <!--                      v-if="props.row.payment_status == 'paid'"-->
                            <!--                      class="badge badge-outline-success"-->
                            <!--                    >{{$t('Paid')}}</span>-->
                            <!--                    <span-->
                            <!--                      v-else-if="props.row.payment_status == 'partial'"-->
                            <!--                      class="badge badge-outline-primary"-->
                            <!--                    >{{$t('partial')}}</span>-->
                            <!--                    <span v-else class="badge badge-outline-warning">{{$t('Unpaid')}}</span>-->
                            <!--                  </div>-->
                            <!--                   <div v-else-if="props.column.field == 'Ref'">-->
                            <!--                    <router-link-->
                            <!--                      :to="'/app/sale_return/detail/'+props.row.id"-->
                            <!--                    >-->
                            <!--                      <span class="ul-btn__text ml-1">{{props.row.Ref}}</span>-->
                            <!--                    </router-link>-->
                            <!--                  </div>-->
                            <!--                  <div v-else-if="props.column.field == 'sale_ref' && props.row.sale_id">-->
                            <!--                  <router-link-->
                            <!--                    :to="'/app/sales/detail/'+props.row.sale_id"-->
                            <!--                  >-->
                            <!--                    <span class="ul-btn__text ml-1">{{props.row.sale_ref}}</span>-->
                            <!--                  </router-link>-->
                            <!--                </div>-->
                            <!--                </template>-->
                            <!--              </vue-good-table>-->
                            <!--            </v-tab>-->

                            <!--            &lt;!&ndash; Returns Purchase Table &ndash;&gt;-->
                            <!--            <v-tab :title="$t('PurchasesReturn')">-->
                            <!--              <vue-good-table-->
                            <!--                mode="remote"-->
                            <!--                :columns="columns_returns_purchase"-->
                            <!--                :totalRows="totalRows_Return_purchase"-->
                            <!--                :rows="returns_purchase"-->
                            <!--                @on-page-change="PageChangeReturn_Purchase"-->
                            <!--                @on-per-page-change="onPerPageChangeReturn_Purchase"-->
                            <!--                :pagination-options="{-->
                            <!--                  enabled: true,-->
                            <!--                  mode: 'records',-->
                            <!--                  nextLabel: 'next',-->
                            <!--                  prevLabel: 'prev',-->
                            <!--                }"-->
                            <!--                @on-search="onSearch_Return_Purchase"-->
                            <!--                :search-options="{-->
                            <!--                    placeholder: $t('Search_this_table'),-->
                            <!--                    enabled: true,-->
                            <!--                }"-->
                            <!--                styleClass="order-table vgt-table mt-2"-->
                            <!--              >-->
                            <!--               <div slot="table-actions" class="mt-2 mb-3">-->
                            <!--                <v-button @click="Returns_Purchase_PDF()" size="sm" variant="outline-success ripple m-1">-->
                            <!--                  <i class="i-File-Copy"></i> PDF-->
                            <!--                </v-button>-->
                            <!--              </div>-->
                            <!--                <template slot="table-row" slot-scope="props">-->
                            <!--                  <div v-if="props.column.field == 'statut'">-->
                            <!--                    <span-->
                            <!--                      v-if="props.row.statut == 'completed'"-->
                            <!--                      class="badge badge-outline-success"-->
                            <!--                    >{{$t('complete')}}</span>-->
                            <!--                    <span v-else class="badge badge-outline-info">{{$t('Pending')}}</span>-->
                            <!--                  </div>-->

                            <!--                  <div v-else-if="props.column.field == 'payment_status'">-->
                            <!--                    <span-->
                            <!--                      v-if="props.row.payment_status == 'paid'"-->
                            <!--                      class="badge badge-outline-success"-->
                            <!--                    >{{$t('Paid')}}</span>-->
                            <!--                    <span-->
                            <!--                      v-else-if="props.row.payment_status == 'partial'"-->
                            <!--                      class="badge badge-outline-primary"-->
                            <!--                    >{{$t('partial')}}</span>-->
                            <!--                    <span v-else class="badge badge-outline-warning">{{$t('Unpaid')}}</span>-->
                            <!--                  </div>-->
                            <!--                   <div v-else-if="props.column.field == 'Ref'">-->
                            <!--                    <router-link-->
                            <!--                      :to="'/app/purchase_return/detail/'+props.row.id"-->
                            <!--                    >-->
                            <!--                      <span class="ul-btn__text ml-1">{{props.row.Ref}}</span>-->
                            <!--                    </router-link>-->
                            <!--                  </div>-->
                            <!--                   <div v-else-if="props.column.field == 'purchase_ref' && props.row.purchase_id">-->
                            <!--                    <router-link-->
                            <!--                      :to="'/app/purchases/detail/'+props.row.purchase_id"-->
                            <!--                    >-->
                            <!--                      <span class="ul-btn__text ml-1">{{props.row.purchase_ref}}</span>-->
                            <!--                    </router-link>-->
                            <!--                  </div>-->
                            <!--                </template>-->
                            <!--              </vue-good-table>-->
                            <!--            </v-tab>-->
                        </v-card>
                    </v-col>
                </v-row>
                <v-row class="mt-3">
                    <v-col md="6" cols="12">
                        <v-card class="mb-30" :loading="loading">
                            <v-card-title>
                                {{ labelsNew.Total_Items_Quantity }}
                            </v-card-title>
                            <v-card-text>
                                <v-chart
                                    v-if="!loading"
                                    class="chart-wrapper"
                                    :option="Stock_Count"
                                    :autoresize="true"
                                ></v-chart>
                            </v-card-text>
                        </v-card>
                    </v-col>
                    <v-col md="6" cols="12">
                        <v-card class="mb-30" :loading="loading">
                            <v-card-title>
                                {{ labelsNew.Value_by_Cost_and_Price }}
                            </v-card-title>
                            <v-card-text>
                                <v-chart
                                    v-if="!loading"
                                    class="chart-wrapper"
                                    :option="Stock_value"
                                    :autoresize="true"
                                ></v-chart>
                            </v-card-text>
                        </v-card>
                    </v-col>
                </v-row>
            </v-col>
        </v-row>
    </layout>
</template>
<style scoped>
.chart-wrapper {
    height: 300px;
    width: 100%;
}
</style>
