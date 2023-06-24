<script setup>
import {ref, onMounted, watch} from "vue";
import Layout from "@/Layouts/Authenticated.vue";
import {router} from "@inertiajs/vue3";
import helper from "@/helpers";
import Snackbar from "@/Components/snackbar.vue";

const props = defineProps({
  clients: Object,
  warehouses: Object,
  sales_types: Object,
  sale: {type: Object, default: null},
  details: {type: Object, default: null},
  errors: Object,
});

const editmode = ref(false);
const form = ref(null);
const loading = ref(false);
const loadingFilter = ref(false);
const snackbar = ref(false);
const snackbarText = ref("");
const snackbarColor = ref("info");
const search_input = ref("");

const client = ref({});
const clientFilter = ref([]);
const products = ref([]);
const detail = ref({});
const detailsForm = ref([]);
const payment = ref({
  status: "unpaid",
  Reglement: "cash",
  amount: 0,
  received_amount: "",
});
const sales = ref([]);
const saleForm = ref({
  id: "",
  date: new Date().toISOString().slice(0, 10),
  statut: "completed",
  notes: "",
  client_id: "",
  warehouse_id: "",
  sales_type_id: "",
  tax_rate: 0,
  TaxNet: 0,
  shipping: 0,
  discount: 0,
});
const saleLabel = ref({
  date: "Fecha",
  statut: "Estado",
  notes: "Notas",
  client_id: "Cliente",
  warehouse_id: "Agencia",
  sales_type_id: "Tipo de Orden",
  discount: "Descuento",
});
const total = ref(0);
const GrandTotal = ref(0);
const units = ref([]);
const product = ref({
  id: "",
  code: "",
  stock: "",
  quantity: 1,
  discount: "",
  DiscountNet: "",
  discount_Method: "",
  name: "",
  sale_unit_id: "",
  fix_stock: "",
  fix_price: "",
  unitSale: "",
  Net_price: "",
  Unit_price: "",
  Total_price: "",
  subtotal: "",
  product_id: "",
  detail_id: "",
  taxe: "",
  tax_percent: "",
  tax_method: "",
  product_variant_id: "",
  is_imei: "",
  imei_number: "",
});

//---------------------- Event Select Payment Status ------------------------------\\

function Selected_PaymentStatus(value) {
  if (value == "paid") {
    let payment_amount = GrandTotal.value.toFixed(2);
    payment.value.amount = helper.formatNumber(payment_amount, 2);
    payment.value.received_amount = helper.formatNumber(payment_amount, 2);
  } else {
    payment.value.amount = 0;
    payment.value.received_amount = 0;
  }
}

//---------- keyup paid Amount

function Verified_paidAmount() {
  snackbar.value = false;
  if (isNaN(payment.value.amount)) {
    payment.value.amount = 0;
  } else if (payment.value.amount > payment.value.received_amount) {
    snackbar.value = true;
    snackbarText.value = "Pago es mayor al monto recibido";
    snackbarColor.value = "warning";
    payment.value.amount = 0;
  } else if (payment.value.amount > GrandTotal.value) {
    snackbar.value = true;
    snackbarText.value = "Pago es mayor al monto total";
    snackbarColor.value = "warning";
    payment.value.amount = 0;
  }
}

//---------- keyup Received Amount

function Verified_Received_Amount() {
  if (isNaN(payment.value.received_amount)) {
    payment.value.received_amount = 0;
  }
}

//--- Submit Validate Create Sale
async function Submit_Sale() {
  snackbar.value = false;
  const validate = await form.value.validate();
  if (!validate.valid) {
    snackbar.value = true;
    snackbarText.value = "llene el formulario correctamente";
    snackbarColor.value = "error";
  } else {
    if (!editmode.value) {
      Create_Sale();
    } else {
      Update_Sale();
    }
  }
}

//---------------------- get_units ------------------------------\\
function get_units(value) {
  axios
      .get("/get_units?id=" + value)
      .then(({data}) => (units.value = data));
}

//------ Show Modal Update Detail Product
function Modal_Updat_Detail(item) {
  detail.value = {};
  get_units(item.product_id);
  detail.value.detail_id = item.detail_id;
  detail.value.sale_unit_id = item.sale_unit_id;
  detail.value.name = item.name;
  detail.value.Unit_price = item.Unit_price;
  detail.value.fix_price = item.fix_price;
  detail.value.fix_stock = item.fix_stock;
  detail.value.stock = item.stock;
  detail.value.tax_method = item.tax_method;
  detail.value.discount_Method = item.discount_Method;
  detail.value.discount = item.discount;
  detail.value.quantity = item.quantity;
  detail.value.tax_percent = item.tax_percent;
}

//------ Submit Update Detail Product

function Update_Detail() {
  loading.value = true;
  for (let i = 0; i < details.value.length; i++) {
    if (details.value[i].detail_id === detail.value.detail_id) {
      // this.convert_unit();
      for (let k = 0; k < units.value.length; k++) {
        if (units.value[k].id == detail.value.sale_unit_id) {
          if (units.value[k].operator == "/") {
            details.value[i].stock =
                detail.value.fix_stock *
                units.value[k].operator_value;
            details.value[i].unitSale = units.value[k].ShortName;
          } else {
            details.value[i].stock =
                detail.value.fix_stock /
                units.value[k].operator_value;
            details.value[i].unitSale = units.value[k].ShortName;
          }
        }
      }

      if (details.value[i].stock < details.value[i].quantity) {
        details.value[i].quantity = details.value[i].stock;
      } else {
        details.value[i].quantity = 1;
      }

      details.value[i].Unit_price = detail.value.Unit_price;
      details.value[i].tax_percent = detail.value.tax_percent;
      details.value[i].tax_method = detail.value.tax_method;
      details.value[i].discount_Method = detail.value.discount_Method;
      details.value[i].discount = detail.value.discount;
      details.value[i].sale_unit_id = detail.value.sale_unit_id;
      details.value[i].imei_number = detail.value.imei_number;

      if (details.value[i].discount_Method == "2") {
        //Fixed
        details.value[i].DiscountNet = details.value[i].discount;
      } else {
        //Percentage %
        details.value[i].DiscountNet = parseFloat(
            (details.value[i].Unit_price * details.value[i].discount) /
            100
        );
      }

      if (details.value[i].tax_method == "1") {
        //Exclusive
        details.value[i].Net_price = parseFloat(
            details.value[i].Unit_price - details.value[i].DiscountNet
        );

        details.value[i].taxe = parseFloat(
            (details.value[i].tax_percent *
                (details.value[i].Unit_price -
                    details.value[i].DiscountNet)) /
            100
        );
      } else {
        //Inclusive
        details.value[i].Net_price = parseFloat(
            (details.value[i].Unit_price -
                details.value[i].DiscountNet) /
            (details.value[i].tax_percent / 100 + 1)
        );

        details.value[i].taxe = parseFloat(
            details.value[i].Unit_price -
            details.value[i].Net_price -
            details.value[i].DiscountNet
        );
      }
    }
  }
  Calcul_Total();

  //form_Update_Detail
}

//------------------------- get Result Value Search Product

function getResultValue(val) {
  search_input.value = val;
  loadingFilter.value = true;

  if (
      saleForm.value.warehouse_id !== "" &&
      saleForm.value.warehouse_id != null
  ) {
    let product_filter = [];
    for (let item of products.value) {
      if (search_input.value === item.id) {
        product_filter = item;
        break;
      }
    }
    if (Object.keys(product_filter).length > 0)
      SearchProduct(product_filter);
  }
  loadingFilter.value = false;
}

//------------------------- Submit Search Product//

function SearchProduct(result) {
  snackbar.value = false;
  product.value = {};
  if (
      detailsForm.value.length > 0 &&
      detailsForm.value.some((detail) => detail.code === result.code)
  ) {
    snackbar.value = true;
    snackbarText.value = "Ya esta añadido";
    snackbarColor.value = "warning";
  } else {
    product.value.code = result.code;
    product.value.stock = result.qte_sale;
    product.value.fix_stock = result.qty;
    if (result.qte_sale < 1) {
      product.value.quantity = result.qte_sale;
    } else {
      product.value.quantity = 1;
    }
    product.value.product_variant_id = result.product_variant_id;
    Get_Product_Details(result.id);
  }

  search_input.value = "";
}

//---------------------- Event Select Warehouse ------------------------------\\
function Selected_Warehouse(value) {
  search_input.value = "";
  Get_Products_By_Warehouse(value);
}

//------------------------------------ Get Products By Warehouse -------------------------\\

function Get_Products_By_Warehouse(id) {
  products.value = [];
  axios
      .get("/get_Products_by_warehouse/" + id + "?stock=" + 0)
      .then((response) => {
        products.value = response.data;
      })
      .catch((error) => {
      });
}

//----------------------------------------- Add Product to order list -------------------------\\
function add_product() {
  if (detailsForm.value.length > 0) {
    Last_Detail_id();
  } else if (detailsForm.value.length === 0) {
    product.value.detail_id = 1;
  }

  detailsForm.value.push(product.value);
}

//-----------------------------------Verified QTY ------------------------------\\
function Verified_Qty(detail, id) {
  for (let i = 0; i < detailsForm.value.length; i++) {
    if (detailsForm.value[i].detail_id === id) {
      if (isNaN(detail.quantity)) {
        detailsForm.value[i].quantity = detail.stock;
      }

      if (detail.quantity > detail.stock) {
        snackbar.value = true;
        snackbarText.value = "bajo stock";
        snackbarColor.value = "warning";
        detailsForm.value[i].quantity = detail.stock;
      } else {
        detailsForm.value[i].quantity = detail.quantity;
      }
    }
  }
  Calcul_Total();
}

//-----------------------------------increment QTY ------------------------------\\

function increment(detail, id) {
  for (let i = 0; i < detailsForm.value.length; i++) {
    if (detailsForm.value[i].detail_id == id) {
      if (detail.quantity + 1 > detail.stock) {
        snackbar.value = true;
        snackbarText.value = "bajo stock";
        snackbarColor.value = "warning";
      } else {
        helper.formatNumber(detailsForm.value[i].quantity++, 2);
      }
    }
  }
  Calcul_Total();
}

//-----------------------------------decrement QTY ------------------------------\\

function decrement(detail, id) {
  for (let i = 0; i < detailsForm.value.length; i++) {
    if (detailsForm.value[i].detail_id == id) {
      if (detail.quantity - 1 > 0) {
        if (detail.quantity - 1 > detail.stock) {
          snackbar.value = true;
          snackbarText.value = "bajo stock";
          snackbarColor.value = "warning";
        } else {
          helper.formatNumber(detailsForm.value[i].quantity--, 2);
        }
      }
    }
  }
  Calcul_Total();
}

//-----------------------------------------Calcul Total ------------------------------\\
function Calcul_Total() {
  total.value = 0;
  for (let i = 0; i < detailsForm.value.length; i++) {
    detailsForm.value[i].subtotal = parseFloat(
        detailsForm.value[i].quantity * detailsForm.value[i].Net_price
    );
    total.value = parseFloat(total.value + detailsForm.value[i].subtotal);
  }
  const total_without_discount = parseFloat(
      total.value - saleForm.value.discount
  );

  GrandTotal.value = parseFloat(total_without_discount);

  let grand_total = GrandTotal.value.toFixed(2);
  GrandTotal.value = parseFloat(grand_total);

  // if (payment.value.status == "paid") {
  // payment.value.received_amount = helper.formatNumber(GrandTotal.value, 2);
  payment.value.amount = helper.formatNumber(GrandTotal.value, 2);
  // }
}

// -----------------------------------Delete Detail Product ------------------------------\\
function delete_Product_Detail(id) {
  for (let i = 0; i < detailsForm.value.length; i++) {
    if (id === detailsForm.value[i].detail_id) {
      detailsForm.value.splice(i, 1);
      Calcul_Total();
    }
  }
}

//-----------------------------------verified Order List ------------------------------\\
function verifiedForm() {
  if (detailsForm.value.length <= 0) {
    snackbar.value = true;
    snackbarText.value = "debes añadir un producto";
    snackbarColor.value = "warning";
    return false;
  } else {
    let count = 0;
    for (let i = 0; i < detailsForm.value.length; i++) {
      if (
          detailsForm.value[i].quantity == "" ||
          detailsForm.value[i].quantity === 0
      ) {
        count += 1;
      }
    }

    if (count > 0) {
      snackbar.value = true;
      snackbarText.value = "Debes añadir cantidades";
      snackbarColor.value = "warning";
      return false;
    } else {
      return true;
    }
  }
}

//--------------------------------- Create Sale -------------------------\\
function Create_Sale() {
  if (verifiedForm()) {
    loading.value = true;
    snackbar.value = false;
    axios
        .post("/sales", {
          sales_type: saleForm.value.sales_type_id,
          date: saleForm.value.date,
          client_id: saleForm.value.client_id,
          warehouse_id: saleForm.value.warehouse_id,
          statut: saleForm.value.statut,
          notes: saleForm.value.notes,
          tax_rate: saleForm.value.tax_rate ? saleForm.value.tax_rate : 0,
          TaxNet: saleForm.value.TaxNet ? saleForm.value.TaxNet : 0,
          discount: saleForm.value.discount ? saleForm.value.discount : 0,
          shipping: saleForm.value.shipping ? saleForm.value.shipping : 0,
          GrandTotal: GrandTotal.value,
          details: detailsForm.value,
          payment: payment.value,
          amount: parseFloat(payment.value.amount).toFixed(2),
          received_amount: parseFloat(
              payment.value.received_amount
          ).toFixed(2),
          change: parseFloat(
              payment.value.received_amount - payment.value.amount
          ).toFixed(2),
        })
        .then((response) => {
          snackbar.value = true;
          snackbarText.value = "compra exitosa";
          snackbarColor.value = "success";
          router.visit("/sales");
        })
        .catch((error) => {
          console.log(error);
          snackbar.value = true;
          snackbarText.value = "No se pudo procesar el pago";
          snackbarColor.value = "error";
        })
        .finally(() => {
          loading.value = false;
        });
  }
}

//--------------------------------- Update Sale -------------------------\\
function Update_Sale() {
  if (verifiedForm()) {
    loading.value = true;
    snackbar.value = false;
    let id = props.sale.id;
    axios
        .put(`/sales/${id}`, {
          sales_type: saleForm.value.sales_type_id,
          date: saleForm.value.date,
          client_id: saleForm.value.client_id,
          GrandTotal: GrandTotal.value,
          warehouse_id: saleForm.value.warehouse_id,
          statut: saleForm.value.statut,
          notes: saleForm.value.notes,
          tax_rate: saleForm.value.tax_rate ? saleForm.value.tax_rate : 0,
          TaxNet: saleForm.value.TaxNet ? saleForm.value.TaxNet : 0,
          discount: saleForm.value.discount ? saleForm.value.discount : 0,
          shipping: saleForm.value.shipping ? saleForm.value.shipping : 0,
          details: detailsForm.value,
        })
        .then((response) => {
          snackbar.value = true;
          snackbarText.value = "compra exitosa";
          snackbarColor.value = "success";
          router.visit("/sales", {
            onStart: () => {
              loading.value = true;
            },
            onFinish: () => {
              loading.value = false;
            },
          });
        })
        .catch((error) => {
          console.log(error);
          snackbar.value = true;
          snackbarText.value = "No se pudo procesar el pago";
          snackbarColor.value = "error";
        })
        .finally(() => {
          loading.value = false;
        });
  }
}

//-------------------------------- Get Last Detail Id -------------------------\\
function Last_Detail_id() {
  product.value.detail_id = 0;
  let len = detailsForm.value.length;
  product.value.detail_id = detailsForm.value[len - 1].detail_id + 1;
}

//---------------------------------Get Product Details ------------------------\\

function Get_Product_Details(product_id) {
  axios.get("/product/" + product_id).then((response) => {
    product.value.discount = 0;
    product.value.DiscountNet = 0;
    product.value.discount_Method = "2";
    product.value.product_id = response.data.id;
    product.value.name = response.data.name;
    product.value.Net_price = response.data.Net_price;
    product.value.Unit_price = response.data.Unit_price;
    product.value.tax_method = response.data.tax_method;
    product.value.tax_percent = response.data.tax_percent;
    product.value.unitSale = response.data.unitSale;
    product.value.fix_price = response.data.fix_price;
    product.value.sale_unit_id = response.data.sale_unit_id;
    product.value.imei_number = "";
    add_product();
    Calcul_Total();
  });
}

//---------- keyup Discount

function keyup_Discount() {
  if (isNaN(saleForm.value.discount)) {
    saleForm.value.discount = 0;
  } else if (saleForm.value.discount == "") {
    saleForm.value.discount = 0;
    Calcul_Total();
  } else {
    Calcul_Total();
  }
}

function change_payment_status() {
  if (
      parseFloat(payment.value.amount, 2) >=
      parseFloat(GrandTotal.value, 2)
  ) {
    payment.value.status = "paid";
  } else if (
      parseFloat(payment.value.amount, 2) <= 0 ||
      payment.value.amount == ""
  ) {
    payment.value.status = "unpaid";
  } else {
    payment.value.status = "partial";
  }
}

//---------- filter clients
function querySelections(v) {
  clientFilter.value = props.clients.filter((e) => {
    return (
        (e.title || "").toLowerCase().indexOf((v || "").toLowerCase()) > -1
    );
  });
}

function resetForm() {
  detailsForm.value = [];
  products.value = [];
  payment.value = {
    status: "unpaid",
    Reglement: "cash",
    amount: 0,
    received_amount: "",
  };
  saleForm.value = {
    id: "",
    date: new Date().toISOString().slice(0, 10),
    statut: "completed",
    notes: "",
    client_id: "",
    warehouse_id: "",
    sales_type_id: "",
    tax_rate: 0,
    TaxNet: 0,
    shipping: 0,
    discount: 0,
  };
}

watch(
    () => [props.sale],
    () => {
      if (props.sale != null) {
        editmode.value = true;
      } else {
        resetForm();
        editmode.value = false;
      }
    }
);

onMounted(() => {
  if (props.sale != null) {
    editmode.value = true;
    saleForm.value = props.sale;
    detailsForm.value = props.details;
    clientFilter.value = props.clients.filter(
        (val) => val.value === props.sale.client_id
    );
    Get_Products_By_Warehouse(props.sale.warehouse_id);
    Calcul_Total();
  } else if (props.warehouses.length == 1) {
    saleForm.value.warehouse_id = props.warehouses[0].value;
    Get_Products_By_Warehouse(saleForm.value.warehouse_id);
  }
});
</script>
<template>
  <Layout :loading="loading">
    <snackbar
        v-model="snackbar"
        :snackbarColor="snackbarColor"
        :snackbarText="snackbarText"
    >
    </snackbar>
    <v-form ref="form">
      <v-card>
        <v-toolbar height="10"></v-toolbar>
        <v-card-text>
          <v-row>
            <!-- date-->
            <v-col lg="4" md="4" cols="12">
              <v-text-field
                  v-model="saleForm.date"
                  :label="saleLabel.date"
                  variant="outlined"
                  density="comfortable"
                  hide-details="auto"
                  type="date"
                  :rules="helper.required"
              >
              </v-text-field>
            </v-col>
            <!-- Customer -->
            <v-col lg="4" md="4" cols="12">
              <v-autocomplete
                  v-model="saleForm.client_id"
                  @update:search="querySelections"
                  :items="clientFilter"
                  :label="saleLabel.client_id"
                  variant="outlined"
                  density="comfortable"
                  hide-details="auto"
                  clearable
                  :rules="helper.required"
              ></v-autocomplete>
            </v-col>

            <!-- warehouse -->
            <v-col lg="4" md="4" cols="12">
              <v-select
                  @update:modelValue="Selected_Warehouse"
                  v-model="saleForm.warehouse_id"
                  :items="warehouses"
                  :label="saleLabel.warehouse_id"
                  variant="outlined"
                  density="comfortable"
                  hide-details="auto"
                  clearable
                  :rules="helper.required"
              ></v-select>
            </v-col>

            <!-- Product -->
            <v-col cols="12">
              <v-autocomplete
                  @update:modelValue="getResultValue"
                  :loading="loadingFilter"
                  :items="products"
                  :model-value="search_input"
                  variant="solo-filled"
                  item-title="name"
                  item-value="id"
                  density="comfortable"
                  hide-no-data
                  hide-details
                  label="Añadir Producto"
                  :disabled="products.length == 0"
                  clearable
                  prepend-inner-icon="mdi-magnify"
              ></v-autocomplete>
            </v-col>

            <!-- products  -->
            <v-col cols="12">
              <p class="text-h6">Productos *</p>
              <v-table
                  hover
                  class="border rounded"
                  density="comfortable"
              >
                <thead>
                <tr class="bg-secondary">
                  <th
                      class="text-white text-center"
                  >
                    #
                  </th>
                  <th
                      class="text-white text-center"
                  >
                    Producto
                  </th>
                  <th
                      class="text-white text-center"
                  >
                    Precio x Unidad
                  </th>
                  <th
                      class="text-white text-center"
                  >
                    En Stock
                  </th>
                  <th
                      class="text-white text-center"
                  >
                    Cantidad
                  </th>
                  <th
                      class="text-white text-center"
                  >
                    Descuento
                  </th>
                  <th
                      class="text-white text-center"
                  >
                    Sub Total
                  </th>
                  <th
                      class="text-white text-center"
                  ></th>
                </tr>
                </thead>
                <tbody>
                <tr v-if="detailsForm.length <= 0">
                  <td colspan="8">
                    No hay datos disponibles
                  </td>
                </tr>
                <tr v-for="detail in detailsForm">
                  <td>
                    {{ detail.detail_id }}
                  </td>
                  <td>
                    <v-chip
                        color="primary"
                        size="small"
                    >{{ detail.code }}
                    </v-chip>

                    {{ detail.name }}
                  </td>
                  <td>
                    <v-text-field
                        variant="outlined"
                        density="compact"
                        hide-details="auto"
                        :rules="helper.number"
                        v-model="detail.Net_price"
                        @keyup="Calcul_Total"
                    >
                      <template
                          v-slot:prepend
                      >
                        Bs
                      </template>
                    </v-text-field>
                  </td>
                  <td>
                    <v-chip
                        color="default"
                        size="small"
                    >{{ detail.stock }}
                      {{ detail.unitSale }}
                    </v-chip>
                  </td>
                  <td>
                    <div class="quantity">
                      <v-text-field
                          variant="outlined"
                          density="compact"
                          hide-details="auto"
                          :rules="helper.number"
                          v-model="detail.quantity"
                          @keyup="Verified_Qty(detail,detail.detail_id)"
                          :min="0.0"
                          :max="detail.current"
                      >
                        <template v-slot:append>
                          <v-icon
                              color="secundary"
                              @click="increment(detail,detail.detail_id)"
                          >
                            mdi-plus-box
                          </v-icon>
                        </template>
                        <template v-slot:prepend>
                          <v-icon
                              color="secundary"
                              @click="decrement(detail, detail.detail_id ) "
                          >
                            mdi-minus-box
                          </v-icon>
                        </template>
                      </v-text-field>
                    </div>
                  </td>
                  <td>
                    Bs
                    {{
                      helper.formatNumber(
                          detail.DiscountNet *
                          detail.quantity,
                          2
                      )
                    }}
                  </td>
                  <td>
                    Bs
                    {{
                      detail.subtotal.toFixed(2)
                    }}
                  </td>
                  <td>
                    <i @click="Modal_Updat_Detail(detail)"
                        class="i-Edit text-25 text-success"
                    ></i>
                    <v-btn
                        class="ma-1 rounded"
                        color="success"
                        icon="mdi-pen"
                        size="small"
                        density="comfortable"
                        variant="elevated"
                        @click="Modal_Updat_Detail(detail)"
                    >
                    </v-btn>
                    <v-btn
                        class="ma-1 rounded"
                        color="error"
                        icon="mdi-delete"
                        size="small"
                        density="comfortable"
                        variant="elevated"
                        @click="delete_Product_Detail(detail.detail_id)"
                    >
                    </v-btn>
                  </td>
                </tr>
                </tbody>
              </v-table>
            </v-col>
            <v-col cols="12" md="4" offset-md="8" sm="6" offset-sm="6">
              <v-table density="compact" hover class="border">
                <tbody>
<!--                <tr>-->
<!--                  <td class="bold">{{$t('OrderTax')}}</td>-->
<!--                  <td>-->
<!--                    <span>{{currentUser.currency}} {{sale.TaxNet.toFixed(2)}} ({{formatNumber(sale.tax_rate ,2)}} %)</span>-->
<!--                  </td>-->
<!--                </tr>-->
<!--                <tr>-->
<!--                  <td class="bold">{{$t('Discount')}}</td>-->
<!--                  <td>{{currentUser.currency}} {{sale.discount.toFixed(2)}}</td>-->
<!--                </tr>-->
<!--                <tr>-->
<!--                  <td class="bold">{{$t('Shipping')}}</td>-->
<!--                  <td>{{currentUser.currency}} {{sale.shipping.toFixed(2)}}</td>-->
<!--                </tr>-->
                <tr>
                  <td class="font-weight-bold">
                    Total
                  </td>
                  <td class="font-weight-bold">
                          Bs {{GrandTotal.toFixed(2)}}
                  </td>
                </tr>
                </tbody>
              </v-table>
            </v-col>
          </v-row>
          <v-row>
            <v-col lg="4" md="4" cols="12">
              <!-- Discount -->
              <v-text-field
                  :label="saleLabel.discount"
                  variant="outlined"
                  density="compact"
                  hide-details="auto"
                  :rules="helper.number"
                  v-model="saleForm.discount"
                  @keyup="keyup_Discount()"
              ></v-text-field>
            </v-col>
            <!-- Amount  -->
            <v-col lg="4" md="4" cols="12" v-if="editmode==false">
              <v-text-field
                  v-model="payment.amount"
                  variant="outlined"
                  density="compact"
                  hide-details="auto"
                  type="text"
                  label="A pagar"
                  :rules="helper.numberWithDecimal"
              ></v-text-field>
            </v-col>
            <!-- Received  Amount  -->
            <v-col lg="4" md="4" cols="12" v-if="editmode==false">
              <v-text-field
                  v-model="payment.received_amount"
                  variant="outlined"
                  density="compact"
                  hide-details="auto"
                  type="text"
                  label="Monto Recibido"
                  readonly
                  :rules="helper.numberWithDecimal"
                  @keyup="change_payment_status()"
              ></v-text-field>
            </v-col>

            <!-- Status  -->
            <v-col lg="4" md="4" cols="12">
              <v-select
                  v-model="saleForm.statut"
                  variant="outlined"
                  density="compact"
                  clearable
                  hide-details="auto"
                  :items="helper.statutSale()"
                  :label="saleLabel.statut"
              ></v-select>
            </v-col>

            <!-- Sales type  -->
            <v-col lg="4" md="4" cols="12">
              <v-select
                  v-model="saleForm.sales_type_id"
                  variant="outlined"
                  density="compact"
                  hide-details="auto"
                  :items="sales_types"
                  :label="saleLabel.sales_type_id + ' *'"
                  :rules="helper.required"
              ></v-select>
            </v-col>

            <!-- Payment choice -->
            <v-col lg="4" md="4" cols="12">
              <v-select
                  v-model="payment.Reglement"
                  variant="outlined"
                  density="compact"
                  hide-details="auto"
                  :items="helper.reglamentPayment()"
                  label="Tipo de Pago"
              ></v-select>
            </v-col>

            <!--                &lt;!&ndash; change  Amount  &ndash;&gt;-->
            <!--                <v-col md="4" v-if="payment.status != 'pending'">-->
            <!--                  <label>{{$t('Change')}} :</label>-->
            <!--                  <p-->
            <!--                    class="change_amount"-->
            <!--                  >{{parseFloat(payment.received_amount - payment.amount).toFixed(2)}}</p>-->
            <!--                </v-col>-->

            <v-col md="12" class="mt-3">
              <v-textarea
                  v-model="saleForm.notes"
                  rows="4"
                  variant="outlined"
                  label="Notas"
                  placeholder="Notas"
                  hide-details="auto"
              ></v-textarea>
            </v-col>

            <v-col cols="12">
              <v-btn
                  variant="flat"
                  color="primary"
                  :disabled="loading"
                  @click="Submit_Sale"
              >Guardar
              </v-btn
              >
            </v-col>
          </v-row>
        </v-card-text>
      </v-card>
    </v-form>

    <!--    &lt;!&ndash; Modal Update detail Product &ndash;&gt;-->
    <!--    <validation-observer ref="Update_Detail">-->
    <!--      <v-modal hide-footer size="lg" id="form_Update_Detail" :title="detail.name">-->
    <!--        <v-form @submit.prevent="submit_Update_Detail">-->
    <!--          <v-row>-->
    <!--            &lt;!&ndash; Unit Price &ndash;&gt;-->
    <!--            <v-col lg="6" md="6" sm="12">-->
    <!--              <validation-provider-->
    <!--                name="Product Price"-->
    <!--                :rules="{ required: true , regex: /^\d*\.?\d*$/}"-->
    <!--                v-slot="validationContext"-->
    <!--              >-->
    <!--                <v-form-group :label="$t('ProductPrice') + ' ' + '*'" id="Price-input">-->
    <!--                  <v-form-input-->
    <!--                    label="Product Price"-->
    <!--                    v-model="detail.Unit_price"-->
    <!--                    :state="getValidationState(validationContext)"-->
    <!--                    aria-describedby="Price-feedback"-->
    <!--                  ></v-form-input>-->
    <!--                  <v-form-invalid-feedback id="Price-feedback">{{ validationContext.errors[0] }}</v-form-invalid-feedback>-->
    <!--                </v-form-group>-->
    <!--              </validation-provider>-->
    <!--            </v-col>-->

    <!--            &lt;!&ndash; Tax Method &ndash;&gt;-->
    <!--            <v-col lg="6" md="6" sm="12">-->
    <!--              <validation-provider name="Tax Method" :rules="{ required: true}">-->
    <!--                <v-form-group slot-scope="{ valid, errors }" :label="$t('TaxMethod') + ' ' + '*'">-->
    <!--                  <v-select-->
    <!--                    :class="{'is-invalid': !!errors.length}"-->
    <!--                    :state="errors[0] ? false : (valid ? true : null)"-->
    <!--                    v-model="detail.tax_method"-->
    <!--                    :reduce="label => label.value"-->
    <!--                    :placeholder="$t('Choose_Method')"-->
    <!--                    :options="-->
    <!--                           [-->
    <!--                            {label: 'Exclusive', value: '1'},-->
    <!--                            {label: 'Inclusive', value: '2'}-->
    <!--                           ]"-->
    <!--                  ></v-select>-->
    <!--                  <v-form-invalid-feedback>{{ errors[0] }}</v-form-invalid-feedback>-->
    <!--                </v-form-group>-->
    <!--              </validation-provider>-->
    <!--            </v-col>-->

    <!--            &lt;!&ndash; Tax Rate &ndash;&gt;-->
    <!--            <v-col lg="6" md="6" sm="12">-->
    <!--              <validation-provider-->
    <!--                name="Order Tax"-->
    <!--                :rules="{ required: true , regex: /^\d*\.?\d*$/}"-->
    <!--                v-slot="validationContext"-->
    <!--              >-->
    <!--                <v-form-group :label="$t('OrderTax') + ' ' + '*'">-->
    <!--                  <v-input-group append="%">-->
    <!--                    <v-form-input-->
    <!--                      label="Order Tax"-->
    <!--                      v-model="detail.tax_percent"-->
    <!--                      :state="getValidationState(validationContext)"-->
    <!--                      aria-describedby="OrderTax-feedback"-->
    <!--                    ></v-form-input>-->
    <!--                  </v-input-group>-->
    <!--                  <v-form-invalid-feedback id="OrderTax-feedback">{{ validationContext.errors[0] }}</v-form-invalid-feedback>-->
    <!--                </v-form-group>-->
    <!--              </validation-provider>-->
    <!--            </v-col>-->

    <!--            &lt;!&ndash; Discount Method &ndash;&gt;-->
    <!--             <v-col lg="6" md="6" sm="12">-->
    <!--              <validation-provider name="Discount Method" :rules="{ required: true}">-->
    <!--                <v-form-group slot-scope="{ valid, errors }" :label="$t('Discount_Method') + ' ' + '*'">-->
    <!--                  <v-select-->
    <!--                    v-model="detail.discount_Method"-->
    <!--                    :reduce="label => label.value"-->
    <!--                    :placeholder="$t('Choose_Method')"-->
    <!--                    :class="{'is-invalid': !!errors.length}"-->
    <!--                    :state="errors[0] ? false : (valid ? true : null)"-->
    <!--                    :options="-->
    <!--                           [-->
    <!--                            {label: 'Percent %', value: '1'},-->
    <!--                            {label: 'Fixed', value: '2'}-->
    <!--                           ]"-->
    <!--                  ></v-select>-->
    <!--                  <v-form-invalid-feedback>{{ errors[0] }}</v-form-invalid-feedback>-->
    <!--                </v-form-group>-->
    <!--              </validation-provider>-->
    <!--            </v-col>-->

    <!--            &lt;!&ndash; Discount Rate &ndash;&gt;-->
    <!--           <v-col lg="6" md="6" sm="12">-->
    <!--              <validation-provider-->
    <!--                name="Discount Rate"-->
    <!--                :rules="{ required: true , regex: /^\d*\.?\d*$/}"-->
    <!--                v-slot="validationContext"-->
    <!--              >-->
    <!--                <v-form-group :label="$t('Discount') + ' ' + '*'">-->
    <!--                  <v-form-input-->
    <!--                    label="Discount"-->
    <!--                    v-model.number="detail.discount"-->
    <!--                    :state="getValidationState(validationContext)"-->
    <!--                    aria-describedby="Discount-feedback"-->
    <!--                  ></v-form-input>-->
    <!--                  <v-form-invalid-feedback id="Discount-feedback">{{ validationContext.errors[0] }}</v-form-invalid-feedback>-->
    <!--                </v-form-group>-->
    <!--              </validation-provider>-->
    <!--            </v-col>-->

    <!--            &lt;!&ndash; Unit Sale &ndash;&gt;-->
    <!--            <v-col lg="6" md="6" sm="12">-->
    <!--              <validation-provider name="Unit Sale" :rules="{ required: true}">-->
    <!--                <v-form-group slot-scope="{ valid, errors }" :label="$t('UnitSale') + ' ' + '*'">-->
    <!--                  <v-select-->
    <!--                    :class="{'is-invalid': !!errors.length}"-->
    <!--                    :state="errors[0] ? false : (valid ? true : null)"-->
    <!--                    v-model="detail.sale_unit_id"-->
    <!--                    :placeholder="$t('Choose_Unit_Sale')"-->
    <!--                    :reduce="label => label.value"-->
    <!--                    :options="units.map(units => ({label: units.name, value: units.id}))"-->
    <!--                  />-->
    <!--                  <v-form-invalid-feedback>{{ errors[0] }}</v-form-invalid-feedback>-->
    <!--                </v-form-group>-->
    <!--              </validation-provider>-->
    <!--            </v-col>-->

    <!--             &lt;!&ndash; Imei or serial numbers &ndash;&gt;-->
    <!--              <v-col lg="12" md="12" sm="12" v-show="detail.is_imei">-->
    <!--                <v-form-group :label="$t('Add_product_IMEI_Serial_number')">-->
    <!--                  <v-form-input-->
    <!--                    label="Add_product_IMEI_Serial_number"-->
    <!--                    v-model="detail.imei_number"-->
    <!--                    :placeholder="$t('Add_product_IMEI_Serial_number')"-->
    <!--                  ></v-form-input>-->
    <!--                </v-form-group>-->
    <!--            </v-col>-->

    <!--            <v-col md="12">-->
    <!--              <v-form-group>-->
    <!--                <v-button-->
    <!--                  variant="primary"-->
    <!--                  type="submit"-->
    <!--                  :disabled="Submit_Processing_detail"-->
    <!--                >{{$t('submit')}}</v-button>-->
    <!--                <div v-once class="typo__p" v-if="Submit_Processing_detail">-->
    <!--                  <div class="spinner sm spinner-primary mt-3"></div>-->
    <!--                </div>-->
    <!--              </v-form-group>-->
    <!--            </v-col>-->
    <!--          </v-row>-->
    <!--        </v-form>-->
    <!--      </v-modal>-->
    <!--    </validation-observer>-->
  </Layout>
</template>
