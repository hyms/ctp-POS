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
const product_filter = ref([]);
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

watch(search_input, (val) => {
    val && val !== product_filter.value && querySelections(val);
});

function querySelections(val) {
    loadingFilter.value = true;
    if (timer.value) {
        clearTimeout(timer.value);
        timer.value = null;
    }

    if (search_input.value.length < 1) {
        return (product_filter.value = []);
    }

    if (
        adjustmentForm.value.warehouse_id !== "" &&
        adjustmentForm.value.warehouse_id != null
    ) {
        timer.value = setTimeout(() => {
            const product_filter = products.value.filter(
                (product) => product.code === search_input.value
            );
            if (product_filter.length === 1) {
                SearchProduct(product_filter[0]);
            } else {
                product_filter.value = products.value.filter((product) => {
                    return (
                        product.name
                            .toLowerCase()
                            .includes(search_input.value.toLowerCase()) ||
                        product.code
                            .toLowerCase()
                            .includes(search_input.value.toLowerCase())
                    );
                });
            }
            loadingFilter.value = false;
        }, 800);
    }
}

//---------------- Submit Search Product-----------------\\
function SearchProduct(result) {
    snackbar.value = false;
    product.value = {};
    if (
        details.value.length > 0 &&
        details.value.some((detail) => detail.code === result.code)
    ) {
        snackbar.value = true;
        snackbarText.value = "Ya esta añadido";
        snackbarColor.value = "warning";
    } else {
        product.value.code = result.code;
        product.value.current = result.qty;
        product.value.quantity = result.qty < 1 ? result.qty : 1;
        product.value.product_variant_id = result.product_variant_id;
        Get_Product_Details(result.id);
    }
    search_input.value = "";
    product_autocomplete.value = "";
    product_filter.value = [];
}

//---------------------- Event Get Value Search ------------------------------\\
function getResultValue(result) {
    return result.code + " " + "(" + result.name + ")";
}

//
//------------- Submit Validation Create Adjustment
function Submit_Adjustment() {
    // form.validate();
    Create_Adjustment();
}

//---------------------- Event Select Warehouse ------------------------------\\
function Selected_Warehouse(value) {
    search_input.value = "";
    product_filter.value = [];
    Get_Products_By_Warehouse(value);
}

//------------------------------------ Get Products By Warehouse -------------------------\\

function Get_Products_By_Warehouse(id) {
    axios
        .get("get_Products_by_warehouse/" + id + "?stock=" + 0)
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
    for (let i = 0; i < details.value.length; i++) {
        if (details.value[i].detail_id === id) {
            if (isNaN(detail.quantity)) {
                details.value[i].quantity = detail.current;
            }

            if (detail.type == "sub" && detail.quantity > detail.current) {
                this.makeToast(
                    "warning",
                    this.$t("LowStock"),
                    this.$t("Warning")
                );
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
        if (details.value[i].detail_id == id) {
            if (detail.type == "sub") {
                if (detail.quantity + 1 > detail.current) {
                    snackbar.value = true;
                    snackbarText.value = "Queda poco Stock";
                    snackbarColor.value = "warning";
                } else {
                    this.formatNumber(details.value[i].quantity++, 2);
                }
            } else {
                this.formatNumber(details.value[i].quantity++, 2);
            }
        }
    }
}

//----------------------------------- Decrement QTY ------------------------------\\
function decrement(detail, id) {
    for (let i = 0; i < details.value.length; i++) {
        if (details.value[i].detail_id == id) {
            if (detail.quantity - 1 > 0) {
                if (
                    detail.type == "sub" &&
                    detail.quantity - 1 > detail.current
                ) {
                    snackbar.value = true;
                    snackbarText.value = "Queda poco Stock";
                    snackbarColor.value = "warning";
                } else {
                    this.formatNumber(details.value[i].quantity--, 2);
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
            .post("adjustments", {
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
    axios.get("products/" + product_id).then(({ data }) => {
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
            :snackbar="snackbar"
            :snackbarColor="snackbarColor"
            :snackbarText="snackbarText"
        >
        </snackbar>
        <v-form @submit.prevent="Submit_Adjustment">
            <v-row>
                <v-col lg="12" md="12" sm="12">
                    <v-card>
                        <v-row>
                            <!-- warehouse -->
                            <v-col md="6" class="mb-3">
                                <!--                  <validation-provider name="warehouse" :rules="{ required: true}">-->
                                <!--                    <v-form-group slot-scope="{ valid, errors }" :label="$t('warehouse') + ' ' + '*'">-->
                                <!--                      <v-select-->
                                <!--                        :class="{'is-invalid': !!errors.length}"-->
                                <!--                        :state="errors[0] ? false : (valid ? true : null)"-->
                                <!--                        :disabled="details.length > 0"-->
                                <!--                        @input="Selected_Warehouse"-->
                                <!--                        v-model="adjustment.warehouse_id"-->
                                <!--                        :reduce="label => label.value"-->
                                <!--                        :placeholder="$t('Choose_Warehouse')"-->
                                <!--                        :options="warehouses.map(warehouses => ({label: warehouses.name, value: warehouses.id}))"-->
                                <!--                      />-->
                                <!--                      <v-form-invalid-feedback>{{ errors[0] }}</v-form-invalid-feedback>-->
                                <!--                    </v-form-group>-->
                                <!--                  </validation-provider>-->
                            </v-col>

                            <!-- date  -->
                            <v-col lg="6" md="6" sm="12">
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
                            <v-col md="12" class="mb-5">
                                <v-autocomplete
                                    v-model="product_autocomplete"
                                    v-model:search="search_input"
                                    :loading="loadingFilter"
                                    :items="product_filter"
                                    class="mx-4"
                                    density="comfortable"
                                    hide-no-data
                                    hide-details
                                    label="Producto"
                                ></v-autocomplete>
                            </v-col>
                        </v-row>
                        <v-row>
                            <!-- Products -->
                            <v-col md="12">
                                <v-table>
                                    <thead class="bg-gray-300">
                                        <tr>
                                            <th scope="col">#</th>
                                            <th scope="col">Codigo</th>
                                            <th scope="col">Producto</th>
                                            <th scope="col">En Stock</th>
                                            <th scope="col">Cantidad</th>
                                            <th scope="col">Tipo</th>
                                            <th scope="col" class="text-center">
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
                                            <td>{{ detail.detail_id }}</td>
                                            <td>{{ detail.code }}</td>
                                            <td>({{ detail.name }})</td>
                                            <td>
                                                <span
                                                    class="badge badge-outline-warning"
                                                    >{{ detail.current }}
                                                    {{ detail.unit }}</span
                                                >
                                            </td>
                                            <td>
                                                <div class="quantity">
                                                    <!--                                                    <v-input-group>-->
                                                    <!--                                                        <v-input-group-prepend>-->
                                                    <!--                                                            <span-->
                                                    <!--                                                                class="btn btn-primary btn-sm"-->
                                                    <!--                                                                @click="-->
                                                    <!--                                                                    decrement(-->
                                                    <!--                                                                        detail,-->
                                                    <!--                                                                        detail.detail_id-->
                                                    <!--                                                                    )-->
                                                    <!--                                                                "-->
                                                    <!--                                                                >-</span-->
                                                    <!--                                                            >-->
                                                    <!--                                                        </v-input-group-prepend>-->

                                                    <!--                                                        <input-->
                                                    <!--                                                            class="form-control"-->
                                                    <!--                                                            @keyup="-->
                                                    <!--                                                                Verified_Qty(-->
                                                    <!--                                                                    detail,-->
                                                    <!--                                                                    detail.detail_id-->
                                                    <!--                                                                )-->
                                                    <!--                                                            "-->
                                                    <!--                                                            :min="0.0"-->
                                                    <!--                                                            :max="-->
                                                    <!--                                                                detail.current-->
                                                    <!--                                                            "-->
                                                    <!--                                                            v-model.number="-->
                                                    <!--                                                                detail.quantity-->
                                                    <!--                                                            "-->
                                                    <!--                                                        />-->
                                                    <!--                                                        <v-input-group-append>-->
                                                    <!--                                                            <span-->
                                                    <!--                                                                class="btn btn-primary btn-sm"-->
                                                    <!--                                                                @click="-->
                                                    <!--                                                                    increment(-->
                                                    <!--                                                                        detail,-->
                                                    <!--                                                                        detail.detail_id-->
                                                    <!--                                                                    )-->
                                                    <!--                                                                "-->
                                                    <!--                                                                >+</span-->
                                                    <!--                                                            >-->
                                                    <!--                                                        </v-input-group-append>-->
                                                    <!--                                                    </v-input-group>-->
                                                </div>
                                            </td>
                                            <td>
                                                <!--                                                <select-->
                                                <!--                                                    v-model="detail.type"-->
                                                <!--                                                    @change="-->
                                                <!--                                                        Verified_Qty(-->
                                                <!--                                                            detail,-->
                                                <!--                                                            detail.detail_id-->
                                                <!--                                                        )-->
                                                <!--                                                    "-->
                                                <!--                                                    type="text"-->
                                                <!--                                                    required-->
                                                <!--                                                    class="form-control"-->
                                                <!--                                                >-->
                                                <!--                                                    <option value="add">-->
                                                <!--                                                        {{ $t("Addition") }}-->
                                                <!--                                                    </option>-->
                                                <!--                                                    <option value="sub">-->
                                                <!--                                                        {{ $t("Subtraction") }}-->
                                                <!--                                                    </option>-->
                                                <!--                                                </select>-->
                                            </td>
                                            <td>
                                                <a
                                                    @click="
                                                        Remove_Product(
                                                            detail.detail_id
                                                        )
                                                    "
                                                    class="btn btn-icon btn-sm"
                                                    title="Delete"
                                                >
                                                    <i
                                                        class="i-Close-Window text-25 text-danger"
                                                    ></i>
                                                </a>
                                            </td>
                                        </tr>
                                    </tbody>
                                </v-table>
                            </v-col>
                            <v-col md="12">
                                <!--                  <v-form-group :label="$t('Note')" class="mt-4">-->
                                <!--                    <textarea-->
                                <!--                      v-model="adjustment.notes"-->
                                <!--                      rows="4"-->
                                <!--                      class="form-control"-->
                                <!--                      :placeholder="$t('Afewwords')"-->
                                <!--                    ></textarea>-->
                                <!--                  </v-form-group>-->
                            </v-col>
                            <v-col md="12">
                                <!--                  <v-form-group>-->
                                <!--                     <v-button variant="primary" :disabled="SubmitProcessing" @click="Submit_Adjustment">{{$t('submit')}}</v-button>-->
                                <!--                      <div v-once class="typo__p" v-if="SubmitProcessing">-->
                                <!--                        <div class="spinner sm spinner-primary mt-3"></div>-->
                                <!--                      </div>-->
                                <!--                  </v-form-group>-->
                            </v-col>
                        </v-row>
                    </v-card>
                </v-col>
            </v-row>
        </v-form>
    </Layout>
</template>
