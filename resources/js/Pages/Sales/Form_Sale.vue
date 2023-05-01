<script setup>
import { ref, onMounted, watch } from "vue";
import Layout from "@/Layouts/Authenticated.vue";
import { router } from "@inertiajs/vue3";
import ruleForm from "@/rules";
import Snackbar from "@/Components/snackbar.vue";
import Vue3TagsInput from "vue3-tags-input";

const props = defineProps({
    clients: Object,
    warehouses: Object,
    sale: { type: Object, default: null },
    errors: Object,
});

const form = ref(null);
const loading = ref(false);
const loadingFilter = ref(false);
const snackbar = ref(false);
const snackbarText = ref("");
const snackbarColor = ref("info");
const search_input = ref("");

const client = ref({});
const products = ref([]);
const detailsForm = ref([]);
const detail = ref({});
const payment = ref({
    status: "pending",
    Reglement: "Cash",
    amount: "",
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
    tax_rate: 0,
    TaxNet: 0,
    shipping: 0,
    discount: 0,
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
        payment.value.amount = ruleForm.formatNumber(payment_amount, 2);
        payment.value.received_amount = ruleForm.formatNumber(
            payment_amount,
            2
        );
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
    if (validate.valid) {
        snackbar.value = true;
        snackbarText.value = "llene el formulario correctamente";
        snackbarColor.value = "error";
    } else if (payment.value.amount > payment.value.received_amount) {
        snackbar.value = true;
        snackbarText.value = "el monto a pagar es mayor al monto recibido";
        snackbarColor.value = "warning";
        payment.value.received_amount = 0;
    } else if (payment.value.amount > GrandTotal.value) {
        snackbar.value = true;
        snackbarText.value = "el monto a pagar es mayor al monto total";
        snackbarColor.value = "warning";
        payment.value.amount = 0;
    } else {
        Create_Sale();
    }
}
//---------------------- get_units ------------------------------\\
function get_units(value) {
    axios
        .get("/get_units?id=" + value)
        .then(({ data }) => (units.value = data));
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

function getResultValue(result) {
    return result.code + " " + "(" + result.name + ")";
}

//---------------------- Event Select Warehouse ------------------------------\\
function Selected_Warehouse(value) {
    search_input.value = "";
    product_filter.value = [];
    Get_Products_By_Warehouse(value);
}

//------------------------------------ Get Products By Warehouse -------------------------\\

//------------------------------------ Get Products By Warehouse -------------------------\\

function Get_Products_By_Warehouse(id) {
    axios
        .get("/get_Products_by_warehouse/" + id + "?stock=" + 0)
        .then((response) => {
            products.value = response.data;
        })
        .catch((error) => {});
}

//----------------------------------------- Add Product to order list -------------------------\\
function add_product() {
    if (details.value.length > 0) {
        Last_Detail_id();
    } else if (details.value.length === 0) {
        product.value.detail_id = 1;
    }

    details.value.push(product.value);
}

//-----------------------------------Verified QTY ------------------------------\\
function Verified_Qty(detail, id) {
    for (let i = 0; i < details.value.length; i++) {
        if (details.value[i].detail_id === id) {
            if (isNaN(detail.quantity)) {
                details.value[i].quantity = detail.stock;
            }

            if (detail.quantity > detail.stock) {
                snackbar.value = true;
                snackbarText.value = "bajo stock";
                snackbarColor.value = "warning";
                details.value[i].quantity = detail.stock;
            } else {
                details.value[i].quantity = detail.quantity;
            }
        }
    }
    Calcul_Total();
}

//-----------------------------------increment QTY ------------------------------\\

function increment(detail, id) {
    for (let i = 0; i < details.value.length; i++) {
        if (details.value[i].detail_id == id) {
            if (detail.quantity + 1 > detail.stock) {
                snackbar.value = true;
                snackbarText.value = "bajo stock";
                snackbarColor.value = "warning";
            } else {
                ruleForm.formatNumber(details.value[i].quantity++, 2);
            }
        }
    }
    Calcul_Total();
}

//-----------------------------------decrement QTY ------------------------------\\

function decrement(detail, id) {
    for (let i = 0; i < details.value.length; i++) {
        if (details.value[i].detail_id == id) {
            if (detail.quantity - 1 > 0) {
                if (detail.quantity - 1 > detail.stock) {
                    snackbar.value = true;
                    snackbarText.value = "bajo stock";
                    snackbarColor.value = "warning";
                } else {
                    ruleForm.formatNumber(details.value[i].quantity--, 2);
                }
            }
        }
    }
    Calcul_Total();
}

//-----------------------------------------Calcul Total ------------------------------\\
function Calcul_Total() {
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

    if (payment.value.status == "paid") {
        payment.value.amount = this.formatNumber(GrandTotal.value, 2);
    }
}

// -----------------------------------Delete Detail Product ------------------------------\\
function delete_Product_Detail(id) {
    for (let i = 0; i < details.value.length; i++) {
        if (id === details.value[i].detail_id) {
            details.value.splice(i, 1);
            Calcul_Total();
        }
    }
}

//-----------------------------------verified Order List ------------------------------\\
function verifiedForm() {
    if (details.value.length <= 0) {
        snackbar.value = true;
        snackbarText.value = "debes añadir un producto";
        snackbarColor.value = "warning";
        return false;
    } else {
        let count = 0;
        for (let i = 0; i < details.value.length; i++) {
            if (
                details.value[i].quantity == "" ||
                details.value[i].quantity === 0
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
        payment.valueProcessing = true;
        axios
            .post("sales", {
                date: sale.value.date,
                client_id: sale.value.client_id,
                warehouse_id: sale.value.warehouse_id,
                statut: sale.value.statut,
                notes: sale.value.notes,
                tax_rate: sale.value.tax_rate ? sale.value.tax_rate : 0,
                TaxNet: sale.value.TaxNet ? sale.value.TaxNet : 0,
                discount: sale.value.discount ? sale.value.discount : 0,
                shipping: sale.value.shipping ? sale.value.shipping : 0,
                GrandTotal: GrandTotal.value,
                details: details.value,
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
                payment.valueProcessing = false;
                router.visit("/sales");
            })
            .catch((error) => {
                snackbar.value = true;
                snackbarText.value = "Pago invalido";
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
    let len = details.value.length;
    product.value.detail_id = details.value[len - 1].detail_id + 1;
}

//---------------------------------Get Product Details ------------------------\\

function Get_Product_Details(product_id) {
    axios.get("/products/" + product_id).then((response) => {
        product.value.discount = 0;
        product.value.DiscountNet = 0;
        product.value.discount_Method = "2";
        product.value.product_id = response.data.id;
        product.value.name = response.data.name;
        product.value.Net_price = response.data.Net_price;
        product.value.Unit_price = response.data.Unit_price;
        product.value.taxe = response.data.tax_price;
        product.value.tax_method = response.data.tax_method;
        product.value.tax_percent = response.data.tax_percent;
        product.value.unitSale = response.data.unitSale;
        product.value.fix_price = response.data.fix_price;
        product.value.sale_unit_id = response.data.sale_unit_id;
        product.value.is_imei = response.data.is_imei;
        product.value.imei_number = "";
        add_product();
        Calcul_Total();
    });
}
</script>
<template>
    <!--  <div class="main-content">-->
    <!--    <breadcumb :page="$t('AddSale')" :folder="$t('ListSales')"/>-->
    <!--    <div v-if="isLoading" class="loading_page spinner spinner-primary mr-3"></div>-->

    <!--    <validation-observer ref="create_sale" v-if="!isLoading">-->
    <!--      <b-form @submit.prevent="Submit_Sale">-->
    <!--        <b-row>-->
    <!--          <b-col lg="12" md="12" sm="12">-->
    <!--            <b-card>-->
    <!--              <b-row>-->
    <!--                &lt;!&ndash; date  &ndash;&gt;-->
    <!--                <b-col lg="4" md="4" sm="12" class="mb-3">-->
    <!--                  <validation-provider-->
    <!--                    name="date"-->
    <!--                    :rules="{ required: true}"-->
    <!--                    v-slot="validationContext"-->
    <!--                  >-->
    <!--                    <b-form-group :label="$t('date') + ' ' + '*'">-->
    <!--                      <b-form-input-->
    <!--                        :state="getValidationState(validationContext)"-->
    <!--                        aria-describedby="date-feedback"-->
    <!--                        type="date"-->
    <!--                        v-model="sale.date"-->
    <!--                      ></b-form-input>-->
    <!--                      <b-form-invalid-feedback-->
    <!--                        id="OrderTax-feedback"-->
    <!--                      >{{ validationContext.errors[0] }}</b-form-invalid-feedback>-->
    <!--                    </b-form-group>-->
    <!--                  </validation-provider>-->
    <!--                </b-col>-->
    <!--                &lt;!&ndash; Customer &ndash;&gt;-->
    <!--                <b-col lg="4" md="4" sm="12" class="mb-3">-->
    <!--                  <validation-provider name="Customer" :rules="{ required: true}">-->
    <!--                    <b-form-group slot-scope="{ valid, errors }" :label="$t('Customer') + ' ' + '*'">-->
    <!--                      <v-select-->
    <!--                        :class="{'is-invalid': !!errors.length}"-->
    <!--                        :state="errors[0] ? false : (valid ? true : null)"-->
    <!--                        v-model="sale.client_id"-->
    <!--                        :reduce="label => label.value"-->
    <!--                        :placeholder="$t('Choose_Customer')"-->
    <!--                        :options="clients.map(clients => ({label: clients.name, value: clients.id}))"-->
    <!--                      />-->
    <!--                      <b-form-invalid-feedback>{{ errors[0] }}</b-form-invalid-feedback>-->
    <!--                    </b-form-group>-->
    <!--                  </validation-provider>-->
    <!--                </b-col>-->

    <!--                &lt;!&ndash; warehouse &ndash;&gt;-->
    <!--                <b-col lg="4" md="4" sm="12" class="mb-3">-->
    <!--                  <validation-provider name="warehouse" :rules="{ required: true}">-->
    <!--                    <b-form-group slot-scope="{ valid, errors }" :label="$t('warehouse') + ' ' + '*'">-->
    <!--                      <v-select-->
    <!--                        :class="{'is-invalid': !!errors.length}"-->
    <!--                        :state="errors[0] ? false : (valid ? true : null)"-->
    <!--                        :disabled="details.length > 0"-->
    <!--                        @input="Selected_Warehouse"-->
    <!--                        v-model="sale.warehouse_id"-->
    <!--                        :reduce="label => label.value"-->
    <!--                        :placeholder="$t('Choose_Warehouse')"-->
    <!--                        :options="warehouses.map(warehouses => ({label: warehouses.name, value: warehouses.id}))"-->
    <!--                      />-->
    <!--                      <b-form-invalid-feedback>{{ errors[0] }}</b-form-invalid-feedback>-->
    <!--                    </b-form-group>-->
    <!--                  </validation-provider>-->
    <!--                </b-col>-->

    <!--                &lt;!&ndash; Product &ndash;&gt;-->
    <!--                <b-col md="12" class="mb-5">-->
    <!--                  <h6>{{$t('ProductName')}}</h6>-->
    <!--                 -->
    <!--                  <div id="autocomplete" class="autocomplete">-->
    <!--                    <input -->
    <!--                     :placeholder="$t('Scan_Search_Product_by_Code_Name')"-->
    <!--                      @input='e => search_input = e.target.value' -->
    <!--                      @keyup="search(search_input)"-->
    <!--                      @focus="handleFocus"-->
    <!--                      @blur="handleBlur"-->
    <!--                      ref="product_autocomplete"-->
    <!--                      class="autocomplete-input" />-->
    <!--                    <ul class="autocomplete-result-list" v-show="focused">-->
    <!--                      <li class="autocomplete-result" v-for="product_fil in product_filter" @mousedown="SearchProduct(product_fil)">{{getResultValue(product_fil)}}</li>-->
    <!--                    </ul>-->
    <!--                </div>-->
    <!--                </b-col>-->

    <!--                &lt;!&ndash; order products  &ndash;&gt;-->
    <!--                <b-col md="12" class="mb-4">-->
    <!--                  <h5>{{$t('order_products')}} *</h5>-->
    <!--                  <div class="table-responsive">-->
    <!--                    <table class="table table-hover">-->
    <!--                      <thead class="bg-gray-300">-->
    <!--                        <tr>-->
    <!--                          <th scope="col">#</th>-->
    <!--                          <th scope="col">{{$t('ProductName')}}</th>-->
    <!--                          <th scope="col">{{$t('Net_Unit_Price')}}</th>-->
    <!--                          <th scope="col">{{$t('CurrentStock')}}</th>-->
    <!--                          <th scope="col">{{$t('Qty')}}</th>-->
    <!--                          <th scope="col">{{$t('Discount')}}</th>-->
    <!--                          <th scope="col">{{$t('Tax')}}</th>-->
    <!--                          <th scope="col">{{$t('SubTotal')}}</th>-->
    <!--                          <th scope="col" class="text-center">-->
    <!--                            <i class="i-Close-Window text-25"></i>-->
    <!--                          </th>-->
    <!--                        </tr>-->
    <!--                      </thead>-->
    <!--                      <tbody>-->
    <!--                        <tr v-if="details.length <=0">-->
    <!--                          <td colspan="9">{{$t('NodataAvailable')}}</td>-->
    <!--                        </tr>-->
    <!--                        <tr v-for="detail  in details" >-->
    <!--                          <td >{{detail.detail_id}}</td>-->
    <!--                          <td>-->
    <!--                            <span>{{detail.code}}</span>-->
    <!--                            <br>-->
    <!--                            <span class="badge badge-success">{{detail.name}}</span>-->
    <!--                           -->
    <!--                          </td>-->
    <!--                          <td>{{currentUser.currency}} {{formatNumber(detail.Net_price, 3)}}</td>-->
    <!--                          <td>-->
    <!--                            <span-->
    <!--                              class="badge badge-outline-warning"-->
    <!--                            >{{detail.stock}} {{detail.unitSale}}</span>-->
    <!--                          </td>-->
    <!--                          <td>-->
    <!--                            <div class="quantity">-->
    <!--                              <b-input-group>-->
    <!--                                <b-input-group-prepend>-->
    <!--                                  <span-->
    <!--                                    class="btn btn-primary btn-sm"-->
    <!--                                    @click="decrement(detail ,detail.detail_id)"-->
    <!--                                  >-</span>-->
    <!--                                </b-input-group-prepend>-->
    <!--                                <input-->
    <!--                                  class="form-control"-->
    <!--                                  @keyup="Verified_Qty(detail,detail.detail_id)"-->
    <!--                                  :min="0.00"-->
    <!--                                  :max="detail.stock"-->
    <!--                                  v-model.number="detail.quantity"-->
    <!--                                >-->
    <!--                                <b-input-group-append>-->
    <!--                                  <span-->
    <!--                                    class="btn btn-primary btn-sm"-->
    <!--                                    @click="increment(detail ,detail.detail_id)"-->
    <!--                                  >+</span>-->
    <!--                                </b-input-group-append>-->
    <!--                              </b-input-group>-->
    <!--                            </div>-->
    <!--                          </td>-->
    <!--                          <td>{{currentUser.currency}} {{formatNumber(detail.DiscountNet * detail.quantity, 2)}}</td>-->
    <!--                          <td>{{currentUser.currency}} {{formatNumber(detail.taxe  * detail.quantity, 2)}}</td>-->
    <!--                          <td>{{currentUser.currency}} {{detail.subtotal.toFixed(2)}}</td>-->
    <!--                          <td>-->
    <!--                            <i @click="Modal_Updat_Detail(detail)" class="i-Edit text-25 text-success"></i>-->
    <!--                            <i @click="delete_Product_Detail(detail.detail_id)" class="i-Close-Window text-25 text-danger"></i>-->
    <!--                          </td>-->
    <!--                        </tr>-->
    <!--                      </tbody>-->
    <!--                    </table>-->
    <!--                  </div>-->
    <!--                </b-col>-->

    <!--                <div class="offset-md-9 col-md-3 mt-4">-->
    <!--                  <table class="table table-striped table-sm">-->
    <!--                    <tbody>-->
    <!--                      <tr>-->
    <!--                        <td class="bold">{{$t('OrderTax')}}</td>-->
    <!--                        <td>-->
    <!--                          <span>{{currentUser.currency}} {{sale.TaxNet.toFixed(2)}} ({{formatNumber(sale.tax_rate,2)}} %)</span>-->
    <!--                        </td>-->
    <!--                      </tr>-->
    <!--                      <tr>-->
    <!--                        <td class="bold">{{$t('Discount')}}</td>-->
    <!--                        <td>{{currentUser.currency}} {{sale.discount.toFixed(2)}}</td>-->
    <!--                      </tr>-->
    <!--                      <tr>-->
    <!--                        <td class="bold">{{$t('Shipping')}}</td>-->
    <!--                        <td>{{currentUser.currency}} {{sale.shipping.toFixed(2)}}</td>-->
    <!--                      </tr>-->
    <!--                      <tr>-->
    <!--                        <td>-->
    <!--                          <span class="font-weight-bold">{{$t('Total')}}</span>-->
    <!--                        </td>-->
    <!--                        <td>-->
    <!--                          <span-->
    <!--                            class="font-weight-bold"-->
    <!--                          >{{currentUser.currency}} {{GrandTotal.toFixed(2)}}</span>-->
    <!--                        </td>-->
    <!--                      </tr>-->
    <!--                    </tbody>-->
    <!--                  </table>-->
    <!--                </div>-->

    <!--                &lt;!&ndash; Order Tax  &ndash;&gt;-->
    <!--                <b-col lg="4" md="4" sm="12" class="mb-3">-->
    <!--                  <validation-provider-->
    <!--                    name="Order Tax"-->
    <!--                    :rules="{ regex: /^\d*\.?\d*$/}"-->
    <!--                    v-slot="validationContext"-->
    <!--                  >-->
    <!--                    <b-form-group :label="$t('OrderTax')">-->
    <!--                      <b-input-group append="%">-->
    <!--                        <b-form-input-->
    <!--                          :state="getValidationState(validationContext)"-->
    <!--                          aria-describedby="OrderTax-feedback"-->
    <!--                          label="Order Tax"-->
    <!--                          v-model.number="sale.tax_rate"-->
    <!--                          @keyup="keyup_OrderTax()"-->
    <!--                        ></b-form-input>-->
    <!--                      </b-input-group>-->
    <!--                      <b-form-invalid-feedback-->
    <!--                        id="OrderTax-feedback"-->
    <!--                      >{{ validationContext.errors[0] }}</b-form-invalid-feedback>-->
    <!--                    </b-form-group>-->
    <!--                  </validation-provider>-->
    <!--                </b-col>-->

    <!--                &lt;!&ndash; Discount &ndash;&gt;-->
    <!--                <b-col lg="4" md="4" sm="12" class="mb-3">-->
    <!--                  <validation-provider-->
    <!--                    name="Discount"-->
    <!--                    :rules="{ regex: /^\d*\.?\d*$/}"-->
    <!--                    v-slot="validationContext"-->
    <!--                  >-->
    <!--                    <b-form-group :label="$t('Discount')">-->
    <!--                      <b-input-group :append="currentUser.currency">-->
    <!--                        <b-form-input-->
    <!--                          :state="getValidationState(validationContext)"-->
    <!--                          aria-describedby="Discount-feedback"-->
    <!--                          label="Discount"-->
    <!--                          v-model.number="sale.discount"-->
    <!--                          @keyup="keyup_Discount()"-->
    <!--                        ></b-form-input>-->
    <!--                      </b-input-group>-->
    <!--                      <b-form-invalid-feedback-->
    <!--                        id="Discount-feedback"-->
    <!--                      >{{ validationContext.errors[0] }}</b-form-invalid-feedback>-->
    <!--                    </b-form-group>-->
    <!--                  </validation-provider>-->
    <!--                </b-col>-->

    <!--                &lt;!&ndash; Shipping  &ndash;&gt;-->
    <!--                <b-col lg="4" md="4" sm="12" class="mb-3">-->
    <!--                  <validation-provider-->
    <!--                    name="Shipping"-->
    <!--                    :rules="{ regex: /^\d*\.?\d*$/}"-->
    <!--                    v-slot="validationContext"-->
    <!--                  >-->
    <!--                    <b-form-group :label="$t('Shipping')">-->
    <!--                      <b-input-group :append="currentUser.currency">-->
    <!--                        <b-form-input-->
    <!--                          :state="getValidationState(validationContext)"-->
    <!--                          aria-describedby="Shipping-feedback"-->
    <!--                          label="Shipping"-->
    <!--                          v-model.number="sale.shipping"-->
    <!--                          @keyup="keyup_Shipping()"-->
    <!--                        ></b-form-input>-->
    <!--                      </b-input-group>-->

    <!--                      <b-form-invalid-feedback-->
    <!--                        id="Shipping-feedback"-->
    <!--                      >{{ validationContext.errors[0] }}</b-form-invalid-feedback>-->
    <!--                    </b-form-group>-->
    <!--                  </validation-provider>-->
    <!--                </b-col>-->

    <!--                &lt;!&ndash; Status  &ndash;&gt;-->
    <!--                <b-col lg="4" md="4" sm="12" class="mb-3">-->
    <!--                  <validation-provider name="Status" :rules="{ required: true}">-->
    <!--                    <b-form-group slot-scope="{ valid, errors }" :label="$t('Status') + ' ' + '*'">-->
    <!--                      <v-select-->
    <!--                        :class="{'is-invalid': !!errors.length}"-->
    <!--                        :state="errors[0] ? false : (valid ? true : null)"-->
    <!--                        v-model="sale.statut"-->
    <!--                        :reduce="label => label.value"-->
    <!--                        :placeholder="$t('Choose_Status')"-->
    <!--                        :options="-->
    <!--                                [-->
    <!--                                  {label: 'completed', value: 'completed'},-->
    <!--                                  {label: 'Pending', value: 'pending'},-->
    <!--                                  {label: 'ordered', value: 'ordered'}-->
    <!--                                ]"-->
    <!--                      ></v-select>-->
    <!--                      <b-form-invalid-feedback>{{ errors[0] }}</b-form-invalid-feedback>-->
    <!--                    </b-form-group>-->
    <!--                  </validation-provider>-->
    <!--                </b-col>-->

    <!--                &lt;!&ndash; PaymentStatus  &ndash;&gt;-->
    <!--                <b-col md="4">-->
    <!--                  <validation-provider name="PaymentStatus">-->
    <!--                    <b-form-group :label="$t('PaymentStatus')">-->
    <!--                      <v-select-->
    <!--                        @input="Selected_PaymentStatus"-->
    <!--                        :reduce="label => label.value"-->
    <!--                        v-model="payment.status"-->
    <!--                        :placeholder="$t('Choose_Status')"-->
    <!--                        :options="-->
    <!--                                [-->
    <!--                                  {label: 'Paid', value: 'paid'},-->
    <!--                                  {label: 'partial', value: 'partial'},-->
    <!--                                  {label: 'Pending', value: 'pending'},-->
    <!--                                ]"-->
    <!--                      ></v-select>-->
    <!--                    </b-form-group>-->
    <!--                  </validation-provider>-->
    <!--                </b-col>-->

    <!--                &lt;!&ndash; Payment choice &ndash;&gt;-->
    <!--                <b-col md="4" v-if="payment.status != 'pending'">-->
    <!--                  <validation-provider name="Payment choice" :rules="{ required: true}">-->
    <!--                    <b-form-group slot-scope="{ valid, errors }" :label="$t('Paymentchoice') + ' ' + '*'">-->
    <!--                      <v-select-->
    <!--                        :class="{'is-invalid': !!errors.length}"-->
    <!--                        :state="errors[0] ? false : (valid ? true : null)"-->
    <!--                        :reduce="label => label.value"-->
    <!--                        @input="Selected_PaymentMethod"-->
    <!--                        v-model="payment.Reglement"-->
    <!--                        :placeholder="$t('PleaseSelect')"-->
    <!--                        :options="-->
    <!--                                  [-->
    <!--                                  {label: 'Cash', value: 'Cash'},-->
    <!--                                  {label: 'credit card', value: 'credit card'},-->
    <!--                                  {label: 'TPE', value: 'tpe'},-->
    <!--                                  {label: 'cheque', value: 'cheque'},-->
    <!--                                  {label: 'Western Union', value: 'Western Union'},-->
    <!--                                  {label: 'bank transfer', value: 'bank transfer'},-->
    <!--                                  {label: 'other', value: 'other'},-->
    <!--                                  ]"-->
    <!--                      ></v-select>-->
    <!--                      <b-form-invalid-feedback>{{ errors[0] }}</b-form-invalid-feedback>-->
    <!--                    </b-form-group>-->
    <!--                  </validation-provider>-->
    <!--                </b-col>-->

    <!--                  &lt;!&ndash; Received  Amount  &ndash;&gt;-->
    <!--                  <b-col md="4" v-if="payment.status != 'pending'">-->
    <!--                      <validation-provider-->
    <!--                        name="Received Amount"-->
    <!--                        :rules="{ required: true , regex: /^\d*\.?\d*$/}"-->
    <!--                        v-slot="validationContext"-->
    <!--                      >-->
    <!--                        <b-form-group :label="$t('Received_Amount') + ' ' + '*'">-->
    <!--                          <b-form-input-->
    <!--                            @keyup="Verified_Received_Amount(payment.received_amount)"-->
    <!--                            label="Received_Amount"-->
    <!--                            :placeholder="$t('Received_Amount')"-->
    <!--                            v-model.number="payment.received_amount"-->
    <!--                            :state="getValidationState(validationContext)"-->
    <!--                            aria-describedby="Received_Amount-feedback"-->
    <!--                          ></b-form-input>-->
    <!--                          <b-form-invalid-feedback-->
    <!--                            id="Received_Amount-feedback"-->
    <!--                          >{{ validationContext.errors[0] }}</b-form-invalid-feedback>-->
    <!--                        </b-form-group>-->
    <!--                      </validation-provider>-->
    <!--                    </b-col>-->

    <!--                &lt;!&ndash; Amount  &ndash;&gt;-->
    <!--                <b-col md="4" v-if="payment.status != 'pending'">-->
    <!--                  <validation-provider-->
    <!--                    name="Amount"-->
    <!--                    :rules="{ required: true , regex: /^\d*\.?\d*$/}"-->
    <!--                    v-slot="validationContext"-->
    <!--                  >-->
    <!--                    <b-form-group :label="$t('Paying_Amount') + ' ' + '*'">-->
    <!--                      <b-form-input-->
    <!--                        :disabled="payment.status == 'paid'"-->
    <!--                        label="Amount"-->
    <!--                        :placeholder="$t('Paying_Amount')"-->
    <!--                        v-model.number="payment.amount"-->
    <!--                        @keyup="Verified_paidAmount(payment.amount)"-->
    <!--                        :state="getValidationState(validationContext)"-->
    <!--                        aria-describedby="Amount-feedback"-->
    <!--                      ></b-form-input>-->
    <!--                      <b-form-invalid-feedback-->
    <!--                        id="Amount-feedback"-->
    <!--                      >{{ validationContext.errors[0] }}</b-form-invalid-feedback>-->
    <!--                    </b-form-group>-->
    <!--                  </validation-provider>-->
    <!--                </b-col>-->

    <!--                &lt;!&ndash; change  Amount  &ndash;&gt;-->
    <!--                <b-col md="4" v-if="payment.status != 'pending'">-->
    <!--                  <label>{{$t('Change')}} :</label>-->
    <!--                  <p-->
    <!--                    class="change_amount"-->
    <!--                  >{{parseFloat(payment.received_amount - payment.amount).toFixed(2)}}</p>-->
    <!--                </b-col>-->

    <!--                 <b-col-->
    <!--                  md="12"-->
    <!--                  class="mt-3"-->
    <!--                  v-if="payment.status != 'pending' && payment.Reglement == 'credit card'"-->
    <!--                >-->
    <!--                  <form id="payment-form">-->
    <!--                    <label-->
    <!--                      for="card-element"-->
    <!--                      class="leading-7 text-sm text-gray-600"-->
    <!--                    >{{$t('Credit_Card_Info')}}</label>-->
    <!--                    <div id="card-element">-->
    <!--                      &lt;!&ndash; Elements will create input elements here &ndash;&gt;-->
    <!--                    </div>-->
    <!--                    &lt;!&ndash; We'll put the error messages in this element &ndash;&gt;-->
    <!--                    <div id="card-errors" role="alert"></div>-->
    <!--                  </form>-->
    <!--                </b-col>-->

    <!--                <b-col md="12" class="mt-3">-->
    <!--                  <b-form-group :label="$t('Note')">-->
    <!--                    <textarea-->
    <!--                      v-model="sale.notes"-->
    <!--                      rows="4"-->
    <!--                      class="form-control"-->
    <!--                      :placeholder="$t('Afewwords')"-->
    <!--                    ></textarea>-->
    <!--                  </b-form-group>-->
    <!--                </b-col>-->

    <!--                <b-col md="12">-->
    <!--                  <b-form-group>-->
    <!--                    <b-button variant="primary" :disabled="paymentProcessing" @click="Submit_Sale">{{$t('submit')}}</b-button>-->
    <!--                    <div v-once class="typo__p" v-if="paymentProcessing">-->
    <!--                    <div class="spinner sm spinner-primary mt-3"></div>-->
    <!--                  </div>-->
    <!--                  </b-form-group>-->
    <!--                </b-col>-->
    <!--              </b-row>-->
    <!--            </b-card>-->
    <!--          </b-col>-->
    <!--        </b-row>-->
    <!--      </b-form>-->
    <!--    </validation-observer>-->

    <!--    &lt;!&ndash; Modal Update detail Product &ndash;&gt;-->
    <!--    <validation-observer ref="Update_Detail">-->
    <!--      <b-modal hide-footer size="lg" id="form_Update_Detail" :title="detail.name">-->
    <!--        <b-form @submit.prevent="submit_Update_Detail">-->
    <!--          <b-row>-->
    <!--            &lt;!&ndash; Unit Price &ndash;&gt;-->
    <!--            <b-col lg="6" md="6" sm="12">-->
    <!--              <validation-provider-->
    <!--                name="Product Price"-->
    <!--                :rules="{ required: true , regex: /^\d*\.?\d*$/}"-->
    <!--                v-slot="validationContext"-->
    <!--              >-->
    <!--                <b-form-group :label="$t('ProductPrice') + ' ' + '*'" id="Price-input">-->
    <!--                  <b-form-input-->
    <!--                    label="Product Price"-->
    <!--                    v-model="detail.Unit_price"-->
    <!--                    :state="getValidationState(validationContext)"-->
    <!--                    aria-describedby="Price-feedback"-->
    <!--                  ></b-form-input>-->
    <!--                  <b-form-invalid-feedback id="Price-feedback">{{ validationContext.errors[0] }}</b-form-invalid-feedback>-->
    <!--                </b-form-group>-->
    <!--              </validation-provider>-->
    <!--            </b-col>-->

    <!--            &lt;!&ndash; Tax Method &ndash;&gt;-->
    <!--            <b-col lg="6" md="6" sm="12">-->
    <!--              <validation-provider name="Tax Method" :rules="{ required: true}">-->
    <!--                <b-form-group slot-scope="{ valid, errors }" :label="$t('TaxMethod') + ' ' + '*'">-->
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
    <!--                  <b-form-invalid-feedback>{{ errors[0] }}</b-form-invalid-feedback>-->
    <!--                </b-form-group>-->
    <!--              </validation-provider>-->
    <!--            </b-col>-->

    <!--            &lt;!&ndash; Tax Rate &ndash;&gt;-->
    <!--            <b-col lg="6" md="6" sm="12">-->
    <!--              <validation-provider-->
    <!--                name="Order Tax"-->
    <!--                :rules="{ required: true , regex: /^\d*\.?\d*$/}"-->
    <!--                v-slot="validationContext"-->
    <!--              >-->
    <!--                <b-form-group :label="$t('OrderTax') + ' ' + '*'">-->
    <!--                  <b-input-group append="%">-->
    <!--                    <b-form-input-->
    <!--                      label="Order Tax"-->
    <!--                      v-model="detail.tax_percent"-->
    <!--                      :state="getValidationState(validationContext)"-->
    <!--                      aria-describedby="OrderTax-feedback"-->
    <!--                    ></b-form-input>-->
    <!--                  </b-input-group>-->
    <!--                  <b-form-invalid-feedback id="OrderTax-feedback">{{ validationContext.errors[0] }}</b-form-invalid-feedback>-->
    <!--                </b-form-group>-->
    <!--              </validation-provider>-->
    <!--            </b-col>-->

    <!--            &lt;!&ndash; Discount Method &ndash;&gt;-->
    <!--             <b-col lg="6" md="6" sm="12">-->
    <!--              <validation-provider name="Discount Method" :rules="{ required: true}">-->
    <!--                <b-form-group slot-scope="{ valid, errors }" :label="$t('Discount_Method') + ' ' + '*'">-->
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
    <!--                  <b-form-invalid-feedback>{{ errors[0] }}</b-form-invalid-feedback>-->
    <!--                </b-form-group>-->
    <!--              </validation-provider>-->
    <!--            </b-col>-->

    <!--            &lt;!&ndash; Discount Rate &ndash;&gt;-->
    <!--           <b-col lg="6" md="6" sm="12">-->
    <!--              <validation-provider-->
    <!--                name="Discount Rate"-->
    <!--                :rules="{ required: true , regex: /^\d*\.?\d*$/}"-->
    <!--                v-slot="validationContext"-->
    <!--              >-->
    <!--                <b-form-group :label="$t('Discount') + ' ' + '*'">-->
    <!--                  <b-form-input-->
    <!--                    label="Discount"-->
    <!--                    v-model.number="detail.discount"-->
    <!--                    :state="getValidationState(validationContext)"-->
    <!--                    aria-describedby="Discount-feedback"-->
    <!--                  ></b-form-input>-->
    <!--                  <b-form-invalid-feedback id="Discount-feedback">{{ validationContext.errors[0] }}</b-form-invalid-feedback>-->
    <!--                </b-form-group>-->
    <!--              </validation-provider>-->
    <!--            </b-col>-->

    <!--            &lt;!&ndash; Unit Sale &ndash;&gt;-->
    <!--            <b-col lg="6" md="6" sm="12">-->
    <!--              <validation-provider name="Unit Sale" :rules="{ required: true}">-->
    <!--                <b-form-group slot-scope="{ valid, errors }" :label="$t('UnitSale') + ' ' + '*'">-->
    <!--                  <v-select-->
    <!--                    :class="{'is-invalid': !!errors.length}"-->
    <!--                    :state="errors[0] ? false : (valid ? true : null)"-->
    <!--                    v-model="detail.sale_unit_id"-->
    <!--                    :placeholder="$t('Choose_Unit_Sale')"-->
    <!--                    :reduce="label => label.value"-->
    <!--                    :options="units.map(units => ({label: units.name, value: units.id}))"-->
    <!--                  />-->
    <!--                  <b-form-invalid-feedback>{{ errors[0] }}</b-form-invalid-feedback>-->
    <!--                </b-form-group>-->
    <!--              </validation-provider>-->
    <!--            </b-col>-->

    <!--             &lt;!&ndash; Imei or serial numbers &ndash;&gt;-->
    <!--              <b-col lg="12" md="12" sm="12" v-show="detail.is_imei">-->
    <!--                <b-form-group :label="$t('Add_product_IMEI_Serial_number')">-->
    <!--                  <b-form-input-->
    <!--                    label="Add_product_IMEI_Serial_number"-->
    <!--                    v-model="detail.imei_number"-->
    <!--                    :placeholder="$t('Add_product_IMEI_Serial_number')"-->
    <!--                  ></b-form-input>-->
    <!--                </b-form-group>-->
    <!--            </b-col>-->

    <!--            <b-col md="12">-->
    <!--              <b-form-group>-->
    <!--                <b-button-->
    <!--                  variant="primary"-->
    <!--                  type="submit"-->
    <!--                  :disabled="Submit_Processing_detail"-->
    <!--                >{{$t('submit')}}</b-button>-->
    <!--                <div v-once class="typo__p" v-if="Submit_Processing_detail">-->
    <!--                  <div class="spinner sm spinner-primary mt-3"></div>-->
    <!--                </div>-->
    <!--              </b-form-group>-->
    <!--            </b-col>-->
    <!--          </b-row>-->
    <!--        </b-form>-->
    <!--      </b-modal>-->
    <!--    </validation-observer>-->
    <!--  </div>-->
</template>
