<script setup>
import { computed, onMounted, ref, watch } from "vue";
import Layout from "@/Layouts/Authenticated.vue";
import { router, usePage } from "@inertiajs/vue3";
import helper from "@/helpers";
import labels from "@/labels";
import api from "@/api";
import DeleteBtn from "@/Components/buttons/DeleteBtn.vue";

const props = defineProps({
    details: Object,
    transfer: Object,
    warehouses: Object,
    errors: Object,
});
const currency = computed(() => usePage().props.currency);

const form = ref(null);
const loading = ref(false);
const loadingFilter = ref(false);
const dialogUpdateDetail = ref(false);
const snackbar = ref({
    view: false,
    color: "",
    text: "",
});
const search_input = ref("");
const products = ref([]);
const units = ref([]);
const total = ref(0);
const GrandTotal = ref(0);
const detailsForm = ref([]);
const detail = ref({
    quantity: "",
    discount: "",
    Unit_cost: "",
    discount_Method: "",
    tax_percent: "",
    tax_method: "",
});
const transferForm = ref({
    id: "",
    from_warehouse: "",
    to_warehouse: "",
    statut: "completed",
    notes: "",
    date: new Date().toISOString().slice(0, 10),
    items: 0,
    tax_rate: 0,
    TaxNet: 0,
    shipping: 0,
    discount: 0,
});

const product = ref({
    id: "",
    code: "",
    stock: "",
    quantity: 1,
    discount: "",
    DiscountNet: "",
    discount_Method: "",
    name: "",
    unitPurchase: "",
    purchase_unit_id: "",
    fix_stock: "",
    fix_cost: "",
    Net_cost: "",
    Unit_cost: "",
    Total_cost: "",
    subtotal: "",
    product_id: "",
    detail_id: "",
    taxe: "",
    tax_percent: "",
    tax_method: "",
    product_variant_id: "",
});
const editmode = ref(false);

function resetForm() {
    transferForm.value = {
        id: "",
        notes: "",
        warehouse_id: "",
        date: new Date().toISOString().slice(0, 10),
    };
    detailsForm.value = [];
    products.value = [];
}

function querySelections(val) {
    search_input.value = val;
    loadingFilter.value = true;

    if (
        transferForm.value.from_warehouse !== "" &&
        transferForm.value.from_warehouse != null
    ) {
        let product_filter = [];
        for (let item of products.value) {
            if (search_input.value === item.id) {
                product_filter = item;
                break;
            }
        }
        if (Object.keys(product_filter).length > 0) {
            // console.log(product_filter)
            SearchProduct(product_filter);
        }
    }
    loadingFilter.value = false;
}

//---------------- Submit Search Product-----------------\\
function SearchProduct(result) {
    snackbar.value.view = false;
    product.value = {};
    if (
        detailsForm.value.length > 0 &&
        detailsForm.value.some((detail) => detail.name === result.name)
    ) {
        snackbar.value.view = true;
        snackbar.value.text = "Ya esta añadido";
        snackbar.value.color = "warning";
    } else {
        product.value.code = result.code;
        product.value.stock = result.qte_purchase;
        product.value.fix_stock = result.qte;
        if (result.qte_purchase < 1) {
            product.value.quantity = result.qte_purchase;
        } else {
            product.value.quantity = 1;
        }
        product.value.product_variant_id = result.product_variant_id;
        Get_Product_Details(search_input.value);
    }
    search_input.value = "";
}

//------------- Submit Validation Create Transfer
async function Submit_Transfer() {
    const validate = await form.value.validate();
    if (validate.valid)
        if (editmode.value) {
            Update_Transfer();
        } else {
            Create_Transfer();
        }
}

//---------------------- get_units ------------------------------\\
function get_units(value) {
    axios.get("/get_units?id=" + value).then(({ data }) => (this.units = data));
}

//
//------ Show Modal Update Detail Product
function Modal_Updat_Detail(val) {
    detail.value = {};
    detail.value.name = val.name;
    get_units(val.product_id);
    detail.value.detail_id = val.detail_id;
    detail.value.purchase_unit_id = val.purchase_unit_id;
    detail.value.Unit_cost = val.Unit_cost;
    detail.value.tax_method = val.tax_method;
    detail.value.fix_cost = val.fix_cost;
    detail.value.fix_stock = val.fix_stock;
    detail.value.stock = val.stock;
    detail.value.discount_Method = val.discount_Method;
    detail.value.discount = val.discount;
    detail.value.quantity = val.quantity;
    detail.value.tax_percent = val.tax_percent;
    dialogUpdateDetail.value = true;
}

//-----------------------------------------Calcul Total ------------------------------\\
function Calcul_Total() {
    total.value = 0;
    for (let index = 0; index < detailsForm.value.length; index++) {
        let tax =
            detailsForm.value[index].taxe * detailsForm.value[index].quantity;
        detailsForm.value[index].subtotal = parseFloat(
            detailsForm.value[index].quantity *
                detailsForm.value[index].Net_cost +
                tax
        );
        total.value = parseFloat(
            total.value + detailsForm.value[index].subtotal
        );
    }

    const total_without_discount = parseFloat(
        total.value - transferForm.value.discount
    );
    transferForm.value.TaxNet = parseFloat(
        (total_without_discount * transferForm.value.tax_rate) / 100
    );
    GrandTotal.value = parseFloat(
        total_without_discount +
            transferForm.value.TaxNet +
            transferForm.value.shipping
    );

    GrandTotal.value = parseFloat(GrandTotal.value, 2);
}

//---------- keyup OrderTax
function keyup_OrderTax() {
    if (isNaN(transfer.value.tax_rate)) {
        transfer.value.tax_rate = 0;
    } else if (transfer.value.tax_rate == "") {
        transfer.value.tax_rate = 0;
        Calcul_Total();
    } else {
        Calcul_Total();
    }
}

//---------- keyup Discount

function keyup_Discount() {
    if (isNaN(transfer.value.discount)) {
        transfer.value.discount = 0;
    } else if (transfer.value.discount == "") {
        transfer.value.discount = 0;
        Calcul_Total();
    } else {
        Calcul_Total();
    }
}

//-----------------------------------Delete Detail Product ------------------------------\\
function delete_Product_Detail(id) {
    for (let i = 0; i < detailsForm.value.length; i++) {
        if (id === detailsForm.value[i].detail_id) {
            detailsForm.value.splice(i, 1);
            Calcul_Total();
        }
    }
}

//-----------------------------------Verified Form ------------------------------\\

function verifiedForm() {
    snackbar.value.view = false;
    if (detailsForm.value.length <= 0) {
        snackbar.value.view = true;
        snackbar.value.view.color = "warning";
        snackbar.value.text = labels.no_add_product;
        return false;
    } else if (
        transferForm.value.from_warehouse === transferForm.value.to_warehouse
    ) {
        snackbar.value.view = true;
        snackbar.value.color = "warning";
        snackbar.value.text = labels.same_warehouse;
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
            snackbar.value.color = "warning";
            snackbar.value.text = labels.no_add_qty;
            return false;
        } else {
            return true;
        }
    }
}

//-------------------------------- Create Transfer ----------------------\\
function Create_Transfer() {
    if (verifiedForm()) {
        api.post({
            url: "/transfers",
            params: {
                transfer: transferForm.value,
                details: detailsForm.value,
                GrandTotal: GrandTotal.value,
            },
            snackbar,
            Success: () => {
                snackbar.value.text = "Actualizacion exitosa";
                router.visit("/transfers/");
            },
        });
    }
}

//-------------------------------- Update Transfer ----------------------\\
function Update_Transfer() {
    if (verifiedForm()) {
        let id = props.transfer.id;
        api.put({
            url: `/transfers/${id}`,
            params: {
                transfer: transferForm.value,
                details: detailsForm.value,
                GrandTotal: GrandTotal.value,
            },
            snackbar,
            Success: () => {
                snackbar.value.text = "Actualizacion exitosa";
                router.visit("/transfers/");
            },
        });
    }
}

//-------------------------------- Get Last Detail Id -------------------------\\
function Last_Detail_id() {
    product.value.detail_id = 0;
    const len = detailsForm.value.length;
    product.value.detail_id = detailsForm.value[len - 1].detail_id + 1;
}

//----------------------------------------- Add Detail of Transfer -------------------------\\
function add_Detail() {
    if (detailsForm.value.length > 0) {
        Last_Detail_id();
    } else if (detailsForm.value.length === 0) {
        product.value.detail_id = 1;
    }

    detailsForm.value.push(product.value);
}

//-----------------------------------Verified QTY ------------------------------\\
function Verified_Qty(detail, id) {
    snackbar.value.view = false;
    for (let i = 0; i < detailsForm.value.length; i++) {
        if (detailsForm.value[i].detail_id === id) {
            if (isNaN(detail.quantity)) {
                detailsForm.value[i].quantity = detail.stock;
            }

            if (parseFloat(detail.quantity) > parseFloat(detail.stock)) {
                snackbar.value.view = true;
                snackbar.value.color = "warning";
                snackbar.value.text = labels.low_qty;
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
            if (parseFloat(detail.quantity) + 1 > parseFloat(detail.stock)) {
                snackbar.value.view = true;
                snackbar.value.color = "warning";
                snackbar.value.text = labels.low_qty;
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
        if (detailsForm.value[i].detail_id === id) {
            if (parseFloat(detail.quantity) - 1 >= 1) {
                if (
                    parseFloat(detail.quantity) - 1 >
                    parseFloat(detail.stock)
                ) {
                    snackbar.value.view = true;
                    snackbar.value.color = "warning";
                    snackbar.value.text = labels.low_qty;
                } else {
                    helper.formatNumber(detailsForm.value[i].quantity--, 2);
                }
            }
        }
    }
    Calcul_Total();
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

//     //---------------------------------Get Product Details ------------------------\\
//
function Get_Product_Details(product_id) {
    axios.get("/product/" + product_id).then((response) => {
        product.value.discount = 0;
        product.value.DiscountNet = 0;
        product.value.discount_Method = "2";
        product.value.product_id = response.data.id;
        product.value.name = response.data.name;
        product.value.Net_cost = response.data.Net_cost;
        product.value.Unit_cost = response.data.Unit_cost;
        product.value.taxe = response.data.tax_cost;
        product.value.tax_method = response.data.tax_method;
        product.value.tax_percent = response.data.tax_percent;
        product.value.unitPurchase = response.data.unitPurchase;
        product.value.fix_cost = response.data.fix_cost;
        product.value.purchase_unit_id = response.data.purchase_unit_id;
        add_Detail();
        Calcul_Total();
    });
}

//---------------------- Event Select Warehouse ------------------------------\\
function Selected_Warehouse(value) {
    search_input.value = "";
    Get_Products_By_Warehouse(value);
}

watch(
    () => [props.transfer],
    () => {
        if (props.transfer != null) {
            editmode.value = true;
        } else {
            resetForm();
            editmode.value = false;
        }
    }
);

onMounted(() => {
    if (props.transfer != null) {
        transferForm.value = props.transfer;
        detailsForm.value = props.details;
        editmode.value = true;
        Get_Products_By_Warehouse(transferForm.value.from_warehouse);
    }
});
</script>
<template>
    <layout
        :loading="loading"
        :snackbar-view="snackbar.view"
        :snackbar-text="snackbar.text"
        :snackbar-color="snackbar.color"
    >
        <v-form
            ref="form"
            @submit.prevent="Submit_Transfer"
            :disabled="loading"
        >
            <v-card>
                <v-toolbar height="15"></v-toolbar>
                <v-card-text>
                    <v-row>
                        <!-- date  -->
                        <v-col lg="4" md="4" cols="12" class="mb-3">
                            <v-text-field
                                v-model="transferForm.date"
                                :label="labels.transfer.date"
                                hide-details="auto"
                                type="date"
                                :rules="helper.required"
                            >
                            </v-text-field>
                        </v-col>
                        <!-- From warehouse -->
                        <v-col lg="4" md="4" sm="12" class="mb-3">
                            <v-select
                                @update:modelValue="Selected_Warehouse"
                                v-model="transferForm.from_warehouse"
                                :items="warehouses"
                                :label="labels.transfer.from_warehouse"
                                item-title="name"
                                item-value="id"
                                hide-details="auto"
                                clearable
                                :rules="helper.required"
                                :disabled="detailsForm.length > 0"
                            ></v-select>
                        </v-col>

                        <!-- To warehouse -->
                        <v-col lg="4" md="4" cols="12" class="mb-3">
                            <v-select
                                v-model="transferForm.to_warehouse"
                                :items="warehouses"
                                :label="labels.transfer.to_warehouse"
                                item-title="name"
                                item-value="id"
                                hide-details="auto"
                                clearable
                                :rules="helper.required"
                            ></v-select>
                        </v-col>
                        <!-- Product -->
                        <v-col cols="12" class="mb-5">
                            <v-autocomplete
                                @update:modelValue="querySelections"
                                :loading="loadingFilter"
                                :items="products"
                                :model-value="search_input"
                                item-title="name"
                                item-value="id"
                                variant="solo-filled"
                                hide-no-data
                                hide-details
                                label="Añadir Producto"
                                :disabled="products.length == 0"
                                clearable
                                prepend-inner-icon="mdi-magnify"
                            ></v-autocomplete>
                        </v-col>

                        <!-- order products  -->
                        <v-col cols="12">
                            <!--                  <div class="table-responsive">-->
                            <v-table hover class="border rounded">
                                <thead>
                                    <tr class="bg-secondary">
                                        <th class="text-white text-center">
                                            #
                                        </th>
                                        <th class="text-white text-center">
                                            {{ labels.transfer_detail.product }}
                                        </th>
                                        <th class="text-white text-center">
                                            {{ labels.product.cost }}
                                        </th>
                                        <th class="text-white text-center">
                                            {{ labels.transfer_detail.stock }}
                                        </th>
                                        <th class="text-white text-center">
                                            {{ labels.transfer_detail.qty }}
                                        </th>
                                        <!--                                        <th class="text-white text-center">{{$t('Discount')}}</th>-->
                                        <!--                                        <th class="text-white text-center">{{$t('Tax')}}</th>-->
                                        <th class="text-white text-center">
                                            {{
                                                labels.transfer_detail.sub_total
                                            }}
                                        </th>
                                        <th class="text-white text-center"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-if="detailsForm.length <= 0">
                                        <td colspan="7">
                                            {{ labels.no_data_table }}
                                        </td>
                                    </tr>
                                    <tr v-for="detail in detailsForm">
                                        <td>{{ detail.detail_id }}</td>
                                        <td>
                                            <v-chip
                                                color="primary"
                                                size="small"
                                                >{{ detail.code }}</v-chip
                                            >
                                            {{ detail.name }}
                                            <!--                    <i @click="Modal_Updat_Detail(detail)" class="i-Edit"></i>-->
                                        </td>
                                        <td>
                                            {{ currency }}
                                            {{
                                                helper.formatNumber(
                                                    detail.Net_cost,
                                                    2
                                                )
                                            }}
                                        </td>
                                        <td>
                                            <v-chip color="default" size="small"
                                                >{{ detail.stock }}
                                                {{
                                                    detail.unitPurchase
                                                }}</v-chip
                                            >
                                        </td>
                                        <td>
                                            <div class="quantity">
                                                <v-text-field
                                                    class="my-1"
                                                    hide-details="auto"
                                                    :rules="helper.number"
                                                    v-model="detail.quantity"
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
                                                            mdi-plus-box
                                                        </v-icon>
                                                    </template>
                                                    <template v-slot:prepend>
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
                                        <!--                  <td>{{ currentUser.currency }} {{ formatNumber(detail.DiscountNet * detail.quantity, 2) }}</td>-->
                                        <!--                  <td>{{ currentUser.currency }} {{ formatNumber(detail.taxe * detail.quantity, 2) }}</td>-->
                                        <td>
                                            {{ currency }}
                                            {{
                                                helper.formatNumber(
                                                    detail.subtotal,
                                                    2
                                                )
                                            }}
                                        </td>
                                        <td>
                                            <delete-btn
                                                @click="
                                                    () => {
                                                        delete_Product_Detail(
                                                            detail.detail_id
                                                        );
                                                    }
                                                "
                                            ></delete-btn>
                                        </td>
                                    </tr>
                                </tbody>
                            </v-table>
                        </v-col>

                        <v-col cols="12" md="3" offset-md="9">
                            <v-table hover>
                                <tbody>
                                    <!--                                  <tr>-->
                                    <!--                                    <td class="bold">{{$t('OrderTax')}}</td>-->
                                    <!--                                    <td>-->
                                    <!--                                      <span>{{currentUser.currency}} {{transfer.TaxNet.toFixed(2)}} ({{formatNumber(transfer.tax_rate ,2)}} %)</span>-->
                                    <!--                                    </td>-->
                                    <!--                                  </tr>-->
                                    <!--                                  <tr>-->
                                    <!--                                    <td class="bold">{{$t('Discount')}}</td>-->
                                    <!--                                    <td>{{currentUser.currency}} {{transfer.discount.toFixed(2)}}</td>-->
                                    <!--                                  </tr>-->
                                    <!--                                  <tr>-->
                                    <!--                                    <td class="bold">{{$t('Shipping')}}</td>-->
                                    <!--                                    <td>{{currentUser.currency}} {{transfer.shipping.toFixed(2)}}</td>-->
                                    <!--                                  </tr>-->
                                    <tr>
                                        <td>
                                            <span class="font-weight-bold">{{
                                                labels.transfer.GrandTotal
                                            }}</span>
                                        </td>
                                        <td>
                                            <span class="font-weight-bold"
                                                >{{ currency }}
                                                {{
                                                    GrandTotal.toFixed(2)
                                                }}</span
                                            >
                                        </td>
                                    </tr>
                                </tbody>
                            </v-table>
                        </v-col>

                        <!--                &lt;!&ndash; Order Tax  &ndash;&gt;-->
                        <!--                <v-col lg="4" md="4" sm="12" class="mb-3">-->
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
                        <!--                          v-model.number="transfer.tax_rate"-->
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
                        <!--                <v-col lg="4" md="4" sm="12" class="mb-3">-->
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
                        <!--                          v-model.number="transfer.discount"-->
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
                        <!--                <v-col lg="4" md="4" sm="12" class="mb-3">-->
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
                        <!--                          v-model.number="transfer.shipping"-->
                        <!--                          @keyup="keyup_Shipping()"-->
                        <!--                        ></v-form-input>-->
                        <!--                      </v-input-group>-->
                        <!--                      <v-form-invalid-feedback-->
                        <!--                        id="Shipping-feedback"-->
                        <!--                      >{{ validationContext.errors[0] }}</v-form-invalid-feedback>-->
                        <!--                    </v-form-group>-->
                        <!--                  </validation-provider>-->
                        <!--                </v-col>-->

                        <!-- Status  -->
                        <v-col lg="4" md="4" cols="12" class="mb-3">
                            <v-select
                                v-model="transferForm.statut"
                                :items="helper.statutTransfer()"
                                :label="labels.transfer.statut"
                                item-title="title"
                                item-value="value"
                                hide-details="auto"
                                clearable
                                :rules="helper.required"
                            ></v-select>
                        </v-col>

                        <v-col md="12">
                            <v-textarea
                                rows="4"
                                :label="labels.transfer.notes"
                                v-model="transferForm.notes"
                                :placeholder="labels.transfer.notes"
                                hide-details="auto"
                            ></v-textarea>
                        </v-col>
                        <v-col md="12">
                            <v-btn
                                variant="elevated"
                                type="submit"
                                color="primary"
                                :loading="loading"
                                :disabled="loading"
                                @click="Submit_Transfer"
                                >Guardar
                            </v-btn>
                        </v-col>
                    </v-row>
                </v-card-text>
            </v-card>
        </v-form>
    </layout>
</template>
