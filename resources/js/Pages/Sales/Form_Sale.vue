<script setup>
import { onMounted, ref, watch } from "vue";
import Layout from "@/Layouts/Authenticated.vue";
import { router } from "@inertiajs/vue3";
import { api, globals, helpers, rules } from "@/helpers";
import Snackbar from "@/Components/snackbar.vue";
import SelectClient from "@/Components/select_client.vue";

const props = defineProps({
    clients: Object,
    warehouses: Object,
    sales_types: Object,
    sale: { type: Object, default: null },
    details: { type: Object, default: null },
    errors: Object,
});
const currency = globals.currency();
const editmode = ref(false);
const form = ref(null);
const loading = ref(false);
const loadingFilter = ref(false);
const loadingProduct = ref(false);
const snackbar = ref({
    view: false,
    color: "",
    text: "",
});
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
        payment.value.amount = helpers.formatNumber(payment_amount, 2);
        payment.value.received_amount = helpers.formatNumber(payment_amount, 2);
    } else {
        payment.value.amount = 0;
        payment.value.received_amount = 0;
    }
}

//---------- keyup paid Amount

function Verified_paidAmount() {
    snackbar.value.view = false;
    if (isNaN(payment.value.amount)) {
        payment.value.amount = 0;
    } else if (payment.value.amount > payment.value.received_amount) {
        snackbar.value.view = true;
        snackbar.value.text = "Pago es mayor al monto recibido";
        snackbar.value.color = "warning";
        payment.value.amount = 0;
    } else if (payment.value.amount > GrandTotal.value) {
        snackbar.value.view = true;
        snackbar.value.text = "Pago es mayor al monto total";
        snackbar.value.color = "warning";
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
    snackbar.value.view = false;
    const validate = await form.value.validate();
    if (!validate.valid) {
        snackbar.value.view = true;
        snackbar.value.text = "llene el formulario correctamente";
        snackbar.value.color = "error";
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
    api.get({
        url: "/get_units?id=" + value,
        loadingItem: loading,
        snackbar,
        onSuccess: (data) => {
            units.value = data;
        },
    });
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
    snackbar.value.view = false;
    product.value = {};
    if (
        detailsForm.value.length > 0 &&
        detailsForm.value.some((detail) => detail.code === result.code)
    ) {
        snackbar.value.view = true;
        snackbar.value.text = "Ya esta añadido";
        snackbar.value.color = "warning";
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
    api.get({
        url: "/get_Products_by_warehouse/" + id + "?stock=" + 0,
        loadingItem: loadingFilter,
        snackbar,
        onSuccess: (data) => {
            products.value = data;
        },
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
                snackbar.value.view = true;
                snackbar.value.text = "bajo stock";
                snackbar.value.color = "warning";
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
                snackbar.value.view = true;
                snackbar.value.text = "bajo stock";
                snackbar.value.color = "warning";
            } else {
                helpers.formatNumber(detailsForm.value[i].quantity++, 2);
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
                    snackbar.value.view = true;
                    snackbar.value.text = "bajo stock";
                    snackbar.value.color = "warning";
                } else {
                    helpers.formatNumber(detailsForm.value[i].quantity--, 2);
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
    // payment.value.received_amount = helpers.formatNumber(GrandTotal.value, 2);
    payment.value.amount = helpers.formatNumber(GrandTotal.value, 2);
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
        snackbar.value.view = true;
        snackbar.value.text = "debes añadir un producto";
        snackbar.value.color = "warning";
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
            snackbar.value.view = true;
            snackbar.value.text = "Debes añadir cantidades";
            snackbar.value.color = "warning";
            return false;
        } else {
            return true;
        }
    }
}

//--------------------------------- Create Sale -------------------------\\
function Create_Sale() {
    if (verifiedForm()) {
        api.post({
            url: "/sales",
            params: {
                sales_type: saleForm.value.sales_type_id,
                date: saleForm.value.date,
                client_id: saleForm.value.client_id,
                warehouse_id: saleForm.value.warehouse_id,
                statut: saleForm.value.statut,
                notes: saleForm.value.notes,
                tax_rate: saleForm.value.tax_rate ?? 0,
                TaxNet: saleForm.value.TaxNet ?? 0,
                discount: saleForm.value.discount ?? 0,
                shipping: saleForm.value.shipping ?? 0,
                GrandTotal: GrandTotal.value,
                details: detailsForm.value,
                payment: payment.value,
                amount: parseFloat(payment.value.amount).toFixed(2),
                received_amount: parseFloat(
                    // payment.value.received_amount
                    "0"
                ).toFixed(2),
                change: parseFloat(
                    "0"
                    // GrandTotal.value - payment.value.amount
                ).toFixed(2),
            },
            loadingItem: loading,
            snackbar,
            onSuccess: () => {
                snackbar.value.text = "compra exitosa";
                router.visit("/sales");
            },
            onError: () => {
                snackbar.value.text = "No se pudo procesar el pago";
            },
        });
    }
}

//--------------------------------- Update Sale -------------------------\\
function Update_Sale() {
    if (verifiedForm()) {
        let id = props.sale.id;
        api.put({
            url: `/sales/${id}`,
            params: {
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
            },
            loadingItem: loading,
            snackbar,
            onSuccess: () => {
                snackbar.value.text = "compra exitosa";
                router.visit("/sales");
            },
            onError: () => {
                snackbar.value.text = "No se pudo procesar el pago";
            },
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
    snackbar.value.text = "";
    api.get({
        url: "/products/item/" + product_id,
        snackbar,
        loadingItem: loadingProduct,
        onSuccess: (data) => {
            product.value.discount = 0;
            product.value.DiscountNet = 0;
            product.value.discount_Method = "2";
            product.value.product_id = data.id;
            product.value.name = data.name;
            product.value.Net_price = data.Net_price;
            product.value.Unit_price = data.Unit_price;
            product.value.tax_method = data.tax_method;
            product.value.tax_percent = data.tax_percent;
            product.value.unitSale = data.unitSale;
            product.value.fix_price = data.fix_price;
            product.value.sale_unit_id = data.sale_unit_id;
            product.value.imei_number = "";
            add_product();
            Calcul_Total();
        },
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
        parseFloat(payment.value.amount, 2) >= parseFloat(GrandTotal.value, 2)
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
    <Layout>
        <snackbar
            v-model="snackbar.view"
            :text="snackbar.text"
            :color="snackbar.color"
        ></snackbar>
        <v-form ref="form" :disabled="loading">
            <v-card :loading="loading">
                <v-toolbar height="15"></v-toolbar>
                <v-card-text>
                    <v-row>
                        <!-- date-->
                        <v-col lg="4" md="4" cols="12">
                            <v-text-field
                                v-model="saleForm.date"
                                :label="saleLabel.date"
                                hide-details="auto"
                                type="date"
                                :rules="rules.required"
                            >
                            </v-text-field>
                        </v-col>
                        <!-- Customer -->
                        <v-col lg="4" md="4" cols="12">
                            <SelectClient
                                v-model="saleForm.client_id"
                                :enableForm="false"
                            ></SelectClient>
                        </v-col>

                        <!-- warehouse -->
                        <v-col lg="4" md="4" cols="12">
                            <v-select
                                @update:modelValue="Selected_Warehouse"
                                v-model="saleForm.warehouse_id"
                                :items="warehouses"
                                :label="saleLabel.warehouse_id"
                                hide-details="auto"
                                clearable
                                :rules="rules.required"
                                :disabled="detailsForm.length > 0"
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
                                hide-no-data
                                hide-details
                                label="Añadir Producto"
                                :disabled="products.length == 0"
                                clearable
                                prepend-inner-icon="fas fa-search"
                            ></v-autocomplete>
                        </v-col>

                        <!-- products  -->
                        <v-col cols="12">
                            <p class="text-h6">Productos *</p>
                            <v-card :loading="loadingProduct" color="primary">
                                <v-table hover>
                                    <thead>
                                        <tr class="bg-secondary">
                                            <th class="text-white text-center">
                                                #
                                            </th>
                                            <th class="text-white text-center">
                                                Producto
                                            </th>
                                            <th class="text-white text-center">
                                                Precio x Unidad
                                            </th>
                                            <th class="text-white text-center">
                                                En Stock
                                            </th>
                                            <th class="text-white text-center">
                                                Cantidad
                                            </th>
                                            <th class="text-white text-center">
                                                Descuento
                                            </th>
                                            <th class="text-white text-center">
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
                                                    class="my-1"
                                                    hide-details="auto"
                                                    :rules="rules.number"
                                                    v-model="detail.Net_price"
                                                    @keyup="Calcul_Total"
                                                >
                                                    <template v-slot:prepend>
                                                        {{ currency }}
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
                                                        class="my-1"
                                                        hide-details="auto"
                                                        :rules="rules.number"
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
                                                        :max="detail.current"
                                                    >
                                                        <template v-slot:append>
                                                            <v-icon
                                                                color="secundary"
                                                                @click="
                                                                    increment(
                                                                        detail,
                                                                        detail.detail_id
                                                                    )
                                                                "
                                                            >
                                                                fas
                                                                fa-plus-square
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
                                                                fas
                                                                fa-minus-square
                                                            </v-icon>
                                                        </template>
                                                    </v-text-field>
                                                </div>
                                            </td>
                                            <td>
                                                {{ currency }}
                                                {{
                                                    helpers.formatNumber(
                                                        detail.DiscountNet *
                                                            detail.quantity,
                                                        2
                                                    )
                                                }}
                                            </td>
                                            <td>
                                                {{ currency }}
                                                {{ detail.subtotal.toFixed(2) }}
                                            </td>
                                            <td>
                                                <!--                    <v-btn-->
                                                <!--                        class="ma-1 rounded"-->
                                                <!--                        color="success"-->
                                                <!--                        icon="mdi-pen"-->
                                                <!--                        size="small"-->
                                                <!--                        density="comfortable"-->
                                                <!--                        variant="elevated"-->
                                                <!--                        @click="Modal_Updat_Detail(detail)"-->
                                                <!--                    >-->
                                                <!--                    </v-btn>-->
                                                <v-btn
                                                    class="ma-1 rounded"
                                                    color="error"
                                                    icon="fas fa-trash"
                                                    size="small"
                                                    density="comfortable"
                                                    variant="elevated"
                                                    @click="
                                                        delete_Product_Detail(
                                                            detail.detail_id
                                                        )
                                                    "
                                                >
                                                </v-btn>
                                            </td>
                                        </tr>
                                    </tbody>
                                </v-table>
                            </v-card>
                        </v-col>
                        <v-col
                            cols="12"
                            md="4"
                            offset-md="8"
                            sm="6"
                            offset-sm="6"
                        >
                            <v-table hover class="border">
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
                                        <td class="font-weight-bold">Total</td>
                                        <td class="font-weight-bold">
                                            {{ currency }}
                                            {{ GrandTotal.toFixed(2) }}
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
                                hide-details="auto"
                                :rules="rules.number"
                                v-model="saleForm.discount"
                                @keyup="keyup_Discount()"
                            ></v-text-field>
                        </v-col>
                        <!-- Amount  -->
                        <v-col lg="4" md="4" cols="12" v-if="editmode == false">
                            <v-text-field
                                v-model="payment.amount"
                                hide-details="auto"
                                type="text"
                                label="A pagar"
                                :rules="rules.numberWithDecimal"
                                @keyup="change_payment_status()"
                            ></v-text-field>
                        </v-col>
                        <!-- Received  Amount  -->
                        <!--            <v-col lg="4" md="4" cols="12" v-if="editmode==false">-->
                        <!--              <v-text-field-->
                        <!--                  v-model="payment.received_amount"-->
                        <!--                  hide-details="auto"-->
                        <!--                  type="text"-->
                        <!--                  label="Monto Recibido"-->
                        <!--                  readonly-->
                        <!--                  :rules="rules.numberWithDecimal"-->
                        <!--                  @keyup="change_payment_status()"-->
                        <!--              ></v-text-field>-->
                        <!--            </v-col>-->

                        <!-- Status  -->
                        <v-col lg="4" md="4" cols="12">
                            <v-select
                                v-model="saleForm.statut"
                                variant="outlined"
                                clearable
                                hide-details="auto"
                                :items="helpers.statutSale()"
                                :label="saleLabel.statut"
                            ></v-select>
                        </v-col>

                        <!-- Sales type  -->
                        <v-col lg="4" md="4" cols="12">
                            <v-select
                                v-model="saleForm.sales_type_id"
                                variant="outlined"
                                hide-details="auto"
                                :items="sales_types"
                                :label="saleLabel.sales_type_id + ' *'"
                                :rules="rules.required"
                            ></v-select>
                        </v-col>

                        <!-- Payment choice -->
                        <v-col lg="4" md="4" cols="12">
                            <v-select
                                v-model="payment.Reglement"
                                variant="outlined"
                                hide-details="auto"
                                :items="helpers.reglamentPayment()"
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
                            </v-btn>
                        </v-col>
                    </v-row>
                </v-card-text>
            </v-card>
        </v-form>
    </Layout>
</template>
