<script setup>
import { ref, onMounted, watch } from "vue";
import Layout from "@/Layouts/Authenticated.vue";
import Snackbar from "@/Components/snackbar.vue";
import { router } from "@inertiajs/vue3";
import ruleForm from "@/rules";

const props = defineProps({
    warehouses: Object,
    adjustment: { type: Object, default: null },
    errors: Object,
});

const form = ref(null);
const loading = ref(false);
const loadingFilter = ref(false);
const snackbar = ref(false);
const snackbarText = ref("");
const snackbarColor = ref("info");
const focused = ref(false);
const search_input = ref("");
const timer = ref(null);
const products = ref([]);
const details = ref([]);
const adjustmentForm = ref({
    id: "",
    notes: "",
    warehouse_id: "",
    date: new Date().toISOString().slice(0, 10),
});
const adjustmentLabel = ref({
    notes: "Detalle",
    warehouse_id: "Agencia",
    date: "Fecha",
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
const symbol = ref("");
const product_autocomplete = ref("");
const editmode = ref(false);
const search = ref("");

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
    snackbar.value = false;
    product.value = {};
    if (
        details.value.length > 0 &&
        details.value.some((detail) => detail.name === result.name)
    ) {
        snackbar.value = true;
        snackbarText.value = "Ya esta añadido";
        snackbarColor.value = "warning";
    } else {
        product.value.code = result.code;
        product.value.current = result.qte ?? 0;
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
            // Edit_Product();
        } else {
            Create_Adjustment();
        }
}

//---------------------- Event Select Warehouse ------------------------------\\
function Selected_Warehouse(value) {
    search_input.value = "";
    product.value = [];
    Get_Products_By_Warehouse(value);
}

//------------------------------------ Get Products By Warehouse -------------------------\\

function Get_Products_By_Warehouse(id) {
    axios
        .get("/get_Products_by_warehouse/" + id + "?stock=" + 0)
        .then((response) => {
            products.value = response.data;
        })
        .catch((error) => {});
}

//----------------------------------------- Add Product To list -------------------------\\
function add_product() {
    if (details.value.length > 0) {
        detail_order_id();
    } else if (details.value.length === 0) {
        product.value.detail_id = 1;
    }
    details.value.push(product.value);
}

//-----------------------------------Verified QTY ------------------------------\\
function Verified_Qty(detail, id) {
    snackbar.value = false;
    for (let i = 0; i < details.value.length; i++) {
        if (details.value[i].detail_id === id) {
            if (isNaN(detail.quantity)) {
                details.value[i].quantity = detail.current;
            }

            if (detail.type == "sub" && detail.quantity > detail.current) {
                snackbar.value = true;
                snackbarText.value = "Queda poco Stock";
                snackbarColor.value = "warning";
                details.value[i].quantity = detail.current;
            } else {
                details.value[i].quantity = detail.quantity;
            }
        }
    }
}

//----------------------------------- Increment QTY ------------------------------\\
function increment(detail, id) {
    snackbar.value = false;
    for (let i = 0; i < details.value.length; i++) {
        if (details.value[i].detail_id === id) {
            if (detail.type === "sub") {
                if (detail.quantity + 1 > detail.current) {
                    snackbar.value = true;
                    snackbarText.value = "Queda poco Stock";
                    snackbarColor.value = "warning";
                } else {
                    formatNumber(details.value[i].quantity++, 2);
                }
            } else {
                formatNumber(details.value[i].quantity++, 2);
            }
        }
    }
}

//----------------------------------- Decrement QTY ------------------------------\\
function decrement(detail, id) {
    snackbar.value = false;
    for (let i = 0; i < details.value.length; i++) {
        if (details.value[i].detail_id === id) {
            if (detail.quantity - 1 > 0) {
                if (
                    detail.type === "sub" &&
                    detail.quantity - 1 > detail.current
                ) {
                    snackbar.value = true;
                    snackbarText.value = "Queda poco Stock";
                    snackbarColor.value = "warning";
                } else {
                    formatNumber(details.value[i].quantity--, 2);
                }
            }
        }
    }
}

//------------------------------Formetted Numbers -------------------------\\
function formatNumber(number, dec) {
    const value = (
        typeof number === "string" ? number : number.toString()
    ).split(".");
    if (dec <= 0) return value[0];
    let formated = value[1] || "";
    if (formated.length > dec) return `${value[0]}.${formated.substr(0, dec)}`;
    while (formated.length < dec) formated += "0";
    return `${value[0]}.${formated}`;
}

//-----------------------------------Remove the product from the order list ------------------------------\\
function Remove_Product(id) {
    for (let i = 0; i < details.value.length; i++) {
        if (id === details.value[i].detail_id) {
            details.value.splice(i, 1);
        }
    }
}

//----------------------------------- Verified Quantity if Null Or zero ------------------------------\\
function verifiedForm() {
    snackbar.value = false;
    if (details.value.length <= 0) {
        snackbar.value = true;
        snackbarText.value = "Agregue un producto a la lista";
        snackbarColor.value = "warning";
        return false;
    }
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
        snackbarText.value = "Agregue cantidad";
        snackbarColor.value = "warning";
        return false;
    }
    return true;
}

//--------------------------------- Create New Adjustment -------------------------\\
function Create_Adjustment() {
    if (verifiedForm()) {
        loading.value = true;
        snackbar.value = false;
        axios
            .post("/adjustments", {
                warehouse_id: adjustmentForm.value.warehouse_id,
                date: adjustmentForm.value.date,
                notes: adjustmentForm.value.notes,
                details: details.value,
            })
            .then(({ data }) => {
                snackbar.value = true;
                snackbarColor.value = "success";
                snackbarText.value = "Actualizacion exitosa";
                router.visit("/adjustments/list");
            })
            .catch((error) => {
                console.log(error);
                snackbar.value = true;
                snackbarColor.value = "error";
                snackbarText.value = error.response.data.message;
            })
            .finally(() => {
                setTimeout(() => {
                    loading.value = false;
                }, 1000);
            });
    }
}

//-------------------------------- detail order id -------------------------\\
function detail_order_id() {
    product.value.detail_id = 0;
    let len = details.value.length;
    product.value.detail_id = details.value[len - 1].detail_id + 1;
}

//---------------------------------Get Product Details ------------------------\\

function Get_Product_Details(product_id) {
    axios.get("/products/detail/" + product_id).then(({ data }) => {
        product.value.product_id = data.id;
        product.value.name = data.name;
        product.value.type = "add";
        product.value.unit = data.unit;
        add_product();
    });
}

//---------------------------------------Get Adjustment Elements ------------------------------\\
function Get_Elements() {
    axios
        .get("adjustments/create")
        .then((response) => {
            this.warehouses = response.data.warehouses;
            this.isLoading = false;
        })
        .catch((response) => {
            setTimeout(() => {
                this.isLoading = false;
            }, 500);
        });
}

onMounted(() => {
    if (props.adjustment != null) {
        adjustmentForm.value = props.adjustment;
        // for (let key in props.adjustment.ProductVariant) {
        //   variants.value[key] = props.product.ProductVariant[key].text;
        // }
        editmode.value = true;
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
            <v-row>
                <v-col lg="12" cols="12" sm="12">
                    <v-card>
                        <v-card-text>
                            <v-row>
                                <!-- warehouse -->
                                <v-col cols="12" md="6">
                                    <v-select
                                        @update:modelValue="Selected_Warehouse"
                                        v-model="adjustmentForm.warehouse_id"
                                        :items="warehouses"
                                        :label="adjustmentLabel.warehouse_id"
                                        item-title="title"
                                        item-value="value"
                                        variant="outlined"
                                        density="comfortable"
                                        hide-details="auto"
                                        clearable
                                        :rules="ruleForm.required"
                                        :disabled="details.length > 0"
                                    ></v-select>
                                </v-col>

                                <!-- date  -->
                                <v-col cols="12" lg="6">
                                    <!--                  <validation-provider-->
                                    <!--                    name="date"-->
                                    <!--                    :rules="{ required: true}"-->
                                    <!--                    v-slot="validationContext"-->
                                    <!--                  >-->
                                    <!--                    <v-form-group :label="$t('date') + ' ' + '*'">-->
                                    <!--                      <v-form-input-->
                                    <!--                        :state="getValidationState(validationContext)"-->
                                    <!--                        aria-describedby="date-feedback"-->
                                    <!--                        type="date"-->
                                    <!--                        v-model="adjustment.date"-->
                                    <!--                      ></v-form-input>-->
                                    <!--                      <v-form-invalid-feedback-->
                                    <!--                        id="OrderTax-feedback"-->
                                    <!--                      >{{ validationContext.errors[0] }}</v-form-invalid-feedback>-->
                                    <!--                    </v-form-group>-->
                                    <!--                  </validation-provider>-->
                                </v-col>
                            </v-row>
                            <v-row>
                                <!-- Product -->
                                <v-col cols="12">
                                    <v-autocomplete
                                        @update:modelValue="querySelections"
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
                                    ></v-autocomplete>
                                </v-col>
                                <!-- Products -->
                                <v-col cols="12">
                                    <v-table>
                                        <thead class="bg-gray-300">
                                            <tr>
                                                <th scope="col">#</th>
                                                <th scope="col">Codigo</th>
                                                <th scope="col">Producto</th>
                                                <th scope="col">En Stock</th>
                                                <th scope="col">Cantidad</th>
                                                <th scope="col">Tipo</th>
                                                <th
                                                    scope="col"
                                                    class="text-center"
                                                >
                                                    <i class="fa fa-trash"></i>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-if="details.length <= 0">
                                                <td colspan="7">
                                                    No hay datos disponibles
                                                </td>
                                            </tr>
                                            <tr
                                                v-for="detail in details"
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
                                                        variant="outlined"
                                                        density="compact"
                                                        hide-details="auto"
                                                        :rules="ruleForm.number"
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
                                                        variant="outlined"
                                                        density="compact"
                                                        hide-details="auto"
                                                    ></v-select>
                                                </td>
                                                <td>
                                                    <v-btn
                                                        class="ma-1"
                                                        color="error"
                                                        icon="mdi-delete"
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
                                        :label="adjustmentLabel.notes"
                                        v-model="adjustmentForm.notes"
                                        :placeholder="adjustmentLabel.notes"
                                        variant="outlined"
                                        density="comfortable"
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
                                        @click="Submit_Adjustment"
                                        >Guardar
                                    </v-btn>
                                </v-col>
                            </v-row>
                        </v-card-text>
                    </v-card>
                </v-col>
            </v-row>
        </v-form>
    </Layout>
</template>
