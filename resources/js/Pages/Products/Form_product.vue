<script setup>
import { onMounted, ref, watch } from "vue";
import Layout from "@/Layouts/Authenticated.vue";
import { router } from "@inertiajs/vue3";
import { api, helpers, labels, rules } from "@/helpers";
import Vue3TagsInput from "vue3-tags-input";
import Snackbar from "@/Components/snackbar.vue";

const props = defineProps({
    categories: Object,
    units: Object,
    product: { type: Object, default: null },
    errors: Object,
});
const form = ref(null);
const editmode = ref(false);
const loading = ref(false);
const loadingUnit = ref(false);
const snackbar = ref({
    view: false,
    color: "",
    text: "",
});
const units_sub = ref([]);
const variants = ref([]);
const productForm = ref({
    name: "",
    code: "",
    cost: "",
    price: "",
    category_id: "",
    TaxNet: "0",
    tax_method: "1",
    unit_id: "",
    unit_sale_id: "",
    unit_purchase_id: "",
    stock_alert: "0",
    image: "",
    note: "",
    is_variant: false,
    is_imei: false,
    not_selling: false,
});

function resetForm() {
    productForm.value = {
        name: "",
        code: "",
        cost: "",
        price: "",
        category_id: "",
        TaxNet: "0",
        tax_method: "1",
        unit_id: "",
        unit_sale_id: "",
        unit_purchase_id: "",
        stock_alert: "0",
        image: "",
        note: "",
        is_variant: false,
        is_imei: false,
        not_selling: false,
    };
}

//------------- Submit Validation Create Product
async function Submit_Product() {
    const validate = await form.value.validate();
    if (validate.valid)
        if (editmode.value) {
            Edit_Product();
        } else {
            Create_Product();
        }
}

//---------------------- Get Sub Units with Unit id ------------------------------\\
function Get_Units_SubBase(value) {
    api.get({
        url: "/get_sub_units_by_base?id=" + value,
        snackbar,
        loadingItem: loadingUnit,
        onSuccess: (data) => {
            units_sub.value = data;
        },
    });
}

//---------------------- Event Select Unit Product ------------------------------\\
function Selected_Unit(value) {
    units_sub.value = [];
    productForm.value.unit_sale_id = "";
    productForm.value.unit_purchase_id = "";
    Get_Units_SubBase(value);
}

//------------------------------ Create new Product ------------------------------\\
function Create_Product() {
    let params = {};
    if (productForm.value.is_variant && variants.value.length <= 0) {
        productForm.value.is_variant = false;
    }
    // append objet product
    Object.entries(productForm.value).forEach(([key, value]) => {
        params[key] = value;
    });
    // append array variants
    if (variants.value.length) {
        params[variants] = [];
        for (let i = 0; i < variants.value.length; i++) {
            params[variants][i] = variants.value[i];
        }
    }
    api.post({
        url: "/products",
        snackbar,
        loadingItem: loading,
        params,
        onSuccess: () => {
            snackbar.value.text = "Actualizacion exitosa";
            router.visit("/products/");
        },
    });
}

//------------------------------ Edit  Product ------------------------------\\
function Edit_Product() {
    if (productForm.value.is_variant && variants.value.length <= 0) {
        productForm.value.is_variant = false;
    }

    // append array variants
    if (variants.value.length) {
        productForm.value.variants = [];
        for (let i = 0; i < variants.value.length; i++) {
            productForm.value.variants[i] = variants.value[i];
            // data.append("variants[" + i + "]", variants.value[i]);
        }
    }
    // Send Data with axios
    api.put({
        url: "/products/" + productForm.value.id,
        snackbar,
        loadingItem: loading,
        params: productForm.value,
        onSuccess: () => {
            snackbar.value.text = "Actualizacion exitosa";
            router.visit("/products/");
        },
    });
}

function onChangeTag(value) {
    variants.value = value;
}

watch(
    () => [props.product],
    () => {
        if (props.product != null) {
            productForm.value = props.product;
            Get_Units_SubBase(props.product.unit_id);
            for (let key in props.product.ProductVariant) {
                variants.value[key] = props.product.ProductVariant[key].text;
            }
            editmode.value = true;
        } else {
            resetForm();
            editmode.value = false;
        }
    }
);

onMounted(() => {
    if (props.product != null) {
        productForm.value = props.product;
        Get_Units_SubBase(props.product.unit_id);
        for (let key in props.product.ProductVariant) {
            variants.value[key] = props.product.ProductVariant[key].text;
        }
        editmode.value = true;
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
        <v-card variant="elevated">
            <v-toolbar height="15"></v-toolbar>
            <v-card-text>
                <v-form
                    @submit.prevent="Submit_Product"
                    ref="form"
                    :disabled="loading"
                >
                    <v-row>
                        <!-- Name -->
                        <v-col cols="12" md="6">
                            <v-text-field
                                :label="labels.product.name + ' *'"
                                v-model="productForm.name"
                                :placeholder="labels.product.name"
                                :rules="rules.required"
                                hide-details="auto"
                            >
                            </v-text-field>
                        </v-col>

                        <!-- Code Product"-->
                        <v-col cols="12" md="6">
                            <v-text-field
                                :label="labels.product.code + ' *'"
                                v-model="productForm.code"
                                :placeholder="labels.product.code"
                                :rules="rules.required"
                                hide-details="auto"
                            >
                            </v-text-field>
                        </v-col>

                        <!-- Category -->
                        <v-col cols="12" md="6">
                            <v-select
                                v-model="productForm.category_id"
                                :items="helpers.toArraySelect(categories)"
                                :label="labels.product.category_id"
                                hide-details="auto"
                                clearable
                                :rules="rules.required"
                            ></v-select>
                        </v-col>

                        <!-- Product Cost -->
                        <v-col cols="12" md="6">
                            <v-text-field
                                :label="labels.product.cost + ' *'"
                                v-model="productForm.cost"
                                :placeholder="labels.product.cost"
                                :rules="
                                    rules.required.concat(
                                        rules.numberWithDecimal
                                    )
                                "
                                hide-details="auto"
                            >
                            </v-text-field>
                        </v-col>

                        <!-- Product Price -->
                        <v-col cols="12" md="6">
                            <v-text-field
                                :label="labels.product.price + ' *'"
                                v-model="productForm.price"
                                :placeholder="labels.product.price"
                                :rules="
                                    rules.required.concat(
                                        rules.numberWithDecimal
                                    )
                                "
                                hide-details="auto"
                            >
                            </v-text-field>
                        </v-col>

                        <!-- Unit Product -->
                        <v-col cols="12" md="6">
                            <v-select
                                @update:modelValue="Selected_Unit"
                                v-model="productForm.unit_id"
                                :items="helpers.toArraySelect(units)"
                                :label="labels.product.unit_id"
                                hide-details="auto"
                                clearable
                                :rules="rules.required"
                            ></v-select>
                        </v-col>

                        <!-- Unit Sale -->
                        <v-col cols="12" md="6">
                            <v-select
                                v-model="productForm.unit_sale_id"
                                :items="helpers.toArraySelect(units_sub)"
                                :label="labels.product.unit_sale_id"
                                hide-details="auto"
                                clearable
                                :rules="rules.required"
                                :loading="loadingUnit"
                            ></v-select>
                        </v-col>
                        <!---->
                        <!-- Unit Purchase -->
                        <v-col cols="12" md="6">
                            <v-select
                                v-model="productForm.unit_purchase_id"
                                :items="helpers.toArraySelect(units_sub)"
                                :label="labels.product.unit_purchase_id"
                                hide-details="auto"
                                clearable
                                :loading="loadingUnit"
                                :rules="rules.required"
                            ></v-select>
                        </v-col>
                        <!---->
                        <!-- Stock Alert -->
                        <v-col cols="12" md="6">
                            <v-text-field
                                :label="labels.product.stock_alert"
                                v-model="productForm.stock_alert"
                                :placeholder="labels.product.stock_alert"
                                :rules="rules.numberWithDecimal"
                                hide-details="auto"
                            >
                            </v-text-field>
                        </v-col>
                        <!---->

                        <v-col cols="12">
                            <v-textarea
                                rows="4"
                                :label="labels.product.note"
                                v-model="productForm.note"
                                :placeholder="labels.product.note"
                                hide-details="auto"
                            ></v-textarea>
                        </v-col>
                        <!---->
                        <!-- Multiple Variants -->
                        <v-col cols="12" class="pt-0 pb-0">
                            <v-checkbox
                                v-model="productForm.is_variant"
                                :label="labels.product.is_variant"
                                hide-details="auto"
                            ></v-checkbox>
                        </v-col>
                        <v-col cols="12" v-if="productForm.is_variant">
                            <vue3-tags-input
                                :tags="variants"
                                placeholder="+ añadir"
                                @on-tags-changed="onChangeTag"
                            />
                        </v-col>

                        <!-- This_Product_Not_For_Selling -->
                        <v-col cols="12" class="pt-0 pb-0">
                            <v-checkbox
                                v-model="productForm.not_selling"
                                :label="labels.product.not_selling"
                                hide-details="auto"
                            ></v-checkbox>
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
                                >Guardar
                            </v-btn>
                        </v-col>
                    </v-row>
                </v-form>
            </v-card-text>
        </v-card>
    </Layout>
</template>
