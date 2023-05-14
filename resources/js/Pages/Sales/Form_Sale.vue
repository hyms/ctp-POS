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
const saleLabel = ref({
    date: "Fecha",
    statut: "Estado",
    notes: "Notas",
    client_id: "Cliente",
    warehouse_id: "Agencia",
    shipping: "Comprando",
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
        .catch((error) => {});
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
                ruleForm.formatNumber(detailsForm.value[i].quantity++, 2);
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
                    ruleForm.formatNumber(detailsForm.value[i].quantity--, 2);
                }
            }
        }
    }
    Calcul_Total();
}

//-----------------------------------------Calcul Total ------------------------------\\
function Calcul_Total() {
    total.value = 0;
    console.log(detailsForm.value);
    for (let i = 0; i < detailsForm.value.length; i++) {
        detailsForm.value[i].subtotal = parseFloat(
            detailsForm.value[i].quantity * detailsForm.value[i].Net_price
        );
        total.value = parseFloat(total.value + detailsForm.value[i].subtotal);
    }
    const total_without_discount = parseFloat(
        total.value - saleForm.value.discount
    );

    GrandTotal.value = parseFloat(
        total_without_discount + saleForm.value.shipping
    );

    let grand_total = GrandTotal.value.toFixed(2);
    GrandTotal.value = parseFloat(grand_total);

    if (payment.value.status == "paid") {
        payment.value.amount = ruleForm.formatNumber(GrandTotal.value, 2);
    }
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
    let len = detailsForm.value.length;
    product.value.detail_id = detailsForm.value[len - 1].detail_id + 1;
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
            <v-row>
                <v-col cols="12">
                    <v-card>
                        <v-card-text>
                            <v-row>
                                <!-- date-->
                                <v-col lg="4" md="4" cols="12" class="mv-3">
                                    <v-text-field
                                        v-model="saleForm.date"
                                        :label="saleLabel.date"
                                        variant="outlined"
                                        density="comfortable"
                                        hide-details="auto"
                                        type="date"
                                        :rules="ruleForm.required"
                                    >
                                    </v-text-field>
                                </v-col>
                                <!-- Customer -->
                                <v-col lg="4" md="4" cols="12" class="mv-3">
                                    <v-autocomplete
                                        v-model="saleForm.client_id"
                                        :items="clients"
                                        :label="saleLabel.client_id"
                                        item-title="company_name"
                                        item-value="id"
                                        variant="outlined"
                                        density="comfortable"
                                        hide-details="auto"
                                        clearable
                                        :rules="ruleForm.required"
                                    ></v-autocomplete>
                                </v-col>

                                <!-- warehouse -->
                                <v-col lg="4" md="4" cols="12" class="mv-3">
                                    <v-select
                                        @update:modelValue="Selected_Warehouse"
                                        v-model="saleForm.warehouse_id"
                                        :items="warehouses"
                                        :label="saleLabel.warehouse_id"
                                        item-title="name"
                                        item-value="id"
                                        variant="outlined"
                                        density="comfortable"
                                        hide-details="auto"
                                        clearable
                                        :rules="ruleForm.required"
                                    ></v-select>
                                </v-col>

                                <!-- Product -->
                                <v-col cols="12">
                                    <v-autocomplete
                                        @update:modelValue="getResultValue"
                                        :loading="loadingFilter"
                                        :items="products"
                                        :model-value="search_input"
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
                                                        >{{
                                                            detail.code
                                                        }}</v-chip
                                                    >

                                                    {{ detail.name }}
                                                </td>
                                                <td>
                                                    <v-text-field
                                                        variant="outlined"
                                                        density="compact"
                                                        hide-details="auto"
                                                        :rules="ruleForm.number"
                                                        v-model="
                                                            detail.Net_price
                                                        "
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
                                                            :rules="
                                                                ruleForm.number
                                                            "
                                                            v-model="
                                                                detail.quantity
                                                            "
                                                            @keyup="
                                                                Verified_Qty(
                                                                    detail,
                                                                    detail.detail_id
                                                                )
                                                            "
                                                            :min="0.0"
                                                            :max="
                                                                detail.current
                                                            "
                                                        >
                                                            <template
                                                                v-slot:append
                                                            >
                                                                <v-icon
                                                                    color="secundary"
                                                                    @click="
                                                                        increment(
                                                                            detail,
                                                                            detail.detail_id
                                                                        )
                                                                    "
                                                                >
                                                                    mdi-plus-box
                                                                </v-icon>
                                                            </template>
                                                            <template
                                                                v-slot:prepend
                                                            >
                                                                <v-icon
                                                                    color="secundary"
                                                                    @click="
                                                                        decrement(
                                                                            detail,
                                                                            detail.detail_id
                                                                        )
                                                                    "
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
                                                        ruleForm.formatNumber(
                                                            detail.DiscountNet *
                                                                detail.quantity,
                                                            2
                                                        )
                                                    }}
                                                </td>
                                                <td>
                                                    Bs
                                                    {{
                                                        detail.subtotal.toFixed(
                                                            2
                                                        )
                                                    }}
                                                </td>
                                                <td>
                                                    <i
                                                        @click="
                                                            Modal_Updat_Detail(
                                                                detail
                                                            )
                                                        "
                                                        class="i-Edit text-25 text-success"
                                                    ></i>
                                                    <i
                                                        @click="
                                                            delete_Product_Detail(
                                                                detail.detail_id
                                                            )
                                                        "
                                                        class="i-Close-Window text-25 text-danger"
                                                    ></i>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </v-table>
                                </v-col>

                                <!--                <div class="offset-md-9 col-md-3 mt-4">-->
                                <!--                  <table class="table table-striped table-sm">-->
                                <!--                    <tbody>-->
                                <!--                      <tr>-->
                                <!--                        <td class="bold">{{$t('OrderTax')}}</td>-->
                                <!--                        <td>-->
                                <!--                          <span>Bs {{sale.TaxNet.toFixed(2)}} ({{formatNumber(sale.tax_rate,2)}} %)</span>-->
                                <!--                        </td>-->
                                <!--                      </tr>-->
                                <!--                      <tr>-->
                                <!--                        <td class="bold">{{$t('Discount')}}</td>-->
                                <!--                        <td>Bs {{sale.discount.toFixed(2)}}</td>-->
                                <!--                      </tr>-->
                                <!--                      <tr>-->
                                <!--                        <td class="bold">{{$t('Shipping')}}</td>-->
                                <!--                        <td>Bs {{sale.shipping.toFixed(2)}}</td>-->
                                <!--                      </tr>-->
                                <!--                      <tr>-->
                                <!--                        <td>-->
                                <!--                          <span class="font-weight-bold">{{$t('Total')}}</span>-->
                                <!--                        </td>-->
                                <!--                        <td>-->
                                <!--                          <span-->
                                <!--                            class="font-weight-bold"-->
                                <!--                          >Bs {{GrandTotal.toFixed(2)}}</span>-->
                                <!--                        </td>-->
                                <!--                      </tr>-->
                                <!--                    </tbody>-->
                                <!--                  </table>-->
                                <!--                </div>-->

                                <!--                &lt;!&ndash; Order Tax  &ndash;&gt;-->
                                <!--                <v-col lg="4" md="4" sm="12" class="mv-3">-->
                                <!--                  <validation-provider-->
                                <!--                    name="Order Tax"-->
                                <!--                    :rules="{ regex: /^\d*\.?\d*$/}"-->
                                <!--                    v-slot="validationContext"-->
                                <!--                  >-->
                                <!--                    <v-form-group :label="$t('OrderTax')">-->
                                <!--                      <v-input-group append="%">-->
                                <!--                        <v-form-input-->
                                <!--                          :state="getValidationState(validationContext)"-->
                                <!--                          aria-describedby="OrderTax-feedback"-->
                                <!--                          label="Order Tax"-->
                                <!--                          v-model.number="sale.tax_rate"-->
                                <!--                          @keyup="keyup_OrderTax()"-->
                                <!--                        ></v-form-input>-->
                                <!--                      </v-input-group>-->
                                <!--                      <v-form-invalid-feedback-->
                                <!--                        id="OrderTax-feedback"-->
                                <!--                      >{{ validationContext.errors[0] }}</v-form-invalid-feedback>-->
                                <!--                    </v-form-group>-->
                                <!--                  </validation-provider>-->
                                <!--                </v-col>-->

                                <!--                &lt;!&ndash; Discount &ndash;&gt;-->
                                <!--                <v-col lg="4" md="4" sm="12" class="mv-3">-->
                                <!--                  <validation-provider-->
                                <!--                    name="Discount"-->
                                <!--                    :rules="{ regex: /^\d*\.?\d*$/}"-->
                                <!--                    v-slot="validationContext"-->
                                <!--                  >-->
                                <!--                    <v-form-group :label="$t('Discount')">-->
                                <!--                      <v-input-group :append="currentUser.currency">-->
                                <!--                        <v-form-input-->
                                <!--                          :state="getValidationState(validationContext)"-->
                                <!--                          aria-describedby="Discount-feedback"-->
                                <!--                          label="Discount"-->
                                <!--                          v-model.number="sale.discount"-->
                                <!--                          @keyup="keyup_Discount()"-->
                                <!--                        ></v-form-input>-->
                                <!--                      </v-input-group>-->
                                <!--                      <v-form-invalid-feedback-->
                                <!--                        id="Discount-feedback"-->
                                <!--                      >{{ validationContext.errors[0] }}</v-form-invalid-feedback>-->
                                <!--                    </v-form-group>-->
                                <!--                  </validation-provider>-->
                                <!--                </v-col>-->

                                <!--                &lt;!&ndash; Shipping  &ndash;&gt;-->
                                <!--                <v-col lg="4" md="4" sm="12" class="mv-3">-->
                                <!--                  <validation-provider-->
                                <!--                    name="Shipping"-->
                                <!--                    :rules="{ regex: /^\d*\.?\d*$/}"-->
                                <!--                    v-slot="validationContext"-->
                                <!--                  >-->
                                <!--                    <v-form-group :label="$t('Shipping')">-->
                                <!--                      <v-input-group :append="currentUser.currency">-->
                                <!--                        <v-form-input-->
                                <!--                          :state="getValidationState(validationContext)"-->
                                <!--                          aria-describedby="Shipping-feedback"-->
                                <!--                          label="Shipping"-->
                                <!--                          v-model.number="sale.shipping"-->
                                <!--                          @keyup="keyup_Shipping()"-->
                                <!--                        ></v-form-input>-->
                                <!--                      </v-input-group>-->

                                <!--                      <v-form-invalid-feedback-->
                                <!--                        id="Shipping-feedback"-->
                                <!--                      >{{ validationContext.errors[0] }}</v-form-invalid-feedback>-->
                                <!--                    </v-form-group>-->
                                <!--                  </validation-provider>-->
                                <!--                </v-col>-->

                                <!--                &lt;!&ndash; Status  &ndash;&gt;-->
                                <!--                <v-col lg="4" md="4" sm="12" class="mv-3">-->
                                <!--                  <validation-provider name="Status" :rules="{ required: true}">-->
                                <!--                    <v-form-group slot-scope="{ valid, errors }" :label="$t('Status') + ' ' + '*'">-->
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
                                <!--                      <v-form-invalid-feedback>{{ errors[0] }}</v-form-invalid-feedback>-->
                                <!--                    </v-form-group>-->
                                <!--                  </validation-provider>-->
                                <!--                </v-col>-->

                                <!--                &lt;!&ndash; PaymentStatus  &ndash;&gt;-->
                                <!--                <v-col md="4">-->
                                <!--                  <validation-provider name="PaymentStatus">-->
                                <!--                    <v-form-group :label="$t('PaymentStatus')">-->
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
                                <!--                    </v-form-group>-->
                                <!--                  </validation-provider>-->
                                <!--                </v-col>-->

                                <!--                &lt;!&ndash; Payment choice &ndash;&gt;-->
                                <!--                <v-col md="4" v-if="payment.status != 'pending'">-->
                                <!--                  <validation-provider name="Payment choice" :rules="{ required: true}">-->
                                <!--                    <v-form-group slot-scope="{ valid, errors }" :label="$t('Paymentchoice') + ' ' + '*'">-->
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
                                <!--                      <v-form-invalid-feedback>{{ errors[0] }}</v-form-invalid-feedback>-->
                                <!--                    </v-form-group>-->
                                <!--                  </validation-provider>-->
                                <!--                </v-col>-->

                                <!--                  &lt;!&ndash; Received  Amount  &ndash;&gt;-->
                                <!--                  <v-col md="4" v-if="payment.status != 'pending'">-->
                                <!--                      <validation-provider-->
                                <!--                        name="Received Amount"-->
                                <!--                        :rules="{ required: true , regex: /^\d*\.?\d*$/}"-->
                                <!--                        v-slot="validationContext"-->
                                <!--                      >-->
                                <!--                        <v-form-group :label="$t('Received_Amount') + ' ' + '*'">-->
                                <!--                          <v-form-input-->
                                <!--                            @keyup="Verified_Received_Amount(payment.received_amount)"-->
                                <!--                            label="Received_Amount"-->
                                <!--                            :placeholder="$t('Received_Amount')"-->
                                <!--                            v-model.number="payment.received_amount"-->
                                <!--                            :state="getValidationState(validationContext)"-->
                                <!--                            aria-describedby="Received_Amount-feedback"-->
                                <!--                          ></v-form-input>-->
                                <!--                          <v-form-invalid-feedback-->
                                <!--                            id="Received_Amount-feedback"-->
                                <!--                          >{{ validationContext.errors[0] }}</v-form-invalid-feedback>-->
                                <!--                        </v-form-group>-->
                                <!--                      </validation-provider>-->
                                <!--                    </v-col>-->

                                <!--                &lt;!&ndash; Amount  &ndash;&gt;-->
                                <!--                <v-col md="4" v-if="payment.status != 'pending'">-->
                                <!--                  <validation-provider-->
                                <!--                    name="Amount"-->
                                <!--                    :rules="{ required: true , regex: /^\d*\.?\d*$/}"-->
                                <!--                    v-slot="validationContext"-->
                                <!--                  >-->
                                <!--                    <v-form-group :label="$t('Paying_Amount') + ' ' + '*'">-->
                                <!--                      <v-form-input-->
                                <!--                        :disabled="payment.status == 'paid'"-->
                                <!--                        label="Amount"-->
                                <!--                        :placeholder="$t('Paying_Amount')"-->
                                <!--                        v-model.number="payment.amount"-->
                                <!--                        @keyup="Verified_paidAmount(payment.amount)"-->
                                <!--                        :state="getValidationState(validationContext)"-->
                                <!--                        aria-describedby="Amount-feedback"-->
                                <!--                      ></v-form-input>-->
                                <!--                      <v-form-invalid-feedback-->
                                <!--                        id="Amount-feedback"-->
                                <!--                      >{{ validationContext.errors[0] }}</v-form-invalid-feedback>-->
                                <!--                    </v-form-group>-->
                                <!--                  </validation-provider>-->
                                <!--                </v-col>-->

                                <!--                &lt;!&ndash; change  Amount  &ndash;&gt;-->
                                <!--                <v-col md="4" v-if="payment.status != 'pending'">-->
                                <!--                  <label>{{$t('Change')}} :</label>-->
                                <!--                  <p-->
                                <!--                    class="change_amount"-->
                                <!--                  >{{parseFloat(payment.received_amount - payment.amount).toFixed(2)}}</p>-->
                                <!--                </v-col>-->

                                <!--                 <v-col-->
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
                                <!--                </v-col>-->

                                <!--                <v-col md="12" class="mt-3">-->
                                <!--                  <v-form-group :label="$t('Note')">-->
                                <!--                    <textarea-->
                                <!--                      v-model="sale.notes"-->
                                <!--                      rows="4"-->
                                <!--                      class="form-control"-->
                                <!--                      :placeholder="$t('Afewwords')"-->
                                <!--                    ></textarea>-->
                                <!--                  </v-form-group>-->
                                <!--                </v-col>-->

                                <!--                <v-col md="12">-->
                                <!--                  <v-form-group>-->
                                <!--                    <v-button variant="primary" :disabled="paymentProcessing" @click="Submit_Sale">{{$t('submit')}}</v-button>-->
                                <!--                    <div v-once class="typo__p" v-if="paymentProcessing">-->
                                <!--                    <div class="spinner sm spinner-primary mt-3"></div>-->
                                <!--                  </div>-->
                                <!--                  </v-form-group>-->
                                <!--                </v-col>-->
                            </v-row>
                        </v-card-text>
                    </v-card>
                </v-col>
            </v-row>
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
