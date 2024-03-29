<script setup>
import { onMounted, ref, watch } from "vue";
import Layout from "@/Layouts/Authenticated.vue";
import { router } from "@inertiajs/vue3";
import { api, helpers, labels, rules } from "@/helpers";
import Snackbar from "@/Components/snackbar.vue";

const props = defineProps({
    warehouses: Object,
    adjustment: { type: Object, default: null },
    details: { type: Object, default: null },
    errors: Object,
});

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
const products = ref([]);
const detailsForm = ref([]);
const adjustmentForm = ref({
    id: "",
    notes: "",
    warehouse_id: "",
    date: new Date().toISOString().slice(0, 10),
});

const product = ref({
    id: "",
    code: "",
    current: "",
    quantity: 1,
    name: "",
    product_id: "",
    detail_id: "",
    product_variant_id: "",
    unit: "",
});
const editmode = ref(false);

function resetForm() {
    adjustmentForm.value = {
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
        adjustmentForm.value.warehouse_id !== "" &&
        adjustmentForm.value.warehouse_id != null
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

//---------------- Submit Search Product-----------------\\
function SearchProduct(result) {
    snackbar.value.view = false;
    product.value = {};
    if (
        detailsForm.value.length > 0 &&
        detailsForm.value.some((detail) => detail.name === result.name)
    ) {
        snackbar.value.view = true;
        snackbar.value.text = labels.add_exist_item;
        snackbar.value.color = "warning";
    } else {
        product.value.code = result.code;
        product.value.current = result.qty ?? 0;
        product.value.quantity =
            product.value.current < 1 ? product.value.current : 1;
        product.value.product_variant_id = result.product_variant_id;
        product.value.product_id = result.id;
        product.value.name = result.name;
        product.value.type = "add";
        product.value.unit = result.unit;
        add_product();
    }
    search_input.value = "";
}

//
//------------- Submit Validation Create Adjustment
async function Submit_Adjustment() {
    const validate = await form.value.validate();
    if (validate.valid)
        if (editmode.value) {
            Update_Adjustment();
        } else {
            Create_Adjustment();
        }
}

//---------------------- Event Select Warehouse ------------------------------\\
function Selected_Warehouse(value) {
    search_input.value = "";
    Get_Products_By_Warehouse(value);
}

//------------------------------------ Get Products By Warehouse -------------------------\\

function Get_Products_By_Warehouse(id) {
    api.get({
        url: "/get_Products_by_warehouse/" + id + "?stock=" + 0,
        snackbar,
        loadingItem: loadingProduct,
        onSuccess: (data) => {
            products.value = data;
        },
    });
}

//----------------------------------------- Add Product To list -------------------------\\
function add_product() {
    if (detailsForm.value.length > 0) {
        detail_order_id();
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
                detailsForm.value[i].quantity = detail.current;
            }

            if (detail.type == "sub" && detail.quantity > detail.current) {
                snackbar.value.view = true;
                snackbar.value.text = labels.low_qty;
                snackbar.value.color = "warning";
                detailsForm.value[i].quantity = detail.current;
            } else {
                detailsForm.value[i].quantity = detail.quantity;
            }
        }
    }
}

//----------------------------------- Increment QTY ------------------------------\\
function increment(detail, id) {
    snackbar.value.view = false;
    for (let i = 0; i < detailsForm.value.length; i++) {
        if (detailsForm.value[i].detail_id === id) {
            if (detail.type === "sub") {
                if (detail.quantity + 1 > detail.current) {
                    snackbar.value.view = true;
                    snackbar.value.text = labels.low_qty;
                    snackbar.value.color = "warning";
                } else {
                    helpers.formatNumber(detailsForm.value[i].quantity++, 2);
                }
            } else {
                helpers.formatNumber(detailsForm.value[i].quantity++, 2);
            }
        }
    }
}

//----------------------------------- Decrement QTY ------------------------------\\
function decrement(detail, id) {
    snackbar.value.view = false;
    for (let i = 0; i < detailsForm.value.length; i++) {
        if (detailsForm.value[i].detail_id === id) {
            if (detail.quantity - 1 > 0) {
                if (
                    detail.type === "sub" &&
                    detail.quantity - 1 > detail.current
                ) {
                    snackbar.value.view = true;
                    snackbar.value.text = labels.low_qty;
                    snackbar.value.color = "warning";
                } else {
                    helpers.formatNumber(detailsForm.value[i].quantity--, 2);
                }
            }
        }
    }
}

//-----------------------------------Remove the product from the order list ------------------------------\\
function Remove_Product(id) {
    for (let i = 0; i < detailsForm.value.length; i++) {
        if (id === detailsForm.value[i].detail_id) {
            detailsForm.value.splice(i, 1);
        }
    }
}

//----------------------------------- Verified Quantity if Null Or zero ------------------------------\\
function verifiedForm() {
    snackbar.value.view = false;
    if (detailsForm.value.length <= 0) {
        snackbar.value.view = true;
        snackbar.value.text = labels.no_add_item;
        snackbar.value.color = "warning";
        return false;
    }
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
        snackbar.value.text = labels.no_qty_add_item;
        snackbar.value.color = "warning";
        return false;
    }
    return true;
}

//--------------------------------- Create New Adjustment -------------------------\\
function Create_Adjustment() {
    if (verifiedForm()) {
        api.post({
            url: "/adjustments",
            params: {
                warehouse_id: adjustmentForm.value.warehouse_id,
                date: adjustmentForm.value.date,
                notes: adjustmentForm.value.notes,
                details: detailsForm.value,
            },
            snackbar,
            loadingItem: loading,
            onSuccess: (data) => {
                snackbar.value.text = labels.success_message;
                router.visit("/adjustments/");
            },
        });
    }
}

//--------------------------------- Update Adjustment -------------------------\\
function Update_Adjustment() {
    if (verifiedForm()) {
        api.put({
            url: `/adjustments/${props.adjustment.id}`,
            params: {
                warehouse_id: adjustmentForm.value.warehouse_id,
                date: adjustmentForm.value.date,
                notes: adjustmentForm.value.notes,
                details: detailsForm.value,
            },
            snackbar,
            loadingItem: loading,
            onSuccess: (data) => {
                snackbar.value.text = labels.success_message;
                router.visit("/adjustments/");
            },
        });
    }
}

//-------------------------------- detail order id -------------------------\\
function detail_order_id() {
    product.value.detail_id = 0;
    let len = detailsForm.value.length;
    product.value.detail_id = detailsForm.value[len - 1].detail_id + 1;
}

//---------------------------------Get Product Details ------------------------\\

// function Get_Product_Details(product_id) {
//   axios.get("/products/detail/" + product_id).then(({data}) => {
//     product.value.product_id = data.id;
//     product.value.name = data.name;
//     product.value.type = "add";
//     product.value.unit = data.unit;
//     add_product();
//   });
// }

watch(
    () => [props.adjustment],
    () => {
        if (props.adjustment != null) {
            editmode.value = true;
        } else {
            resetForm();
            editmode.value = false;
        }
    }
);

onMounted(() => {
    if (props.adjustment != null) {
        adjustmentForm.value = props.adjustment;
        detailsForm.value = props.details;
        editmode.value = true;
        Get_Products_By_Warehouse(adjustmentForm.value.warehouse_id);
    } else if (props.warehouses.length == 1) {
        adjustmentForm.value.warehouse_id = props.warehouses[0];
        Get_Products_By_Warehouse(adjustmentForm.value.warehouse_id);
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
        <v-form
            ref="form"
            :disabled="loading"
            @submit.prevent="Submit_Adjustment"
        >
            <v-card :loading="loading">
                <v-toolbar height="15"></v-toolbar>
                <v-card-text>
                    <v-row>
                        <!-- warehouse -->
                        <v-col cols="12" sm="6">
                            <v-select
                                @update:modelValue="Selected_Warehouse"
                                v-model="adjustmentForm.warehouse_id"
                                :items="helpers.toArraySelect(warehouses)"
                                :label="labels.adjustment.warehouse_id"
                                hide-details="auto"
                                clearable
                                :rules="rules.required"
                                :disabled="detailsForm.length > 0"
                            ></v-select>
                        </v-col>

                        <!-- date  -->
                        <v-col cols="12" sm="6">
                            <v-text-field
                                v-model="adjustmentForm.date"
                                :label="labels.adjustment.date"
                                hide-details="auto"
                                type="date"
                                :rules="rules.required"
                            >
                            </v-text-field>
                        </v-col>
                    </v-row>
                    <v-row>
                        <!-- Product -->
                        <v-col cols="12">
                            <v-autocomplete
                                @update:modelValue="querySelections"
                                :loading="loadingFilter || loadingProduct"
                                :items="products"
                                :model-value="search_input"
                                item-title="name"
                                item-value="id"
                                variant="solo-filled"
                                hide-no-data
                                hide-details
                                :label="labels.product.add"
                                :disabled="products.length == 0"
                                clearable
                                prepend-inner-icon="fas fa-search"
                            ></v-autocomplete>
                        </v-col>
                        <!-- Products -->
                        <v-col cols="12">
                            <v-table hover class="border rounded">
                                <thead>
                                    <tr class="bg-secondary">
                                        <th class="text-white text-center">
                                            #
                                        </th>
                                        <th class="text-white text-center">
                                            Codigo
                                        </th>
                                        <th class="text-white text-center">
                                            Producto
                                        </th>
                                        <th class="text-white text-center">
                                            En Stock
                                        </th>
                                        <th class="text-white text-center">
                                            Cantidad
                                        </th>
                                        <th class="text-white text-center">
                                            Tipo
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
                                    <tr
                                        v-for="detail in detailsForm"
                                        :key="detail.detail_id"
                                    >
                                        <td>
                                            {{ detail.detail_id }}
                                        </td>
                                        <td>{{ detail.code }}</td>
                                        <td>({{ detail.name }})</td>
                                        <td class="text-center">
                                            <v-chip
                                                color="primary"
                                                size="small"
                                            >
                                                {{ detail.current }}
                                                {{ detail.unit }}
                                            </v-chip>
                                        </td>
                                        <td>
                                            <v-text-field
                                                class="my-1"
                                                hide-details="auto"
                                                :rules="rules.number"
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
                                                        fas fa-plus-square
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
                                                        fas fa-minus-square
                                                    </v-icon>
                                                </template>
                                            </v-text-field>
                                        </td>
                                        <td>
                                            <v-select
                                                v-model="detail.type"
                                                :items="[
                                                    {
                                                        title: 'Añadir',
                                                        value: 'add',
                                                    },
                                                    {
                                                        title: 'Quitar',
                                                        value: 'sub',
                                                    },
                                                ]"
                                                item-title="title"
                                                item-value="value"
                                                :hide-details="true"
                                            ></v-select>
                                        </td>
                                        <td>
                                            <v-btn
                                                class="ma-1"
                                                color="error"
                                                icon="fas fa-trash"
                                                size="x-small"
                                                variant="elevated"
                                                @click="
                                                    Remove_Product(
                                                        detail.detail_id
                                                    )
                                                "
                                            >
                                            </v-btn>
                                        </td>
                                    </tr>
                                </tbody>
                            </v-table>
                        </v-col>
                        <v-col cols="12">
                            <v-textarea
                                rows="4"
                                :label="labels.adjustment.notes"
                                v-model="adjustmentForm.notes"
                                :placeholder="labels.adjustment.notes"
                                hide-details="auto"
                            ></v-textarea>
                        </v-col>
                    </v-row>
                    <v-row class="mt-3">
                        <v-col cols="12">
                            <v-btn
                                variant="elevated"
                                type="submit"
                                color="primary"
                                :loading="loading"
                                :disabled="loading"
                                >{{ labels.submit }}
                            </v-btn>
                        </v-col>
                    </v-row>
                </v-card-text>
            </v-card>
        </v-form>
    </Layout>
</template>
