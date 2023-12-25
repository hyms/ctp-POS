<script setup>
import { computed, ref } from "vue";
import Layout from "@/Layouts/Authenticated.vue";
import { api, helpers, labels, labelsNew } from "@/helpers";
import Snackbar from "@/Components/snackbar.vue";
import { router } from "@inertiajs/vue3";

const props = defineProps({
    id: String,
    errors: Object,
});
const snackbar = ref({
    view: false,
    color: "",
    text: "",
});
const loading = ref(true);
const tab = ref(null);

//       totalRows_quotations: "",
//       totalRows_sales: "",
//       totalRows_sales_return: "",
//       totalRows_purchases_return: "",
//       totalRows_purchases: "",
//       totalRows_transfers: "",
//       totalRows_adjustments: "",

const search_sales = ref("");
const search_purchases = ref("");
const search_quotations = ref("");
const search_return_sales = ref("");
const search_return_purchases = ref("");
const search_transfers = ref("");
const search_adjustments = ref("");

const purchases = ref([]);
const sales = ref([]);
const quotations = ref([]);
// const sales_return = ref([]);
// const purchases_return = ref([]);
const transfers = ref([]);
const adjustments = ref([]);

// const columns_quotations = ref([
//     { title: labelsNew.username, key: "username" },
//     { title: labelsNew.date, key: "date" },
//     { title: labelsNew.Reference, key: "Ref" },
//     { title: labelsNew.Customer, key: "client_name" },
//     { title: labelsNew.warehouse, key: "warehouse_name" },
//     { title: labelsNew.Total, key: "GrandTotal" },
//     { title: labelsNew.Status, key: "statut" },
// ]);
const columns_sales = ref([
    { title: labelsNew.username, key: "username" },
    { title: labelsNew.Reference, key: "Ref" },
    { title: labelsNew.Customer, key: "client_name" },
    { title: labelsNew.warehouse, key: "warehouse_name" },
    { title: labelsNew.Status, key: "statut" },
    { title: labelsNew.Total, key: "GrandTotal" },
    { title: labelsNew.Paid, key: "paid_amount" },
    { title: labelsNew.Due, key: "due" },
    { title: labelsNew.PaymentStatus, key: "payment_status" },
    // { title: labelsNew.Shipping_status, key: "shipping_status" },
]);
// const columns_purchases = ref([
//   { title: labelsNew.username, key: "username" },
//   { title: labelsNew.Reference, key: "Ref" },
//   { title: labelsNew.Supplier, key: "provider_name" },
//   { title: labelsNew.warehouse, key: "warehouse_name" },
//   { title: labelsNew.Status, key: "statut" },
//   { title: labelsNew.Total, key: "GrandTotal" },
//   { title: labelsNew.Paid, key: "paid_amount" },
//   { title: labelsNew.Due, key: "due" },
//   { title: labelsNew.PaymentStatus, key: "payment_status" },
// ]);
const columns_transfers = ref([
    { title: labelsNew.username, key: "username" },
    { title: labelsNew.date, key: "date" },
    { title: labelsNew.Reference, key: "Ref" },
    { title: labelsNew.FromWarehouse, key: "from_warehouse" },
    { title: labelsNew.ToWarehouse, key: "to_warehouse" },
    { title: labelsNew.Items, key: "items" },
    { title: labelsNew.Total, key: "GrandTotal" },
    { title: labelsNew.Status, key: "statut" },
]);
const columns_adjustments = ref([
    { title: labelsNew.username, key: "username" },
    { title: labelsNew.date, key: "date" },
    { title: labelsNew.Reference, key: "Ref" },
    { title: labelsNew.warehouse, key: "warehouse_name" },
    { title: labelsNew.TotalProducts, key: "items" },
]);

//--------------------------- get_sales_by_user -------------\\
function Get_Sales(page) {
    api.get({
        url: "/report/get_sales_by_user",
        params: {
            page,
            limit: "",
            search: "", //search_sales +
            id: props.id,
        },
        loadingItem: loading,
        snackbar,
        onSuccess: (data) => {
            sales.value = data.sales;
            // this.totalRows_sales = response.data.totalRows;
        },
    });
}

//--------------------------- Get Purchases By user -------------\\
function Get_Purchases(page) {
    api.get({
        url: "/report/get_purchases_by_user",
        params: {
            page,
            limit: "",
            search: "", //search_purchases +
            id: props.id,
        },
        loadingItem: loading,
        snackbar,
        onSuccess: (data) => {
            sales.purchases = data.purchases;
            // this.totalRows_purchases = response.data.totalRows;
        },
    });
}

//     //--------------------------- Get Quotations By user -------------\\
//     Get_Quotations(page) {
//       axios
//         .get(
//           "report/get_quotations_by_user?page=" +
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
//--------------------------- Get Transfers By user -------------\\
function Get_Transfers(page) {
    api.get({
        url: "/report/get_transfer_by_user",
        params: {
            page,
            limit: "",
            search: "", //search_transfers +
            id: props.id,
        },
        loadingItem: loading,
        snackbar,
        onSuccess: (data) => {
            transfers.value = data.transfers;
            // this.totalRows_transfers = response.data.totalRows;
        },
    });
}

//--------------------------- Get adjustment By user -------------\\
function Get_adjustments(page) {
    api.get({
        url: "/report/get_adjustment_by_user",
        params: {
            page,
            limit: "",
            search: "", //search_adjustments +
            id: props.id,
        },
        loadingItem: loading,
        snackbar,
        onSuccess: (data) => {
            adjustments.value = data.adjustments;
            // this.totalRows_adjustments = response.data.totalRows;
        },
    });
}
const tabVal = computed({
    get() {
        switch (tab.value) {
            case "sales":
                Get_Sales();
                break;
            case "transfer":
                Get_Transfers();
                break;
            case "adjustment":
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
    },
});
</script>
<template>
    <layout>
        <snackbar
            v-model="snackbar.view"
            :text="snackbar.text"
            :color="snackbar.color"
        ></snackbar>
        <v-card>
            <v-tabs v-model="tabVal" color="primary">
                <v-tab value="sales">{{ labelsNew.Sales }}</v-tab>
                <v-tab value="transfer">{{ labelsNew.StockTransfers }}</v-tab>
                <v-tab value="adjustment">{{ labelsNew.Adjustment }}</v-tab>
            </v-tabs>
            <v-card-text>
                <v-window v-model="tabVal">
                    <v-window-item value="sales">
                        <v-row align="center" class="mb-1">
                            <v-col cols="12" sm="6">
                                <v-text-field
                                    v-model="search_sales"
                                    prepend-icon="fas fa-search"
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
                            :headers="columns_sales"
                            :items="sales"
                            :search="search_sales"
                            hover
                            :no-data-text="labels.no_data_table"
                            :loading="loading"
                        >
                            <template v-slot:item.statut="{ item }">
                                <v-chip
                                    :color="
                                        helpers.statutSaleColor(item.statut)
                                    "
                                    variant="tonal"
                                    size="x-small"
                                    >{{ helpers.statutSale(item.statut) }}
                                </v-chip>
                            </template>
                            <template v-slot:item.payment_status="{ item }">
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
                            <template v-slot:item.Ref="{ item }">
                                <v-btn
                                    variant="tonal"
                                    size="x-small"
                                    color="default"
                                    :text="item.Ref"
                                    @click="
                                        router.visit(
                                            '/sales/detail/' + item.sale_id
                                        )
                                    "
                                ></v-btn>
                            </template>
                        </v-data-table>
                    </v-window-item>
                    <v-window-item value="transfer">
                        <v-row align="center" class="mb-1">
                            <v-col cols="12" sm="6">
                                <v-text-field
                                    v-model="search_transfers"
                                    prepend-icon="fas fa-search"
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
                            :headers="columns_transfers"
                            :items="transfers"
                            :search="search_transfers"
                            hover
                            :no-data-text="labels.no_data_table"
                            :loading="loading"
                        >
                            <template v-slot:item.statut="{ item }">
                                <v-chip
                                    :color="
                                        helpers.statutSaleColor(item.statut)
                                    "
                                    variant="tonal"
                                    size="x-small"
                                    >{{ helpers.statutSale(item.statut) }}
                                </v-chip>
                            </template>
                        </v-data-table>
                    </v-window-item>
                    <v-window-item value="adjustment">
                        <v-row align="center" class="mb-1">
                            <v-col cols="12" sm="6">
                                <v-text-field
                                    v-model="search_adjustments"
                                    prepend-icon="fas fa-search"
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
                            :headers="columns_adjustments"
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
    <!--              </div>-->
    <!--                <template slot="table-row" slot-scope="props">-->
    <!--                  <div v-if="props.column.field == 'statut'">-->
    <!--                    <span-->
    <!--                      v-if="props.row.statut == 'received'"-->
    <!--                      class="badge badge-outline-success"-->
    <!--                    >{{$t('Received')}}</span>-->
    <!--                    <span-->
    <!--                      v-else-if="props.row.statut == 'pending'"-->
    <!--                      class="badge badge-outline-info"-->
    <!--                    >{{$t('Pending')}}</span>-->
    <!--                    <span v-else class="badge badge-outline-warning">{{$t('Ordered')}}</span>-->
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
    <!--                      :to="'/app/purchases/detail/'+props.row.purchase_id"-->
    <!--                    >-->
    <!--                      <span class="ul-btn__text ml-1">{{props.row.Ref}}</span>-->
    <!--                    </router-link>-->
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
    <!--                  <div v-else-if="props.column.field == 'Ref'">-->
    <!--                    <router-link-->
    <!--                      :to="'/app/sale_return/detail/'+props.row.return_sale_id"-->
    <!--                    >-->
    <!--                      <span class="ul-btn__text ml-1">{{props.row.Ref}}</span>-->
    <!--                    </router-link>-->
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
    <!--                  <div v-else-if="props.column.field == 'Ref'">-->
    <!--                    <router-link-->
    <!--                      :to="'/app/purchase_return/detail/'+props.row.return_purchase_id"-->
    <!--                    >-->
    <!--                      <span class="ul-btn__text ml-1">{{props.row.Ref}}</span>-->
    <!--                    </router-link>-->
    <!--                  </div>-->
    <!--                </template>-->
    <!--              </vue-good-table>-->
    <!--            </b-tab>-->

    <!--          </b-tabs>-->
    <!--        </b-card>-->
    <!--      </b-col>-->
    <!--    </b-row>-->
    <!--  </div>-->
</template>