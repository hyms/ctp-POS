<script setup>
import {computed, ref} from "vue";
import Layout from "@/Layouts/Pos.vue";
import {router, usePage} from "@inertiajs/vue3";
import helper from "@/helpers";
import labels from "@/labels";
import Snackbar from "@/Components/snackbar.vue";
import MenuUser from "@/Components/Menu_user.vue";
import Full_screen from "@/Components/full_screen.vue";
import InvoiceDialog from "@/Components/InvoiceDialog.vue";

const currency = computed(() => usePage().props.currency);

const props = defineProps({
  defaultWarehouse: Object,
  defaultClient: Object,
  clients: Object,
  sales_types: Object,
  warehouses: Object,
  categories: Object,
  error: Object,
})

const form = ref(null);
const formAddPayment = ref(null);
const formCreateCustomer = ref(null);
const formUpdateDetail = ref(null);
const loading = ref(false);
const snackbar = ref(false);
const snackbarText = ref("");
const snackbarColor = ref("info");
const dialogCustomer = ref(false);
const dialogUpdateDetail = ref(false);
const dialogAddPayment = ref(false);
const dialogInvoice = ref(false);
const payment = ref({
  amount: "",
  received_amount: "",
  Reglement: "",
  notes: ""
});
const focused = ref(false);
const timer = ref(null);
const searchProducts = ref('');
const search_input = ref('');
const product_filter = ref([]);
const clientFilter = ref([]);
const GrandTotal = ref(0);
const total = ref(0);
const Ref = ref("");
const units = ref([]);
const payments = ref([]);
const products = ref([]);
const products_page = ref(0);
const products_pos = ref([]);
const details = ref([]);
const detail = ref({});
const pos_settings = ref({});
const invoice_pos = ref({
  sale: {
    Ref: "",
    client_name: "",
    discount: "",
    taxe: "",
    date: "",
    tax_rate: "",
    shipping: "",
    GrandTotal: "",
    paid_amount: ""
  },
  details: [],
  setting: {
    logo: "",
    CompanyName: "",
    CompanyAdress: "",
    email: "",
    CompanyPhone: ""
  }
});
const sale = ref({
  warehouse_id: "",
  client_id: "",
  tax_rate: 0,
  shipping: 0,
  discount: 0,
  TaxNet: 0,
  notes: '',
  sales_type_id:"",
});
const client = ref({
  id: "",
  name: "",
  code: "",
  email: "",
  phone: "",
  country: "",
  tax_number: "",
  city: "",
  adresse: ""
});
const category_id = ref("");
const product = ref({
  id: "",
  code: "",
  current: "",
  quantity: "",
  check_qty: "",
  discount: "",
  DiscountNet: "",
  discount_Method: "",
  sale_unit_id: "",
  fix_stock: "",
  fix_price: "",
  name: "",
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
const sound = ref("/audio/Beep.wav");
const audio = ref(new Audio("/audio/Beep.wav"))

//---------- filter clients
function querySelectionClient(v) {
  clientFilter.value = props.clients.filter((e) => {
    return (
        (e.name || "").toLowerCase().indexOf((v || "").toLowerCase()) > -1
    );
  });
}

//--- Submit Validate Create Sale
async function Submit_Pos() {
  snackbar.value = false;
  const validate = await form.value.validate();
  if (!validate.valid) {
    if (sale.value.client_id == "" || sale.value.client_id === null) {
      snackbar.value = true;
      snackbarColor.value = "error";
      snackbarText.value = labels.no_select_client;
    } else if (
        sale.value.warehouse_id == "" ||
        sale.value.warehouse_id === null
    ) {
      snackbar.value = true;
      snackbarColor.value = "error";
      snackbarText.value = labels.no_select_warehouse;
    } else {
      snackbar.value = true;
      snackbarColor.value = "error";
      snackbarText.value = labels.no_fill_data;
    }
  } else {
    if (verifiedForm()) {
      created()
    }
  }
}

//---Submit Validation Update Detail
async function submit_Update_Detail() {
  const validate = await formUpdateDetail.value.validate();
  if (validate.valid) {
    Update_Detail();
  }
}

//------ Validate Form Submit_Payment
async function Submit_Payment() {
  snackbar.value = false;
  const validate = await formAddPayment.value.validate();
  if (validate.valid) {
    if (payment.value.amount > payment.value.received_amount) {
      payment.value.received_amount = 0;
    } else if (payment.value.amount > GrandTotal.value) {
      payment.value.amount = 0;
    } else {
      CreatePOS();
    }
  }
}

//------------- Submit Validation Create & Edit Customer
async function Submit_Customer() {
  snackbar.value = false;
  const validate = await formCreateCustomer.value.validate();
  if (!validate) {
    snackbar.value = true;
    snackbarColor.value = "error";
    snackbarText.value = labels.no_fill_data;
  } else {
    Create_Client();
  }
}

//---------------------------------------- Create new Customer -------------------------------\\
function Create_Client() {
  loading.value = true;
  axios
      .post("/clients", {
        name: client.value.name,
        email: client.value.email,
        phone: client.value.phone,
        tax_number: client.value.tax_number,
        country: client.value.country,
        city: client.value.city,
        adresse: client.value.adresse
      })
      .then(response => {
        snackbar.value = true;
        snackbarColor.value = "success";
        snackbarText.value = labels.success_message;
        Get_Client_Without_Paginate();
        dialogCustomer.value = false;
      })
      .catch(error => {
        snackbar.value = true;
        snackbarColor.value = "error";
        snackbarText.value = labels.error_message;
        console.log(error)
      })
      .finally(() => {
        loading.value = false;
      });
}

//------------------------------ New Model (create Customer) -------------------------------\\
function New_Client() {
  reset_Form_client();
  dialogCustomer.value = true;
}

//-------------------------------- reset Form -------------------------------\\
function reset_Form_client() {
  client.value = {
    id: "",
    name: "",
    email: "",
    phone: "",
    tax_number: "",
    country: "",
    city: "",
    adresse: ""
  };
}

//------------------------------------ Get Clients Without Paginate -------------------------\\
function Get_Client_Without_Paginate() {
  axios
      .get("/get_clients_without_paginate")
      .then(({data}) => (client.values = data));
}

//---Validate State Fields
function getValidationState({dirty, validated, valid = null}) {
  return dirty || validated ? valid : null;
}

//---------------------- Event Select Warehouse ------------------------------\\
function Selected_Warehouse(value) {
  search_input.value = '';
  product_filter.value = [];
  Get_Products_By_Warehouse(sale.value.warehouse_id);
  getProducts(1);
}

//------------------------------------ Get Products By Warehouse -------------------------\\
function Get_Products_By_Warehouse(id) {
  axios
      .get("/get_Products_by_warehouse/" + id + "?stock=" + 1 + "&is_sale=" + 1)
      .then(response => {
        products_pos.value = response.data;
      })
      .catch(error => {
        console.log(error)
      });
}

//----------------------------------------- Add Detail of Sale -------------------------\\
function add_product(code) {
  // audio.value.play();
  if (details.value.some(detail => detail.code === code)) {
    increment_qty_scanner(code);
  } else {
    if (details.value.length > 0) {
      order_detail_id();
    } else if (details.value.length === 0) {
      product.value.detail_id = 1;
    }
    details.value.push(product.value);
  }
}

//-------------------------------- order detail id -------------------------\\
function order_detail_id() {
  product.value.detail_id = 0;
  let len = details.value.length;
  product.value.detail_id = details.value[len - 1].detail_id + 1;
}

//---------------------- get_units ------------------------------\\
function get_units(value) {
  axios
      .get("/get_units?id=" + value)
      .then(({data}) => (units.value = data));
}

//------ Show Modal Update Detail Product
function Modal_Updat_Detail(detail) {
  detail.value = {};
  get_units(detail.product_id);
  detail.value.detail_id = detail.detail_id;
  detail.value.sale_unit_id = detail.sale_unit_id;
  detail.value.name = detail.name;
  detail.value.Unit_price = detail.Unit_price;
  detail.value.fix_price = detail.fix_price;
  detail.value.fix_stock = detail.fix_stock;
  detail.value.current = detail.current;
  detail.value.tax_method = detail.tax_method;
  detail.value.discount_Method = detail.discount_Method;
  detail.value.discount = detail.discount;
  detail.value.quantity = detail.quantity;
  detail.value.tax_percent = detail.tax_percent;
  detail.value.is_imei = detail.is_imei;
  detail.value.imei_number = detail.imei_number;
  dialogUpdateDetail.value = true;
}

//------ Submit Update Detail Product
function Update_Detail() {
  for (let i = 0; i < details.value.length; i++) {
    if (details.value[i].detail_id === detail.value.detail_id) {
      // this.convert_unit();
      for (let k = 0; k < units.value.length; k++) {
        if (units.value[k].id == detail.value.sale_unit_id) {
          if (units.value[k].operator == "/") {
            details.value[i].current =
                detail.value.fix_stock * units.value[k].operator_value;
            details.value[i].unitSale = units.value[k].ShortName;
          } else {
            details.value[i].current =
                detail.value.fix_stock / units.value[k].operator_value;
            details.value[i].unitSale = units.value[k].ShortName;
          }
        }
      }
      if (details.value[i].current < details.value[i].quantity) {
        details.value[i].quantity = details.value[i].current;
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
            (details.value[i].Unit_price * details.value[i].discount) / 100
        );
      }
      if (details.value[i].tax_method == "1") {
        //Exclusive
        details.value[i].Net_price = parseFloat(
            details.value[i].Unit_price - details.value[i].DiscountNet
        );
        details.value[i].taxe = parseFloat(
            (details.value[i].tax_percent *
                (details.value[i].Unit_price - details.value[i].DiscountNet)) /
            100
        );
        details.value[i].Total_price = parseFloat(
            details.value[i].Net_price + details.value[i].taxe
        );
      } else {
        //Inclusive
        details.value[i].Net_price = parseFloat(
            (details.value[i].Unit_price - details.value[i].DiscountNet) /
            (details.value[i].tax_percent / 100 + 1)
        );
        details.value[i].taxe = parseFloat(
            details.value[i].Unit_price -
            details.value[i].Net_price -
            details.value[i].DiscountNet
        );
        details.value[i].Total_price = parseFloat(
            details.value[i].Net_price + details.value[i].taxe
        );
      }
    }
  }
  CalculTotal();
  dialogUpdateDetail.value = false;
}

//-- check Qty of  details order if Null or zero
function verifiedForm() {
  snackbar.value = false;
  if (details.value.length <= 0) {
    snackbar.value = true;
    snackbarText.value = labels.no_add_product;
    snackbarColor.value = "warning";
    return false;
  } else {
    let count = 0;
    for (let i = 0; i < details.value.length; i++) {
      if (
          details.value[i].quantity == "" ||
          (details.value[i].quantity * 1) === 0
      ) {
        count += 1;
      }
    }
    if (count > 0) {
      snackbar.value = true;
      snackbarText.value = labels.no_add_qty;
      snackbarColor.value = "warning";
      return false;
    } else {
      return true;
    }
  }
}

//-------------------------------- Invoice POS ------------------------------\\
function Invoice_POS(id) {
  loading.value = true;
  dialogInvoice.value = false;
  snackbar.value = false;
  axios
      .get("/sales_print_invoice/" + id)
      .then(({data}) => {
        invoice_pos.value = data;
        dialogInvoice.value = true;
        // if (response.data.pos_settings.is_printable) {
        //   setTimeout(() => print_it(), 1000);
        // }
      })
      .catch(() => {
      })
      .finally(() => {
        loading.value = false;
      });
}

//----------------------------------Create POS ------------------------------\\
function CreatePOS() {
  loading.value = true;

  axios
      .post("/pos/create_pos", {
        client_id: sale.value.client_id,
        warehouse_id: sale.value.warehouse_id,
        tax_rate: sale.value.tax_rate ? sale.value.tax_rate : 0,
        TaxNet: sale.value.TaxNet ? sale.value.TaxNet : 0,
        discount: sale.value.discount ? sale.value.discount : 0,
        shipping: sale.value.shipping ? sale.value.shipping : 0,
        sales_type: sale.value.sales_type_id,
        notes: sale.value.notes,
        details: details.value,
        GrandTotal: GrandTotal.value,
        payment: payment.value,
        amount: parseFloat(payment.value.amount).toFixed(2),
        received_amount: parseFloat(payment.value.received_amount).toFixed(2),
        change: parseFloat(payment.value.received_amount - payment.value.amount).toFixed(2),
      })
      .then(response => {
        if (response.data.success === true) {
          // Complete the animation of theprogress bar.
          Invoice_POS(response.data.id);
          dialogAddPayment.value = false;
          Reset_Pos();
        }
      })
      .catch(error => {
        snackbar.value = true;
        snackbarText.value = labels.error_message;
        snackbarColor.value = "error";
        console.log(error)
      })
      .finally(() => {
        loading.value = false;
      });
}

//---------------------------------Get Product Details ------------------------\\
function Get_Product_Details(product_item, product_id) {
  axios.get("/product/" + product_id).then(response => {
    product.value.discount = 0;
    product.value.DiscountNet = 0;
    product.value.discount_Method = "2";
    product.value.product_id = response.data.id;
    product.value.name = response.data.name;
    product.value.Net_price = response.data.Net_price;
    product.value.Total_price = response.data.Total_price;
    product.value.Unit_price = response.data.Unit_price;
    product.value.taxe = response.data.tax_price;
    product.value.tax_method = response.data.tax_method;
    product.value.tax_percent = response.data.tax_percent;
    product.value.unitSale = response.data.unitSale;
    product.value.product_variant_id = product_item.product_variant_id;
    product.value.code = product_item.code;
    product.value.fix_price = response.data.fix_price;
    product.value.sale_unit_id = response.data.sale_unit_id;
    add_product(product_item.code);
    CalculTotal();

  });
}

//----------- Calcul Total
function CalculTotal() {
  total.value = 0;
  for (let i = 0; i < details.value.length; i++) {
    let tax = details.value[i].taxe * details.value[i].quantity;
    details.value[i].subtotal = parseFloat(
        details.value[i].quantity * details.value[i].Net_price + tax
    );
    total.value = parseFloat(total.value + details.value[i].subtotal);
  }
  const total_without_discount = parseFloat(
      total.value - sale.value.discount
  );
  sale.value.TaxNet = parseFloat(
      (total_without_discount * sale.value.tax_rate) / 100
  );
  GrandTotal.value = parseFloat(
      total_without_discount + sale.value.TaxNet + sale.value.shipping
  );
  let grand_total = GrandTotal.value.toFixed(2);
  GrandTotal.value = parseFloat(grand_total);
}

//-------Verified QTY
function Verified_Qty(detail, id) {
  snackbar.value = false;
  for (let i = 0; i < details.value.length; i++) {
    if (details.value[i].detail_id === id) {
      if (isNaN(detail.quantity)) {
        details.value[i].quantity = detail.current;
      }
      if (detail.quantity > detail.current) {
        snackbar.value = true;
        snackbarText.value = labels.low_qty;
        snackbarColor.value = "error";
        details.value[i].quantity = detail.current;
      } else {
        details.value[i].quantity = detail.quantity;
      }
    }
  }
  CalculTotal();
}

//----------------------------------- Increment QTY with barcode scanner ------------------------------\\
function increment_qty_scanner(code) {
  snackbar.value = false;
  for (let i = 0; i < details.value.length; i++) {
    if (details.value[i].code === code) {
      if (details.value[i].quantity + 1 > details.value[i].current) {
        snackbar.value = true;
        snackbarText.value = labels.low_qty;
        snackbarColor.value = "error";
      } else {
        details.value[i].quantity++;
      }
    }
  }
  CalculTotal();
}

//----------------------------------- Increment QTY ------------------------------\\
function increment(detail_item, id) {
  snackbar.value = false;
  for (let i = 0; i < details.value.length; i++) {
    if (details.value[i].detail_id == id) {
      if (detail_item.quantity + 1 > detail_item.current) {
        snackbar.value = true;
        snackbarText.value = labels.low_qty;
        snackbarColor.value = "error";
      } else {
        details.value[i].quantity++;
      }
    }
  }
  CalculTotal();
}

//----------------------------------- decrement QTY ------------------------------\\
function decrement(detail_item, id) {
  snackbar.value = false;
  for (let i = 0; i < details.value.length; i++) {
    if (details.value[i].detail_id == id) {
      if (detail_item.quantity - 1 > detail_item.current || detail_item.quantity - 1 < 1) {
        snackbar.value = true;
        snackbarText.value = labels.low_qty;
        snackbarColor.value = "error";
      } else {
        details.value[i].quantity--;
      }
    }
  }
  CalculTotal();
}

//---------- keyup OrderTax
function keyup_OrderTax() {
  if (isNaN(sale.value.tax_rate)) {
    sale.value.tax_rate = 0;
  } else if (sale.value.tax_rate == '') {
    sale.value.tax_rate = 0;
    CalculTotal();
  } else {
    CalculTotal();
  }
}

//---------- keyup Discount
function keyup_Discount() {
  if (isNaN(sale.value.discount)) {
    sale.value.discount = 0;
  } else if (sale.value.discount == '') {
    sale.value.discount = 0;
    CalculTotal();
  } else {
    CalculTotal();
  }
}

//---------- keyup Shipping
function keyup_Shipping() {
  if (isNaN(sale.value.shipping)) {
    sale.value.shipping = 0;
  } else if (sale.value.shipping == '') {
    sale.value.shipping = 0;
    CalculTotal();
  } else {
    CalculTotal();
  }
}

//---------- keyup paid Amount
function Verified_paidAmount() {
  if (isNaN(payment.value.amount)) {
    payment.value.amount = 0;
  } else {
    if (payment.value.amount > payment.value.received_amount) {
      payment.value.amount = 0;
    } else if (payment.value.amount > GrandTotal.value) {
      payment.value.amount = 0;
    }
  }
}

//---------- keyup Received Amount
function Verified_Received_Amount() {
  if (isNaN(payment.value.received_amount)) {
    payment.value.received_amount = 0;
  }
}

//-----------------------------------Delete Detail Product ------------------------------\\
function delete_Product_Detail(id) {
  for (let i = 0; i < details.value.length; i++) {
    if (id === details.value[i].detail_id) {
      details.value.splice(i, 1);
      CalculTotal();
    }
  }
}

//----------Reset Pos
function Reset_Pos() {
  details.value = [];
  product.value = {};
  payment.value = {
    amount: "",
    received_amount: "",
    Reglement: "",
    notes: "",
  };
  sale.value.tax_rate = 0;
  sale.value.TaxNet = 0;
  sale.value.shipping = 0;
  sale.value.discount = 0;
  GrandTotal.value = 0;
  total.value = 0;
  category_id.value = "";
  getProducts(1);
}

//------------------------- get Result Value Search Product
function getResultValue(result) {
  return result.code + " " + "(" + result.name + ")";
}

//------------------------- Submit Search Product
function SearchProduct(result) {
  product.value = {};
  product.value.code = result.code;
  product.value.current = result.qte_sale;
  product.value.fix_stock = result.qte;
  if (result.qte_sale < 1) {
    product.value.quantity = result.qte_sale;
  } else {
    product.value.quantity = 1;
  }
  product.value.product_variant_id = result.product_variant_id;
  Get_Product_Details(result, result.id);
  search_input.value = '';
  product_filter.value = [];
}

// Search Products
// function search() {
//   snackbar.value = false;
//   if (search_input.value.length < 1) {
//     return product_filter.value = [];
//   }
//   if (sale.value.warehouse_id != "" && sale.value.warehouse_id != null) {
//
//     const product_filter = products_pos.value.filter(product => product.code === search_input.value || product.barcode.includes(search_input.value));
//     if (product_filter.length === 1) {
//       Check_Product_Exist(product_filter[0], product_filter[0].id);
//     } else {
//       product_filter.value = products_pos.value.filter(product => {
//         return (
//             product.name.toLowerCase().includes(search_input.value.toLowerCase()) ||
//             product.code.toLowerCase().includes(search_input.value.toLowerCase()) ||
//             product.barcode.toLowerCase().includes(search_input.value.toLowerCase())
//         );
//       });
//     }
//   } else {
//     snackbar.value = true;
//     snackbarText.value = labels.no_select_warehouse;
//     snackbarColor.value = "warning";
//   }
// }

//---------------------------------- Check if Product Exist in Order List ---------------------\\
function Check_Product_Exist(product_item, id) {
  // audio.value.play();
  // Start the progress bar.
  product.value = {};
  product.value.current = product_item.qte_sale;
  product.value.fix_stock = product_item.qte;
  if (product_item.qte_sale < 1) {
    product.value.quantity = product_item.qte_sale;
  } else {
    product.value.quantity = 1;
  }
  Get_Product_Details(product_item, id);
  // search_input.value = '';
  // this.$refs.product_autocomplete.value = "";
  product_filter.value = [];
}

//--- Get Products by Category
function Products_by_Category(id) {
  category_id.value = id;
  getProducts(1);
}

//--- Get All Category
function getAllCategory() {
  category_id.value = "";
  getProducts(1);
}

//------------------------------- Get Products with Filters ------------------------------\\
function getProducts(page = 1) {
  // Start the progress bar.
  axios
      .get(
          "/pos/get_products_pos?page=" +
          page +
          "&category_id=" +
          category_id.value +
          "&warehouse_id=" +
          sale.value.warehouse_id +
          "&stock=" + 1
      )
      .then(response => {
        product.values = response.data.products;
        // product.value_totalRows = response.data.totalRows;
        // product.value_paginatePerPage();
        // Complete the animation of theprogress bar.
      })
      .catch(response => {
        // Complete the animation of theprogress bar.
      });
}

//-------------------- Created Function -----\\

function created() {
  payment.value.amount = helper.formatNumber(GrandTotal.value, 2);
  payment.value.received_amount = helper.formatNumber(GrandTotal.value, 2);
  payment.value.Reglement = "cash";
  dialogAddPayment.value = true;
}
</script>
<template>
  <Layout :loading="loading">

    <snackbar
        v-model="snackbar"
        :snackbarColor="snackbarColor"
        :snackbarText="snackbarText"
    >
    </snackbar>
    <v-row>
      <!-- Card Left Panel Details Sale-->
      <v-col lg="5" md="6" cols="12">
        <v-card class="card-order">
          <v-toolbar>
            <v-btn icon="mdi-arrow-left-bold" variant="tonal" color="primary" density="comfortable"
                   @click="helper.linkVisit('/')"></v-btn>

            <v-spacer></v-spacer>
            <!-- Full screen toggle -->
            <full_screen></full_screen>
            <MenuUser></MenuUser>
          </v-toolbar>
          <v-card-text>
            <v-form ref="form" @submit.prevent="Submit_Pos">
              <v-row>
                <!-- Customer -->
                <v-col cols="12">
                  <v-autocomplete
                      v-model="sale.client_id"
                      @update:search="querySelectionClient"
                      :items="clientFilter"
                      :label="labels.sale.client_id"
                      item-title="name"
                      item-value="id"
                      variant="outlined"
                      density="comfortable"
                      hide-details="auto"
                      clearable
                      :rules="helper.required"
                  >
                    <template v-slot:append>
                      <v-btn color="primary" icon="mdi-account-plus" density="comfortable" class="rounded"
                             @click="New_Client()"></v-btn>
                    </template>
                  </v-autocomplete>
                </v-col>

                <!-- warehouse -->
                <v-col cols="12">
                  <v-autocomplete
                      v-model="sale.warehouse_id"
                      @update:search="Selected_Warehouse"
                      :items="warehouses"
                      :label="labels.sale.warehouse_id"
                      item-title="name"
                      item-value="id"
                      variant="outlined"
                      density="comfortable"
                      hide-details="auto"
                      clearable
                      :rules="helper.required"
                  >
                  </v-autocomplete>
                </v-col>
                <!-- Details Product  -->
                <v-col cols="12">
                  <div class="pos-detail">
                    <v-table density="compact" hover>
                      <thead>
                      <tr>
                        <th>{{ labels.sale.details.product }}</th>
                        <th>{{ labels.sale.details.price }}</th>
                        <th class="text-center">{{ labels.sale.details.qty }}</th>
                        <th class="text-center">{{ labels.sale.details.sub_total }}</th>
                        <th class="text-center">
                          <v-icon icon="mdi-delete"></v-icon>
                        </th>
                      </tr>
                      </thead>
                      <tbody>
                      <tr v-if="details.length <= 0">
                        <td colspan="5">{{ labels.no_data_table }}</td>
                      </tr>
                      <tr v-for="(detail_item, index) in details" :key="index">
                        <td>
                          <span>{{ detail_item.code }}</span>
                          <br>
                          <span class="badge badge-success">{{ detail_item.name }}</span>
                          <v-icon @click="Modal_Updat_Detail(detail_item)" icon="mdi-pencil-box-outline"></v-icon>
                        </td>
                        <td>{{ currency }} {{ helper.formatNumber(detail_item.Total_price, 2) }}</td>
                        <td style="min-width: 140px">
                          <v-text-field
                              variant="outlined"
                              density="compact"
                              hide-details="auto"
                              :rules="helper.number"
                              v-model="detail_item.quantity"
                              @keyup="Verified_Qty(detail_item,detail_item.detail_id)"
                              :min="0.0"
                              :max="detail_item.current"
                          >
                            <template v-slot:append>
                              <v-icon
                                  color="secundary"
                                  @click="increment(detail_item,detail_item.detail_id)"
                                  icon="mdi-plus-box"
                              ></v-icon>
                            </template>
                            <template v-slot:prepend>
                              <v-icon
                                  color="secundary"
                                  @click="decrement(detail_item, detail_item.detail_id ) "
                                  icon="mdi-minus-box"
                              ></v-icon>
                            </template>
                          </v-text-field>
                        </td>
                        <td class="text-center">{{ currency }} {{ detail_item.subtotal.toFixed(2) }}</td>
                        <td>
                          <v-btn
                              @click="delete_Product_Detail(detail_item.detail_id)"
                              title="Delete"
                              icon="mdi-delete"
                              color="error"
                              density="comfortable"
                              size="small"
                              variant="tonal"
                          >
                          </v-btn>
                        </td>
                      </tr>
                      </tbody>
                    </v-table>
                  </div>
                </v-col>
              </v-row>


              <!-- Calcul Grand Total -->
              <div class="footer_panel">
                <v-row>
                  <v-col cols="12">
                    <v-card color="secondary" variant="tonal">
                      <v-card-text class="text-center">
                        <h2><strong>Total :</strong> {{ currency }} {{ GrandTotal.toFixed(2) }}</h2>
                      </v-card-text>
                    </v-card>

                  </v-col>

                  <!--                      &lt;!&ndash; Order Tax  &ndash;&gt;-->
                  <!--                      <v-col lg="4" md="4" sm="12">-->
                  <!--                        <validation-provider-->
                  <!--                            name="Order Tax"-->
                  <!--                            :rules="{ regex: /^\d*\.?\d*$/}"-->
                  <!--                            v-slot="validationContext"-->
                  <!--                        >-->
                  <!--                          <v-form-group :label="$t('Tax')" append="%">-->
                  <!--                            <v-input-group append="%">-->
                  <!--                              <v-form-input-->
                  <!--                                  :state="getValidationState(validationContext)"-->
                  <!--                                  aria-describedby="OrderTax-feedback"-->
                  <!--                                  label="Order Tax"-->
                  <!--                                  v-model.number="sale.tax_rate"-->
                  <!--                                  @keyup="keyup_OrderTax()"-->
                  <!--                              ></v-form-input>-->
                  <!--                            </v-input-group>-->
                  <!--                            <v-form-invalid-feedback-->
                  <!--                                id="OrderTax-feedback"-->
                  <!--                            >{{ validationContext.errors[0] }}</v-form-invalid-feedback>-->
                  <!--                          </v-form-group>-->
                  <!--                        </validation-provider>-->
                  <!--                      </v-col>-->

                  <!-- Discount -->
                  <v-col lg="4" md="4" cols="12">
                    <!--                              <validation-provider-->
                    <!--                            name="Discount"-->
                    <!--                            :rules="{ regex: /^\d*\.?\d*$/}"-->
                    <!--                            v-slot="validationContext"-->
                    <!--                        >-->
                    <!--                          <v-form-group :label="$t('Discount')" append="%">-->
                    <!--                            <v-input-group :append="currency">-->
                    <!--                              <v-form-input-->
                    <!--                                  :state="getValidationState(validationContext)"-->
                    <!--                                  aria-describedby="Discount-feedback"-->
                    <!--                                  label="Discount"-->
                    <!--                                  v-model.number="sale.discount"-->
                    <!--                                  @keyup="keyup_Discount()"-->
                    <!--                              ></v-form-input>-->
                    <!--                            </v-input-group>-->
                    <!--                            <v-form-invalid-feedback-->
                    <!--                                id="Discount-feedback"-->
                    <!--                            >{{ validationContext.errors[0] }}</v-form-invalid-feedback>-->
                    <!--                          </v-form-group>-->
                    <!--                        </validation-provider>-->
                  </v-col>

                </v-row>
                <v-row>
                  <v-col md="6" cols="12">
                    <v-btn
                        @click="Reset_Pos()"
                        color="error"
                        prepend-icon="mdi-replay"
                        block
                    >
                      Reiniciar
                    </v-btn>
                  </v-col>
                  <v-col md="6" cols="12">
                    <v-btn
                        type="submit"
                        color="success"
                        block
                        prepend-icon="mdi-cart">
                      Pagar Ahora
                    </v-btn>
                  </v-col>
                </v-row>
              </div>
            </v-form>
          </v-card-text>


          <!-- Update Detail Product -->
          <v-dialog max-width="600" v-model="dialogUpdateDetail">
            <v-card>
              <v-toolbar>
                <v-toolbar-title :text="detail.name"></v-toolbar-title>
              </v-toolbar>
              <v-card-text>
                <v-form @submit.prevent="submit_Update_Detail" ref="formUpdateDetail">
                  <v-row>
                    <!-- Unit Price -->
                    <v-col lg="6" md="6" cols="12">
                      <!--                      <validation-provider-->
                      <!--                          name="Product Price"-->
                      <!--                          :rules="{ required: true , regex: /^\d*\.?\d*$/}"-->
                      <!--                          v-slot="validationContext"-->
                      <!--                      >-->
                      <!--                        <v-form-group :label="$t('ProductPrice') + ' ' + '*'" id="Price-input">-->
                      <!--                          <v-form-input-->
                      <!--                              label="Product Price"-->
                      <!--                              v-model="detail.Unit_price"-->
                      <!--                              :state="getValidationState(validationContext)"-->
                      <!--                              aria-describedby="Price-feedback"-->
                      <!--                          ></v-form-input>-->
                      <!--                          <v-form-invalid-feedback-->
                      <!--                              id="Price-feedback"-->
                      <!--                          >{{ validationContext.errors[0] }}</v-form-invalid-feedback>-->
                      <!--                        </v-form-group>-->
                      <!--                      </validation-provider>-->
                      <!--                    </v-col>-->

                      <!--                    &lt;!&ndash; Tax Method &ndash;&gt;-->
                      <!--                    <v-col lg="6" md="6" sm="12">-->
                      <!--                      <validation-provider name="Tax Method" :rules="{ required: true}">-->
                      <!--                        <v-form-group slot-scope="{ valid, errors }" :label="$t('TaxMethod') + ' ' + '*'">-->
                      <!--                          <v-select-->
                      <!--                              :class="{'is-invalid': !!errors.length}"-->
                      <!--                              :state="errors[0] ? false : (valid ? true : null)"-->
                      <!--                              v-model="detail.tax_method"-->
                      <!--                              :reduce="label => label.value"-->
                      <!--                              :placeholder="$t('Choose_Method')"-->
                      <!--                              :options="-->
                      <!--                           [-->
                      <!--                            {label: 'Exclusive', value: '1'},-->
                      <!--                            {label: 'Inclusive', value: '2'}-->
                      <!--                           ]"-->
                      <!--                          ></v-select>-->
                      <!--                          <v-form-invalid-feedback>{{ errors[0] }}</v-form-invalid-feedback>-->
                      <!--                        </v-form-group>-->
                      <!--                      </validation-provider>-->
                      <!--                    </v-col>-->

                      <!--                    &lt;!&ndash; Tax &ndash;&gt;-->
                      <!--                    <v-col lg="6" md="6" sm="12">-->
                      <!--                      <validation-provider-->
                      <!--                          name="Tax"-->
                      <!--                          :rules="{ required: true , regex: /^\d*\.?\d*$/}"-->
                      <!--                          v-slot="validationContext"-->
                      <!--                      >-->
                      <!--                        <v-form-group :label="$t('Tax') + ' ' + '*'">-->
                      <!--                          <v-input-group append="%">-->
                      <!--                            <v-form-input-->
                      <!--                                label="Tax"-->
                      <!--                                v-model="detail.tax_percent"-->
                      <!--                                :state="getValidationState(validationContext)"-->
                      <!--                                aria-describedby="Tax-feedback"-->
                      <!--                            ></v-form-input>-->
                      <!--                          </v-input-group>-->
                      <!--                          <v-form-invalid-feedback-->
                      <!--                              id="Tax-feedback"-->
                      <!--                          >{{ validationContext.errors[0] }}</v-form-invalid-feedback>-->
                      <!--                        </v-form-group>-->
                      <!--                      </validation-provider>-->
                      <!--                    </v-col>-->

                      <!--                    &lt;!&ndash; Discount Method &ndash;&gt;-->
                      <!--                    <v-col lg="6" md="6" sm="12">-->
                      <!--                      <validation-provider name="Discount Method" :rules="{ required: true}">-->
                      <!--                        <v-form-group slot-scope="{ valid, errors }" :label="$t('Discount_Method') + ' ' + '*'">-->
                      <!--                          <v-select-->
                      <!--                              v-model="detail.discount_Method"-->
                      <!--                              :reduce="label => label.value"-->
                      <!--                              :placeholder="$t('Choose_Method')"-->
                      <!--                              :class="{'is-invalid': !!errors.length}"-->
                      <!--                              :state="errors[0] ? false : (valid ? true : null)"-->
                      <!--                              :options="-->
                      <!--                              [-->
                      <!--                                {label: 'Percent %', value: '1'},-->
                      <!--                                {label: 'Fixed', value: '2'}-->
                      <!--                              ]"-->
                      <!--                          ></v-select>-->
                      <!--                          <v-form-invalid-feedback>{{ errors[0] }}</v-form-invalid-feedback>-->
                      <!--                        </v-form-group>-->
                      <!--                      </validation-provider>-->
                      <!--                    </v-col>-->

                      <!--                    &lt;!&ndash; Discount Rate &ndash;&gt;-->
                      <!--                    <v-col lg="6" md="6" sm="12">-->
                      <!--                      <validation-provider-->
                      <!--                          name="Discount Rate"-->
                      <!--                          :rules="{ required: true , regex: /^\d*\.?\d*$/}"-->
                      <!--                          v-slot="validationContext"-->
                      <!--                      >-->
                      <!--                        <v-form-group :label="$t('Discount') + ' ' + '*'">-->
                      <!--                          <v-form-input-->
                      <!--                              label="Discount"-->
                      <!--                              v-model="detail.discount"-->
                      <!--                              :state="getValidationState(validationContext)"-->
                      <!--                              aria-describedby="Discount-feedback"-->
                      <!--                          ></v-form-input>-->
                      <!--                          <v-form-invalid-feedback-->
                      <!--                              id="Discount-feedback"-->
                      <!--                          >{{ validationContext.errors[0] }}</v-form-invalid-feedback>-->
                      <!--                        </v-form-group>-->
                      <!--                      </validation-provider>-->
                      <!--                    </v-col>-->

                      <!--                    &lt;!&ndash; Unit Sale &ndash;&gt;-->
                      <!--                    <v-col lg="6" md="6" sm="12">-->
                      <!--                      <validation-provider name="Unit Sale" :rules="{ required: true}">-->
                      <!--                        <v-form-group slot-scope="{ valid, errors }" :label="$t('UnitSale') + ' ' + '*'">-->
                      <!--                          <v-select-->
                      <!--                              :class="{'is-invalid': !!errors.length}"-->
                      <!--                              :state="errors[0] ? false : (valid ? true : null)"-->
                      <!--                              v-model="detail.sale_unit_id"-->
                      <!--                              :placeholder="$t('Choose_Unit_Sale')"-->
                      <!--                              :reduce="label => label.value"-->
                      <!--                              :options="units.map(units => ({label: units.name, value: units.id}))"-->
                      <!--                          />-->
                      <!--                          <v-form-invalid-feedback>{{ errors[0] }}</v-form-invalid-feedback>-->
                      <!--                        </v-form-group>-->
                      <!--                      </validation-provider>-->
                      <!--                    </v-col>-->

                      <!--                    &lt;!&ndash; Imei or serial numbers &ndash;&gt;-->
                      <!--                    <v-col lg="12" md="12" sm="12" v-show="detail.is_imei">-->
                      <!--                      <v-form-group :label="$t('Add_product_IMEI_Serial_number')">-->
                      <!--                        <v-form-input-->
                      <!--                            label="Add_product_IMEI_Serial_number"-->
                      <!--                            v-model="detail.imei_number"-->
                      <!--                            :placeholder="$t('Add_product_IMEI_Serial_number')"-->
                      <!--                        ></v-form-input>-->
                      <!--                      </v-form-group>-->
                      <!--                    </v-col>-->

                      <!--                    <v-col md="12">-->
                      <!--                      <v-form-group>-->
                      <!--                        <v-button variant="primary" type="submit">{{$t('submit')}}</v-button>-->
                      <!--                      </v-form-group>-->
                    </v-col>
                  </v-row>
                </v-form>
              </v-card-text>
            </v-card>
          </v-dialog>
        </v-card>
      </v-col>

      <!-- Card right Of Products -->
      <v-col lg="7" md="6" cols="12">
        <v-card>
          <v-card-text>
            <v-row>
              <v-col md="6" cols="12">
                <v-btn block color="info">
                  <i class="i-Two-Windows"></i>
                  {{ labels.list_category }}
                </v-btn>
              </v-col>
              <!--              <v-col md="6">-->
              <!--                <button v-v-toggle.sidebar-brand class="btn btn-outline-info mt-1 btn-block">-->
              <!--                  <i class="i-Library"></i>-->
              <!--                  {{$t('ListofBrand')}}-->
              <!--                </button>-->
              <!--              </v-col>-->


              <!-- Product -->
              <v-col cols="12" class="mt-2 mv-2">
                <v-data-iterator
                    :items="products_pos"
                    :search="searchProducts"
                >
                  <template v-slot:header>
                    <div class="mb-3">
                      <v-text-field
                          v-model="searchProducts"
                          clearable
                          hide-details
                          prepend-inner-icon="mdi-magnify"
                          placeholder="Search"
                          variant="solo"
                          density="comfortable"
                      ></v-text-field>
                    </div>

                  </template>

                  <template v-slot:no-data>
                    <v-alert type="warning">{{ labels.no_data_products }}</v-alert>
                  </template>

                  <template v-slot:default="props">
                    <v-row>
                      <v-col
                          v-for="item in props.items"
                          :key="item.id"
                          cols="12"
                          sm="6"
                          md="4"
                          lg="3"
                      >
                        <v-card variant="elevated" color="white" @click="Check_Product_Exist( item.raw ,  item.raw.id)">
                          <v-card-item>
                            <p class="font-weight-bold text-h6">{{ item.raw.name }}</p>
                            <p class="text-medium-emphasis text-subtitle-2">COD: {{ item.raw.code }}</p>

                            <v-chip color="primary" size="x-small" class="ma-1">{{ currency }}
                              {{ helper.formatNumber(item.raw.Net_price, 2) }}
                            </v-chip>
                            <v-chip size="x-small" color="info" class="ma-1">
                              {{ helper.formatNumber(item.raw.qte_sale, 2) }}
                              {{ item.raw.unitSale }}
                            </v-chip>
                          </v-card-item>
                        </v-card>
                      </v-col>
                    </v-row>
                  </template>
                </v-data-iterator>
              </v-col>
            </v-row>
          </v-card-text>
        </v-card>
      </v-col>

      <!--        &lt;!&ndash; Sidebar category &ndash;&gt;-->
      <!--        <v-sidebar-->
      <!--            id="sidebar-category"-->
      <!--            :title="$t('ListofCategory')"-->
      <!--            bg-variant="white"-->
      <!--            right-->
      <!--            shadow-->
      <!--        >-->
      <!--          <div class="px-3 py-2">-->
      <!--            <v-row>-->
      <!--              <div class="col-md-12 d-flex flex-row flex-wrap bd-highlight list-item mt-2">-->
      <!--                <div-->
      <!--                    @click="getAllCategory()"-->
      <!--                    :class="{ 'brand-Active' : category_id == ''}"-->
      <!--                    class="card o-hidden bd-highlight m-1"-->
      <!--                >-->
      <!--                  <div class="list-thumb d-flex">-->
      <!--                    <img alt :src="'/images/no-image.png'">-->
      <!--                  </div>-->
      <!--                  <div class="flex-grow-1 d-bock">-->
      <!--                    <div-->
      <!--                        class="card-body align-self-center d-flex flex-column justify-content-between align-items-lg-center"-->
      <!--                    >-->
      <!--                      <div class="item-title">{{$t('All_Category')}}</div>-->
      <!--                    </div>-->
      <!--                  </div>-->
      <!--                </div>-->
      <!--                <div-->
      <!--                    class="card o-hidden bd-highlight m-1"-->
      <!--                    v-for="category in paginated_Category"-->
      <!--                    :key="category.id"-->
      <!--                    @click="Products_by_Category(category.id)"-->
      <!--                    :class="{ 'brand-Active' : category.id === category_id}"-->
      <!--                >-->
      <!--                  <img alt :src="'/images/no-image.png'">-->
      <!--                  <div class="flex-grow-1 d-bock">-->
      <!--                    <div-->
      <!--                        class="card-body align-self-center d-flex flex-column justify-content-between align-items-lg-center"-->
      <!--                    >-->
      <!--                      <div class="item-title">{{category.name}}</div>-->
      <!--                    </div>-->
      <!--                  </div>-->
      <!--                </div>-->
      <!--              </div>-->
      <!--            </v-row>-->

      <!--            <v-row>-->
      <!--              <v-col md="12" class="mt-4">-->
      <!--                <v-pagination-->
      <!--                    @change="Category_onPageChanged"-->
      <!--                    :total-rows="category_totalRows"-->
      <!--                    :per-page="category_perPage"-->
      <!--                    v-model="category_currentPage"-->
      <!--                    class="my-0 gull-pagination align-items-center"-->
      <!--                    align="center"-->
      <!--                    first-text-->
      <!--                    last-text-->
      <!--                >-->
      <!--                  <p class="list-arrow m-0" slot="prev-text">-->
      <!--                    <i class="i-Arrow-Left text-40"></i>-->
      <!--                  </p>-->
      <!--                  <p class="list-arrow m-0" slot="next-text">-->
      <!--                    <i class="i-Arrow-Right text-40"></i>-->
      <!--                  </p>-->
      <!--                </v-pagination>-->
      <!--              </v-col>-->
      <!--            </v-row>-->
      <!--          </div>-->
      <!--        </v-sidebar>-->

      <!-- Modal Show Invoice POS-->
      <invoice-dialog :model="dialogInvoice" :invoice_pos="invoice_pos"></invoice-dialog>

      <!-- Modal Add Payment-->
      <v-dialog v-model="dialogAddPayment" width="800">
        <v-card>
          <v-toolbar>
            <v-toolbar-title>{{ labels.add_pay }}</v-toolbar-title>
            <v-spacer></v-spacer>
            <v-btn icon="mdi-close" size="small" density="comfortable" variant="tonal"
                   @click="dialogAddPayment=false"></v-btn>
          </v-toolbar>
          <v-card-text>
            <v-form @submit.prevent="Submit_Payment" ref="formAddPayment">
              <v-row>
                <v-col sm="6" cols="12">
                  <v-row>
                    <!-- Received  Amount  -->
                    <v-col cols="12">
                      <v-text-field
                          @keyup="Verified_Received_Amount(payment.received_amount)"
                          :label="labels.payment.received_amount"
                          v-model="payment.received_amount"
                          :placeholder="labels.payment.received_amount"
                          variant="outlined"
                          density="comfortable"
                          hide-details="auto"
                      >
                      </v-text-field>
                    </v-col>
                    <!-- Paying  Amount  -->
                    <v-col cols="12">
                      <v-text-field
                          @keyup="Verified_paidAmount(payment.amount)"
                          :label="labels.payment.amount"
                          v-model="payment.amount"
                          :placeholder="labels.payment.amount"
                          variant="outlined"
                          density="comfortable"
                          hide-details="auto"
                      >
                      </v-text-field>
                    </v-col>
                    <!-- change  Amount  -->
                    <v-col cols="12">
                      <label>{{ labels.payment.change }} :</label>
                      <p class="text-disabled">
                        {{ parseFloat(payment.received_amount - payment.amount).toFixed(2) }}
                      </p>
                    </v-col>
                  </v-row>
                </v-col>
                <v-col cols="12" sm="6">
                  <v-row>
                    <v-col cols="12">
                      <v-card>
                        <v-table>
                          <tbody>
                          <tr>
                            <td>{{ labels.total_products }}</td>
                            <td class="text-right">
                              <v-chip size="small" color="primary">{{ details.length }}</v-chip>
                            </td>
                          </tr>
                          <tr>
                            <td>{{ labels.sale.discount }}</td>
                            <td class="text-right font-weight-bold">{{ currency }} {{ sale.discount.toFixed(2) }}</td>
                          </tr>
                          <tr>
                            <td>{{ labels.sale.GrandTotal }}</td>
                            <td class="text-right font-weight-bold">{{ currency }} {{ GrandTotal.toFixed(2) }}</td>
                          </tr>
                          </tbody>
                        </v-table>
                      </v-card>
                    </v-col>
                  </v-row>
                </v-col>

                <!-- Payment choice -->
                <v-col cols="12" sm="6">
                  <v-select
                      v-model="payment.Reglement"
                      :items="helper.reglamentPayment()"
                      :rules="helper.required"
                      :label="labels.payment.role"
                      item-title="title"
                      item-value="value"
                      variant="outlined"
                      density="comfortable"
                      hide-details="auto"
                  ></v-select>
                </v-col>
                <!-- Sales type  -->
                <v-col cols="12" sm="6">
                  <v-select
                      v-model="sale.sales_type_id"
                      variant="outlined"
                      density="compact"
                      hide-details="auto"
                      :items="sales_types"
                      :label="labels.sale.sales_type_id + ' *'"
                      :rules="helper.required"
                      item-value="id"
                      item-title="name"
                  ></v-select>
                </v-col>

                <!-- payment Note -->
                <v-col cols="12" sm="6">
                  <v-textarea
                      rows="3"
                      :label="labels.payment.notes"
                      v-model="payment.notes"
                      :placeholder="labels.payment.notes"
                      variant="outlined"
                      density="comfortable"
                      hide-details="auto"
                  ></v-textarea>
                </v-col>
                <!-- sale Note -->
                <v-col cols="12" sm="6">
                  <v-textarea
                      rows="3"
                      :label="labels.sale.notes"
                      v-model="sale.notes"
                      :placeholder="labels.sale.notes"
                      variant="outlined"
                      density="comfortable"
                      hide-details="auto"
                  ></v-textarea>
                </v-col>
                <v-col md="12" class="mt-3">
                  <v-btn color="primary" type="submit" :disabled="loading">
                    {{ labels.submit }}
                  </v-btn>

                </v-col>
              </v-row>
            </v-form>
          </v-card-text>
        </v-card>
      </v-dialog>

      <!--        <validation-observer ref="Create_Customer">-->
      <!--          <v-modal hide-footer size="lg" id="New_Customer" :title="$t('Add')">-->
      <!--            <v-form @submit.prevent="Submit_Customer">-->
      <!--              <v-row>-->
      <!--                &lt;!&ndash; Customer Name &ndash;&gt;-->
      <!--                <v-col md="6" sm="12">-->
      <!--                  <validation-provider-->
      <!--                      name="Name Customer"-->
      <!--                      :rules="{ required: true}"-->
      <!--                      v-slot="validationContext"-->
      <!--                  >-->
      <!--                    <v-form-group :label="$t('CustomerName') + ' ' + '*'">-->
      <!--                      <v-form-input-->
      <!--                          :state="getValidationState(validationContext)"-->
      <!--                          aria-describedby="name-feedback"-->
      <!--                          label="name"-->
      <!--                          v-model="client.name"-->
      <!--                          :placeholder="$t('CustomerName')"-->
      <!--                      ></v-form-input>-->
      <!--                      <v-form-invalid-feedback id="name-feedback">{{ validationContext.errors[0] }}</v-form-invalid-feedback>-->
      <!--                    </v-form-group>-->
      <!--                  </validation-provider>-->
      <!--                </v-col>-->

      <!--                &lt;!&ndash; Customer Email &ndash;&gt;-->
      <!--                <v-col md="6" sm="12">-->
      <!--                  <v-form-group :label="$t('Email')">-->
      <!--                    <v-form-input-->
      <!--                        label="email"-->
      <!--                        v-model="client.email"-->
      <!--                        :placeholder="$t('Email')"-->
      <!--                    ></v-form-input>-->
      <!--                  </v-form-group>-->
      <!--                </v-col>-->

      <!--                &lt;!&ndash; Customer Phone &ndash;&gt;-->
      <!--                <v-col md="6" sm="12">-->
      <!--                  <v-form-group :label="$t('Phone')">-->
      <!--                    <v-form-input-->
      <!--                        label="Phone"-->
      <!--                        v-model="client.phone"-->
      <!--                        :placeholder="$t('Phone')"-->
      <!--                    ></v-form-input>-->
      <!--                  </v-form-group>-->
      <!--                </v-col>-->

      <!--                &lt;!&ndash; Customer Country &ndash;&gt;-->
      <!--                <v-col md="6" sm="12">-->
      <!--                  <v-form-group :label="$t('Country')">-->
      <!--                    <v-form-input-->
      <!--                        label="Country"-->
      <!--                        v-model="client.country"-->
      <!--                        :placeholder="$t('Country')"-->
      <!--                    ></v-form-input>-->
      <!--                  </v-form-group>-->
      <!--                </v-col>-->

      <!--                &lt;!&ndash; Customer City &ndash;&gt;-->
      <!--                <v-col md="6" sm="12">-->
      <!--                  <v-form-group :label="$t('City')">-->
      <!--                    <v-form-input-->
      <!--                        label="City"-->
      <!--                        v-model="client.city"-->
      <!--                        :placeholder="$t('City')"-->
      <!--                    ></v-form-input>-->
      <!--                  </v-form-group>-->
      <!--                </v-col>-->

      <!--                &lt;!&ndash; Customer Tax Number &ndash;&gt;-->
      <!--                <v-col md="6" sm="12">-->
      <!--                  <v-form-group :label="$t('Tax_Number')">-->
      <!--                    <v-form-input-->
      <!--                        label="Tax Number"-->
      <!--                        v-model="client.tax_number"-->
      <!--                        :placeholder="$t('Tax_Number')"-->
      <!--                    ></v-form-input>-->
      <!--                  </v-form-group>-->
      <!--                </v-col>-->


      <!--                &lt;!&ndash; Customer Adress &ndash;&gt;-->
      <!--                <v-col md="12" sm="12">-->
      <!--                  <v-form-group :label="$t('Adress')">-->
      <!--                      <textarea-->
      <!--                          label="Adress"-->
      <!--                          class="form-control"-->
      <!--                          rows="4"-->
      <!--                          v-model="client.adresse"-->
      <!--                          :placeholder="$t('Adress')"-->
      <!--                      ></textarea>-->
      <!--                  </v-form-group>-->
      <!--                </v-col>-->

      <!--                <v-col md="12" class="mt-3">-->
      <!--                  <v-button variant="primary" type="submit">{{$t('submit')}}</v-button>-->
      <!--                </v-col>-->
      <!--              </v-row>-->
      <!--            </v-form>-->
      <!--          </v-modal>-->
      <!--        </validation-observer>-->
      <!--      </v-row>-->
      <!--    </div>-->
    </v-row>
  </Layout>
</template>
<style>
.total {
  font-weight: bold;
  font-size: 14px;
  /* text-transform: uppercase; */
  /* height: 50px; */
}
</style>