<script setup>
import { inject, onMounted, ref } from "vue";
import Layout from "@/Layouts/Authenticated.vue";
import { globals, helpers, labels } from "@/helpers";
import VChart from "vue-echarts";

import { use } from "echarts/core";
import { CanvasRenderer } from "echarts/renderers";
import { BarChart, LineChart, PieChart } from "echarts/charts";
import {
    GridComponent,
    LegendComponent,
    TooltipComponent,
} from "echarts/components";

use([
    BarChart,
    PieChart,
    LineChart,
    LegendComponent,
    TooltipComponent,
    GridComponent,
    CanvasRenderer,
]);

const props = defineProps({
    errors: Object,
});

const moment = inject("moment");

const warehouses = ref([]);
const customers = ref({});
const product_report = ref({});
const report_dashboard = ref({});
const sales_report = ref({});

const warehouse_id = ref("");
const stock_alerts = ref([]);
const report_today = ref({
    revenue: 0,
    today_purchases: 0,
    today_sales: 0,
    return_sales: 0,
    return_purchases: 0,
});
const sales = ref([]);
const products = ref([]);
const CurrentMonth = ref("");
const loading = ref(false);
const echartSales = ref({});
const echartProduct = ref({});
const echartCustomer = ref({});
const echartPayment = ref({});

const columns_sales = ref([
    { title: labels.sale.Ref, key: "Ref" },
    { title: labels.sale.client_id, key: "client_name" },
    { title: labels.sale.warehouse_id, key: "warehouse_name" },
    { title: labels.sale.statut, key: "statut" },
    { title: labels.sale.GrandTotal, key: "GrandTotal" },
    { title: labels.sale.paid_amount, key: "paid_amount" },
    { title: labels.sale.due, key: "due" },
    { title: labels.sale.payment_status, key: "payment_status" },
]);
const columns_stock = ref([
    { title: labels.product.code, key: "code" },
    { title: labels.product.name, key: "name" },
    { title: labels.warehouse_text, key: "warehouse" },
    { title: labels.quantity, key: "quantity" },
    { title: labels.alert_quantity, key: "stock_alert" },
]);
const columns_products = ref([
    { title: labels.product.name, key: "name" },
    { title: labels.total_sales, key: "total_sales" },
    { title: labels.total_amount, key: "total" },
]);

//---------------------- Event Select Warehouse ------------------------------\\
function Selected_Warehouse(value) {
    // warehouse_id.value = value;
}

//---------------------------------- Report Dashboard With Echart
function all_dashboard_data(warehouseId = null) {
    loading.value = true;
    axios
        .get("/dashboard_data", { params: { warehouse_id: warehouseId } })
        .then(({ data }) => {
            warehouses.value = data["warehouses"];
            customers.value = data["customers"];
            product_report.value = data["product_report"];
            report_dashboard.value = data["report_dashboard"];
            sales_report.value = data["sales_report"];
            // all_dashboard_data();

            //  router.on("success", (event) => {
            //    let url = event.detail.page.url.split('?');
            //    if (url.length > 1) {
            //      let url_query = url[1].split('=');
            //      if (url_query.length > 1) {
            //        warehouse_id.value = warehouses.find(value => value.id == url_query[1] && url_query[1] != "");
            //      }
            //    }
            //  });
            report_today.value = report_dashboard.value.original.report;
            stock_alerts.value = report_dashboard.value.original.stock_alert;
            products.value = report_dashboard.value.original.products;
            sales.value = report_dashboard.value.original.last_sales;
            const dark_heading = "#c2c6dc";

            echartCustomer.value = {
                color: ["#3c858d", "#05828e", "#588d93", "#8fa8ab", "#0E3B42"],
                tooltip: {
                    show: true,
                    backgroundColor: "rgba(0, 0, 0, .8)",
                },

                formatter: function (params) {
                    return `${params.name}: (${params.data.value} ${labels.sales}) (${params.percent}%)`;
                },

                series: [
                    {
                        name: "Top Customers",
                        type: "pie",
                        radius: "50%",
                        center: "50%",

                        data: customers.value.original,
                        itemStyle: {
                            emphasis: {
                                shadowBlur: 10,
                                shadowOffsetX: 0,
                                shadowColor: "rgba(0, 0, 0, 0.5)",
                            },
                        },
                    },
                ],
            };
            // echartPayment.value = {
            //   tooltip: {
            //     trigger: "axis"
            //   },
            //   legend: {
            //     data: ["Payment sent", "Payment received"]
            //   },
            //   grid: {
            //     left: "3%",
            //     right: "4%",
            //     bottom: "3%",
            //     containLabel: true
            //   },
            //   toolbox: {
            //     feature: {
            //       saveAsImage: {}
            //     }
            //   },
            //   xAxis: {
            //     type: "category",
            //     boundaryGap: false,
            //     data: responseData.payments.original.days
            //   },
            //   yAxis: {
            //     type: "value"
            //   },
            //   series: [
            //     {
            //       name: "Payment sent",
            //       type: "line",
            //       data: responseData.payments.original.payment_sent
            //     },
            //     {
            //       name: "Payment received",
            //       type: "line",
            //       data: responseData.payments.original.payment_received
            //     }
            //   ]
            // };

            echartProduct.value = {
                color: ["#3c858d", "#05828e", "#588d93", "#8fa8ab", "#0E3B42"],
                tooltip: {
                    show: true,
                    backgroundColor: "rgba(0, 0, 0, .8)",
                    formatter: function (params) {
                        return `${params.name}: (${params.value} ${labels.sales})`;
                    },
                },
                series: [
                    {
                        name: labels.top_selling_products,
                        type: "pie",
                        radius: "50%",
                        center: "50%",

                        data: product_report.value.original,
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
            echartSales.value = {
                legend: {
                    borderRadius: 0,
                    orient: "horizontal",
                    x: "right",
                    // data: ["Sales", "Purchases"]
                    data: [labels.sales],
                },
                grid: {
                    left: "8px",
                    right: "8px",
                    bottom: "0",
                    containLabel: true,
                },
                tooltip: {
                    show: true,
                    backgroundColor: "rgba(0, 0, 0, .8)",
                },

                xAxis: [
                    {
                        type: "category",
                        data: sales_report.value.original.days,
                        axisTick: {
                            alignWithLabel: true,
                        },
                        splitLine: {
                            show: false,
                        },
                        axisLabel: {
                            color: dark_heading,
                            interval: 0,
                            rotate: 30,
                        },
                        axisLine: {
                            show: true,
                            color: dark_heading,

                            lineStyle: {
                                color: dark_heading,
                            },
                        },
                    },
                ],
                yAxis: [
                    {
                        type: "value",

                        axisLabel: {
                            color: dark_heading,
                            // formatter: "${value}"
                        },
                        axisLine: {
                            show: false,
                            color: dark_heading,

                            lineStyle: {
                                color: dark_heading,
                            },
                        },
                        min: 0,
                        splitLine: {
                            show: true,
                            interval: "auto",
                        },
                    },
                ],

                series: [
                    {
                        name: labels.sales,
                        data: sales_report.value.original.data,
                        label: { show: true, color: "#5e8592" },
                        type: "bar",
                        color: "#3c858d",
                        smooth: true,
                        itemStyle: {
                            emphasis: {
                                shadowBlur: 10,
                                shadowOffsetX: 0,
                                shadowOffsetY: -2,
                                shadowColor: "rgba(0, 0, 0, 0.3)",
                            },
                        },
                    },
                    // {
                    //   name: "Purchases",
                    //   data: responseData.purchases.original.data,
                    //
                    //   label: {show: false, color: "#0168c1"},
                    //   type: "bar",
                    //   barGap: 0,
                    //   color: "#DDD6FE",
                    //   smooth: true,
                    //   itemStyle: {
                    //     emphasis: {
                    //       shadowBlur: 10,
                    //       shadowOffsetX: 0,
                    //       shadowOffsetY: -2,
                    //       shadowColor: "rgba(0, 0, 0, 0.3)"
                    //     }
                    //   }
                    // }
                ],
            };
        })
        .finally(() => {
            loading.value = false;
        });
}

onMounted(() => {
    all_dashboard_data();
    CurrentMonth.value = moment().format("MMMM");
});
</script>

<template>
    <Layout>
        <!--                <v-card>-->
        <!--                    <v-card-title-->
        <!--                        ><h4>Bienvenido a tu Tablero</h4></v-card-title-->
        <!--                    >-->
        <!--                    <v-card-subtitle></v-card-subtitle>-->
        <!--                </v-card>-->
        <!-- warehouse -->
        <v-row v-if="globals.userPermision(['dashboard'])">
            <v-col sm="4" cols="12">
                <v-select
                    @update:modelValue="Selected_Warehouse"
                    :model-value="warehouse_id"
                    :items="warehouses"
                    :label="labels.filter_by_warehouse"
                    item-title="name"
                    item-value="id"
                    hide-details="auto"
                    clearable
                ></v-select>
            </v-col>
        </v-row>
        <v-row v-if="globals.userPermision(['dashboard'])">
            <v-col md="8" cols="12">
                <v-card :loading="loading">
                    <v-card-title>
                        {{ labels.this_week_sales_purchases }}
                    </v-card-title>
                    <v-card-text>
                        <div class="chart-wrapper">
                            <v-chart
                                v-if="!loading"
                                :option="echartSales"
                                :autoresize="true"
                            ></v-chart>
                        </div>
                    </v-card-text>
                </v-card>
            </v-col>
            <v-col md="4" cols="12">
                <v-card :loading="loading">
                    <v-card-title>
                        {{ labels.top_customers }} ({{ CurrentMonth }})
                    </v-card-title>
                    <v-card-text>
                        <div class="chart-wrapper">
                            <!--              <v-chart v-if="!loading" :option="echartProduct" :autoresize="true"></v-chart>-->
                            <v-chart
                                v-if="!loading"
                                :option="echartCustomer"
                                :autoresize="true"
                            ></v-chart>
                        </div>
                    </v-card-text>
                </v-card>
            </v-col>
        </v-row>
        <v-row v-if="globals.rolePermision(['Admin'])">
            <!-- Stock Alert -->
            <v-col cols="12" md="8">
                <v-card :loading="loading">
                    <v-card-title>
                        {{ labels.stock_alert }}
                    </v-card-title>
                    <v-data-table
                        :headers="columns_stock"
                        :items="stock_alerts"
                        hover
                        :no-data-text="labels.no_data_table"
                    >
                        <template v-slot:item.stock_alert="{ item }">
                            <v-chip color="error" variant="tonal" size="small"
                                >{{ item.stock_alert }}
                            </v-chip>
                        </template>
                    </v-data-table>
                </v-card>
            </v-col>

            <v-col cols="12" md="4">
                <v-card :loading="loading">
                    <v-card-title>
                        {{ labels.top_selling_products }} ({{ CurrentMonth }})
                    </v-card-title>
                    <v-data-table
                        :headers="columns_products"
                        :items="products"
                        hover
                        :no-data-text="labels.no_data_table"
                    >
                    </v-data-table>
                </v-card>
            </v-col>
        </v-row>
        <!-- Last Sales -->
        <v-row>
            <v-col cols="12">
                <v-card>
                    <v-card-title>
                        {{ labels.recent_sales }}
                    </v-card-title>
                    <v-data-table
                        :headers="columns_sales"
                        :items="sales"
                        hover
                        :no-data-text="labels.no_data_table"
                    >
                        <template v-slot:item.statut="{ item }">
                            <v-chip
                                :color="helpers.statutSaleColor(item.statut)"
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
                                    helpers.statusPayment(item.payment_status)
                                }}
                            </v-chip>
                        </template>
                    </v-data-table>
                </v-card>
            </v-col>
        </v-row>
    </Layout>
</template>
<style scoped>
.chart-wrapper {
    height: 300px;
    width: 100%;
}
</style>
