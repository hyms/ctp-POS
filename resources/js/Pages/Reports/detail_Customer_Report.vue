<script setup>
import { computed, ref } from "vue";
import Layout from "@/Layouts/Authenticated.vue";
import ExportBtn from "@/Components/buttons/ExportBtn.vue";
import { router, usePage } from "@inertiajs/vue3";
import helper from "@/helpers";
import labels from "@/labels";

const props = defineProps({
    report: Object,
    client_id: Number,
});
const currency = computed(() => usePage().props.currency);

const loading = ref(false);
const tab = ref(null);
const search_sales = ref("");
const search_payments = ref("");
const search_quotations = ref("");
const search_return_sales = ref("");
const totalRows_quotations = ref("");
const totalRows_sales = ref("");
const totalRows_returns = ref("");
const totalRows_payments = ref("");
const payments = ref([]);
const sales = ref([]);
const quotations = ref([]);
const returns_customer = ref([]);
const client = ref({
    id: "",
    name: "",
    total_sales: 0,
    total_amount: 0,
    total_paid: 0,
    due: 0,
});
const fields_quotations = ref([
    { title: "Fecha", key: "date" },
    { title: "Codigo", key: "Ref" },
    { title: "Cliente", key: "client_name" },
    { title: "Agencia", key: "warehouse_name" },
    { title: "Total", key: "GrandTotal" },
    { title: "Estado", key: "statut" },
]);
const fields_sales = ref([
    { title: "Codigo", key: "Ref" },
    { title: "Cliente", key: "client_name" },
    { title: "Agencia", key: "warehouse_name" },
    { title: "Total", key: "GrandTotal" },
    { title: "Pagado", key: "paid_amount" },
    { title: "Deuda", key: "due" },
    { title: "Estado", key: "statut" },
    { title: "Estado de Pago", key: "payment_status" },
    // {title: "Estado de Envio", key: "shipping_status"},
]);
const fields_sales_export = ref({
    Codigo: "Ref",
    Cliente: "client_name",
    Agencia: "warehouse_name",
    Total: "GrandTotal",
    Pagado: "paid_amount",
    Deuda: "due",
    Estado: "statut",
    "Estado de Pago": "payment_status",
    // "Estado de Envio": "shipping_status",
});
const fields_returns = ref([
    { title: "Codigo", key: "Ref" },
    { title: "Cliente", key: "client_name" },
    { title: "Codigo de Venta", key: "sale_ref" },
    { title: "Agencia", key: "warehouse_name" },
    { title: "Total", key: "GrandTotal" },
    { title: "Monto de Pago", key: "paid_amount" },
    { title: "Deuda", key: "due" },
    { title: "Estado", key: "statut" },
    { title: "Estado de Pago", key: "payment_status" },
]);
const fields_payments = ref([
    { title: "Fecha", key: "date" },
    { title: "Codigo", key: "Ref" },
    { title: "Codigo de Venta", key: "Sale_Ref" },
    { title: "Tipo de Pago", key: "Reglement" },
    { title: "Monto de Pago", key: "montant" },
]);
const fields_payments_export = ref({
    Fecha: "date",
    Codigo: "Ref",
    "Codigo de Venta": "Sale_Ref",
    "Tipo de Pago": "Reglement",
    "Monto de Pago": "montant",
});

//--------------------------- Get sales By Customer -------------\\
function Get_Sales() {
    loading.value = true;
    axios
        .get("/report/client_sales", { params: { id: props.client_id } })
        .then((response) => {
            sales.value = response.data.sales;
        })
        .catch((response) => {})
        .finally(() => {
            loading.value = false;
        });
}

//--------------------------- Get Payments By Customer -------------\\
function Get_Payments() {
    loading.value = true;
    axios
        .get("/report/client_payments", { params: { id: props.client_id } })
        .then((response) => {
            payments.value = response.data.payments;
        })
        .catch((response) => {})
        .finally(() => {
            loading.value = false;
        });
}

//
//     //--------------------------- Get Quotations By Customer -------------\\
//     Get_Quotations(page) {
//       axios
//         .get(
//           "report/client_quotations?page=" +
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
//           this.isLoading = false;
//         })
//         .catch(response => {
//           setTimeout(() => {
//             this.isLoading = false;
//           }, 500);
//         });
//     },
//
//     //--------------------------- Get Returns By Customer -------------\\
//     Get_Returns(page) {
//       axios
//         .get(
//           "/report/client_returns?page=" +
//             page +
//             "&limit=" +
//             this.limit_returns +
//             "&search=" +
//             this.search_return_sales +
//             "&id=" +
//             this.$route.params.id
//         )
//         .then(response => {
//           this.returns_customer = response.data.returns_customer;
//           this.totalRows_returns = response.data.totalRows;
//         })
//         .catch(response => {});
//     }
//   }, //end Methods
//
const tabVal = computed({
    get() {
        switch (tab.value) {
            case "sales":
                Get_Sales();
                break;
            case "payments":
                Get_Payments();
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
                            <v-col class="text-h6" cols="6">
                                <p class="text-disabled mt-2 mb-0">Ventas</p>
                                <p
                                    class="text-primary text-24 line-height-1 mb-2"
                                >
                                    {{ report.total_sales }}
                                </p>
                            </v-col>
                        </v-row>
                    </v-card-text>
                </v-card>
            </v-col>
            <v-col md="3" sm="6" cols="12">
                <v-card class="mb-30 text-center">
                    <v-card-text>
                        <v-row align="center" no-gutters>
                            <v-col cols="6" class="text-right">
                                <v-icon
                                    color="primary"
                                    icon="mdi-hand-coin-outline"
                                    size="68"
                                ></v-icon>
                            </v-col>
                            <v-col class="text-h6" cols="6">
                                <p class="text-disabled mt-2 mb-0">
                                    Monto Total
                                </p>
                                <p
                                    class="text-primary text-24 line-height-1 mb-2"
                                >
                                    {{ currency }}
                                    {{
                                        helper.formatNumber(
                                            report.total_amount,
                                            2
                                        )
                                    }}
                                </p>
                            </v-col>
                        </v-row>
                    </v-card-text>
                </v-card>
            </v-col>
            <v-col md="3" sm="6" cols="12">
                <v-card class="mb-30 text-center">
                    <v-card-text>
                        <v-row align="center" no-gutters>
                            <v-col cols="6" class="text-right">
                                <v-icon
                                    color="primary"
                                    icon="mdi-cash-multiple"
                                    size="68"
                                ></v-icon>
                            </v-col>
                            <v-col class="text-h6" cols="6">
                                <p class="text-disabled mt-2 mb-0">
                                    Total Pagado
                                </p>
                                <p
                                    class="text-primary text-24 line-height-1 mb-2"
                                >
                                    {{ currency }}
                                    {{
                                        helper.formatNumber(
                                            report.total_paid,
                                            2
                                        )
                                    }}
                                </p>
                            </v-col>
                        </v-row>
                    </v-card-text>
                </v-card>
            </v-col>
            <v-col md="3" sm="6" cols="12">
                <v-card class="mb-30 text-center">
                    <v-card-text>
                        <v-row align="center" no-gutters>
                            <v-col cols="6" class="text-right">
                                <v-icon
                                    color="primary"
                                    icon="mdi-cash-clock"
                                    size="68"
                                ></v-icon>
                            </v-col>
                            <v-col class="text-h6" cols="6">
                                <p class="text-disabled mt-2 mb-0">Deuda</p>
                                <p
                                    class="text-primary text-24 line-height-1 mb-2"
                                >
                                    {{ currency }}
                                    {{ helper.formatNumber(report.due, 2) }}
                                </p>
                            </v-col>
                        </v-row>
                    </v-card-text>
                </v-card>
            </v-col>
        </v-row>
        <v-card>
            <v-tabs v-model="tabVal" color="primary">
                <v-tab value="sales">Ventas</v-tab>
                <!--        <v-tab value="quotations">Citas</v-tab>-->
                <!--        <v-tab value="returns">Devoluciones</v-tab>-->
                <v-tab value="payments">Pagos de Ventas</v-tab>
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
                                <ExportBtn
                                    :data="sales"
                                    :fields="fields_sales_export"
                                    name-file="Ventas"
                                ></ExportBtn>
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
                                    :color="
                                        helper.statusPaymentColor(
                                            item.payment_status
                                        )
                                    "
                                    variant="tonal"
                                    size="x-small"
                                    >{{
                                        helper.statusPayment(
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
                                        router.visit('/sales/detail/' + item.id)
                                    "
                                ></v-btn>
                            </template>
                        </v-data-table>
                    </v-window-item>
                    <v-window-item value="payments">
                        <v-row align="center" class="mb-1">
                            <v-col cols="12" sm="6">
                                <v-text-field
                                    v-model="search_payments"
                                    prepend-icon="fas fa-search"
                                    hide-details
                                    :label="labels.search"
                                    single-line
                                    variant="underlined"
                                ></v-text-field>
                            </v-col>
                            <v-spacer></v-spacer>
                            <v-col cols="auto" class="text-right">
                                <ExportBtn
                                    :data="payments"
                                    :fields="fields_payments_export"
                                    name-file="Pagos"
                                ></ExportBtn>
                            </v-col>
                        </v-row>
                        <v-data-table
                            :headers="fields_payments"
                            :items="payments"
                            :search="search_payments"
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

    <!--                </div>-->
    <!--                   <div v-else-if="props.column.field == 'Ref'">-->
    <!--                    <router-link-->
    <!--                      :to="'/app/sales/detail/'+props.row.id"-->
    <!--                    >-->
    <!--                      <span class="ul-btn__text ml-1">{{props.row.Ref}}</span>-->
    <!--                    </router-link>-->
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
    <!--              </div>-->
    <!--                <template slot="table-row" slot-scope="props">-->
    <!--                  <div v-if="props.column.field == 'statut'">-->
    <!--                    <span-->
    <!--                      v-if="props.row.statut == 'sent'"-->
    <!--                      class="badge badge-outline-success"-->
    <!--                    >{{$t('Sent')}}</span>-->
    <!--                    <span v-else class="badge badge-outline-info">{{$t('Pending')}}</span>-->
    <!--                  </div>-->
    <!--                    <div v-else-if="props.column.field == 'Ref'">-->
    <!--                    <router-link-->
    <!--                      :to="'/app/quotations/detail/'+props.row.id"-->
    <!--                    >-->
    <!--                      <span class="ul-btn__text ml-1">{{props.row.Ref}}</span>-->
    <!--                    </router-link>-->
    <!--                  </div>-->
    <!--                </template>-->
    <!--              </vue-good-table>-->
    <!--            </b-tab>-->

    <!--            &lt;!&ndash; Returns Table &ndash;&gt;-->
    <!--            <b-tab :title="$t('Returns')">-->
    <!--              <vue-good-table-->
    <!--                mode="remote"-->
    <!--                :columns="columns_returns"-->
    <!--                :totalRows="totalRows_returns"-->
    <!--                :rows="returns_customer"-->
    <!--                @on-page-change="PageChangeReturn"-->
    <!--                @on-per-page-change="onPerPageChangeReturn"-->
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
    <!--                      :to="'/app/sale_return/detail/'+props.row.id"-->
    <!--                    >-->
    <!--                      <span class="ul-btn__text ml-1">{{props.row.Ref}}</span>-->
    <!--                    </router-link>-->
    <!--                  </div>-->
    <!--                  <div v-else-if="props.column.field == 'sale_ref' && props.row.sale_id">-->
    <!--                    <router-link-->
    <!--                      :to="'/app/sales/detail/'+props.row.sale_id"-->
    <!--                    >-->
    <!--                      <span class="ul-btn__text ml-1">{{props.row.sale_ref}}</span>-->
    <!--                    </router-link>-->
    <!--                  </div>-->
    <!--                </template>-->
    <!--              </vue-good-table>-->
    <!--            </b-tab>-->

    <!--            &lt;!&ndash; Payments Table &ndash;&gt;-->
    <!--            <b-tab :title="$t('SalesInvoice')">-->
    <!--              <vue-good-table-->
    <!--                mode="remote"-->
    <!--                :columns="columns_payments"-->
    <!--                :totalRows="totalRows_payments"-->
    <!--                :rows="payments"-->
    <!--                @on-page-change="PageChangePayments"-->
    <!--                @on-per-page-change="onPerPageChangePayments"-->
    <!--                @on-search="onSearch_payments"-->
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
    <!--                <b-button @click="Payments_PDF()" size="sm" variant="outline-success ripple m-1">-->
    <!--                  <i class="i-File-Copy"></i> PDF-->
    <!--                </b-button>-->
    <!--              </div>-->
    <!--              </vue-good-table>-->
    <!--            </b-tab>-->
    <!--          </b-tabs>-->
    <!--        </v-card>-->
    <!--      </v-col>-->
</template>
