<script setup>
import {onMounted, ref} from "vue";
import Layout from "@/Layouts/Authenticated.vue";
import helper from "@/helpers";
import labels from "@/labels";
import {router} from "@inertiajs/vue3";
import VChart from 'vue-echarts'

import {use} from "echarts/core";
// import ECharts modules manually to reduce bundle size
// import "echarts/lib/chart/pie";
// import "echarts/lib/chart/bar";
// import "echarts/lib/chart/line";
// import "echarts/lib/component/tooltip";
// import "echarts/lib/component/legend";
import {CanvasRenderer} from "echarts/renderers";
import {BarChart, PieChart, LineChart} from 'echarts/charts'
import {LegendComponent, TooltipComponent} from 'echarts/components'

use([
  BarChart,
  PieChart,
  LineChart,
  LegendComponent,
  TooltipComponent,
  CanvasRenderer
]);


const props = defineProps({
  warehouses: Array,
  customers: Object,
  product_report: Object,
  report_dashboard: Object,
  sales_report: Object,
  errors: Object,
});

const warehouse_id = ref("");
const stock_alerts = ref([]);
const report_today = ref({
  revenue: 0,
  today_purchases: 0,
  today_sales: 0,
  return_sales: 0,
  return_purchases: 0
});
const sales = ref([]);
const products = ref([]);
const CurrentMonth = ref("");
const loading = ref(false);
const echartSales = ref({});
const echartProduct = ref({});
const echartCustomer = ref({});
const echartPayment = ref({});

//---------------------- Event Select Warehouse ------------------------------\\
function Selected_Warehouse(value) {
  // if (value === null) {
  //   warehouse_id.value = "";
  // }
  // warehouse_id.value = value;
  // all_dashboard_data();
  router.get("/", {warehouse_id: value}, {
    preserveState: false,
    preserveScroll: false,
    only: [
      'warehouses',
      'customers',
      'product_report',
      'report_dashboard',
      'sales_report',
      'errors',],
  });
}

//---------------------------------- Report Dashboard With Echart
function all_dashboard_data() {
  loading.value = true;

  router.on("success", (event) => {
    // console.log(event.detail.page.url);
    let url = event.detail.page.url.split('?');
    let url_query = url[1].split('=');
    console.log(url_query);
    if (url_query.length > 1) {
      warehouse_id.value = props.warehouses.find(value => value.id == url_query[1] && url_query[1] != "");
    }
  });
  setTimeout(() => {
    report_today.value = props.report_dashboard.original.report;
    stock_alerts.value = props.report_dashboard.original.stock_alert;
    products.value = props.report_dashboard.original.products;
    sales.value = props.report_dashboard.original.last_sales;
    const dark_heading = "#c2c6dc";

    echartCustomer.value = {
      color: ["#6D28D9", "#8B5CF6", "#A78BFA", "#C4B5FD", "#7C3AED"],
      tooltip: {
        show: true,
        backgroundColor: "rgba(0, 0, 0, .8)"
      },

      formatter: function (params) {
        return `${params.name}: (${params.data.value} sales) (${
            params.percent
        }%)`;
      },

      series: [
        {
          name: "Top Customers",
          type: "pie",
          radius: "50%",
          center: "50%",

          data: props.customers.original,
          itemStyle: {
            emphasis: {
              shadowBlur: 10,
              shadowOffsetX: 0,
              shadowColor: "rgba(0, 0, 0, 0.5)"
            }
          }
        }
      ]
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
      color: ["#6D28D9", "#8B5CF6", "#A78BFA", "#C4B5FD", "#7C3AED"],
      tooltip: {
        show: true,
        backgroundColor: "rgba(0, 0, 0, .8)"
      },
      formatter: function (params) {
        return `${params.name}: (${params.value}sales)`;
      },
      series: [
        {
          name: "Top Selling Products",
          type: "pie",
          radius: "50%",
          center: "50%",

          data: props.product_report.original,
          itemStyle: {
            emphasis: {
              shadowBlur: 10,
              shadowOffsetX: 0,
              shadowColor: "rgba(0, 0, 0, 0.5)"
            }
          }
        }
      ]
    };
    echartSales.value = {
      legend: {
        borderRadius: 0,
        orient: "horizontal",
        x: "right",
        // data: ["Sales", "Purchases"]
        data: ["Sales"]
      },
      grid: {
        left: "8px",
        right: "8px",
        bottom: "0",
        containLabel: true
      },
      tooltip: {
        show: true,
        backgroundColor: "rgba(0, 0, 0, .8)"
      },

      xAxis: [
        {
          type: "category",
          data: props.sales_report.original.days,
          axisTick: {
            alignWithLabel: true
          },
          splitLine: {
            show: false
          },
          axisLabel: {
            color: dark_heading,
            interval: 0,
            rotate: 30
          },
          axisLine: {
            show: true,
            color: dark_heading,

            lineStyle: {
              color: dark_heading
            }
          }
        }
      ],
      yAxis: [
        {
          type: "value",

          axisLabel: {
            color: dark_heading
            // formatter: "${value}"
          },
          axisLine: {
            show: false,
            color: dark_heading,

            lineStyle: {
              color: dark_heading
            }
          },
          min: 0,
          splitLine: {
            show: true,
            interval: "auto"
          }
        }
      ],

      series: [
        {
          name: "Sales",
          data: props.sales_report.original.data,
          label: {show: false, color: "#8B5CF6"},
          type: "bar",
          color: "#A78BFA",
          smooth: true,
          itemStyle: {
            emphasis: {
              shadowBlur: 10,
              shadowOffsetX: 0,
              shadowOffsetY: -2,
              shadowColor: "rgba(0, 0, 0, 0.3)"
            }
          }
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
      ]
    };

    loading.value = false;
  }, 1000)

}

onMounted(() => {
  all_dashboard_data();
})

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
    <v-row>
      <v-col sm="4" cols="12">
        <v-select
            @update:modelValue="Selected_Warehouse"
            :model-value="warehouse_id"
            :items="warehouses"
            :label="labels.filter_by_warehouse"
            item-title="name"
            item-value="id"
            variant="outlined"
            density="comfortable"
            hide-details="auto"
            clearable
        ></v-select>
      </v-col>
    </v-row>
    <v-row>
      <v-col md="8" cols="12">
        <v-card class="mb-30" :loading="loading">
          <v-card-item>
            <h4 class="card-title m-0">{{ labels.this_week_sales_purchases }}</h4>
            <div class="chart-wrapper">
              <v-chart v-if="!loading" :options="echartSales" :autoresize="true"></v-chart>
            </div>
          </v-card-item>
        </v-card>
      </v-col>
      <v-col md="4" cols="12">
        <!--        <b-card class="mb-30">-->
        <!--          <h4 class="card-title m-0">{{$t('Top_Selling_Products')}} ({{new Date().getFullYear()}})</h4>-->
        <!--          <div class="chart-wrapper">-->
        <!--            <div v-once class="typo__p text-right" v-if="loading">-->
        <!--              <div class="spinner sm spinner-primary mt-3"></div>-->
        <!--            </div>-->
        <!--            <v-chart v-if="!loading" :options="echartProduct" :autoresize="true"></v-chart>-->
        <!--          </div>-->
        <!--        </b-card>-->
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