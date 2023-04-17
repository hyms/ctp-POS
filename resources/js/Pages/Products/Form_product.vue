<script setup>
import { ref, onMounted } from "vue";
import Layout from "@/Layouts/Authenticated.vue";
import { router } from "@inertiajs/vue3";
import ruleForm from "@/rules";
import Snackbar from "@/Components/snackbar.vue";
import Vue3TagsInput from "vue3-tags-input";

const props = defineProps({
    categories: Object,
    units: Object,
    product: { type: Object, default: null },
    errors: Object,
});
const form = ref(null);
const editmode = ref(false);
const search = ref("");
const loading = ref(false);
const snackbar = ref(false);
const snackbarText = ref("");
const snackbarColor = ref("info");
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
const productLabel = ref({
    name: "Nombre",
    code: "Codigo",
    cost: "Costo",
    price: "Precio",
    category_id: "Categoria",
    TaxNet: "Factura",
    tax_method: "Metodo de Factura",
    unit_id: "Unidad",
    unit_sale_id: "Unidad de Venta",
    unit_purchase_id: "Unidad de Compra",
    stock_alert: "Alerta en Stock",
    note: "Detalle",
    is_variant: "El Producto tiene multiples variantes",
    not_selling: "Este Producto no es para la venta",
});

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
    axios
        .get("/products/get_sub_units_by_base?id=" + value)
        .then(({ data }) => {
            units_sub.value = data;
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
    loading.value = true;
    snackbar.value = false;
    // Start the progress bar.
    let data = new FormData();

    if (productForm.value.is_variant && variants.value.length <= 0) {
        productForm.value.is_variant = false;
    }
    // append objet product
    Object.entries(productForm.value).forEach(([key, value]) => {
        data.append(key, value);
    });

    // append array variants
    if (variants.value.length) {
        for (let i = 0; i < variants.value.length; i++) {
            data.append("variants[" + i + "]", variants.value[i]);
        }
    }

    // Send Data with axios
    axios
        .post("/products", data)
        .then(({ data }) => {
            snackbar.value = true;
            snackbarColor.value = "success";
            snackbarText.value = "Actualizacion exitosa";
            router.visit("/products/list");
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

//------------------------------ Edit  Product ------------------------------\\
function Edit_Product() {
    loading.value = true;
    snackbar.value = false;

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
    axios
        .put("/products/" + productForm.value.id, productForm.value)
        .then(({ data }) => {
            snackbar.value = true;
            snackbarColor.value = "success";
            snackbarText.value = "Actualizacion exitosa";
            router.visit("/products/list");
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

function onChangeTag(value) {
    variants.value = value;
}

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
    <Layout :loading="loading">
        <snackbar
            :snackbar="snackbar"
            :snackbarColor="snackbarColor"
            :snackbarText="snackbarText"
        >
        </snackbar>

        <v-card>
            <v-card-text>
                <v-form @submit.prevent="Submit_Product" ref="form">
                    <v-row>
                        <!-- Name -->
                        <v-col md="6">
                            <v-text-field
                                :label="productLabel.name + ' *'"
                                v-model="productForm.name"
                                :placeholder="productLabel.name"
                                :rules="ruleForm.required"
                                variant="outlined"
                                density="comfortable"
                                hide-details="auto"
                            >
                            </v-text-field>
                        </v-col>

                        <!-- Code Product"-->
                        <v-col md="6">
                            <v-text-field
                                :label="productLabel.code + ' *'"
                                v-model="productForm.code"
                                :placeholder="productLabel.code"
                                :rules="ruleForm.required"
                                variant="outlined"
                                density="comfortable"
                                hide-details="auto"
                            >
                            </v-text-field>
                        </v-col>

                        <!-- Category -->
                        <v-col md="6">
                            <v-select
                                v-model="productForm.category_id"
                                :items="categories"
                                :label="productLabel.category_id"
                                item-title="title"
                                item-value="value"
                                variant="outlined"
                                density="comfortable"
                                hide-details="auto"
                                clearable
                                :rules="ruleForm.required"
                            ></v-select>
                        </v-col>

                        <!-- Product Cost -->
                        <v-col md="6">
                            <v-text-field
                                :label="productLabel.cost + ' *'"
                                v-model="productForm.cost"
                                :placeholder="productLabel.cost"
                                :rules="
                                    ruleForm.required.concat(
                                        ruleForm.numberWithDecimal
                                    )
                                "
                                variant="outlined"
                                density="comfortable"
                                hide-details="auto"
                            >
                            </v-text-field>
                        </v-col>

                        <!-- Product Price -->
                        <v-col md="6">
                            <v-text-field
                                :label="productLabel.price + ' *'"
                                v-model="productForm.price"
                                :placeholder="productLabel.price"
                                :rules="
                                    ruleForm.required.concat(
                                        ruleForm.numberWithDecimal
                                    )
                                "
                                variant="outlined"
                                density="comfortable"
                                hide-details="auto"
                            >
                            </v-text-field>
                        </v-col>

                        <!-- Unit Product -->
                        <v-col md="6">
                            <v-select
                                @update:modelValue="Selected_Unit"
                                v-model="productForm.unit_id"
                                :items="units"
                                :label="productLabel.unit_id"
                                item-title="title"
                                item-value="value"
                                variant="outlined"
                                density="comfortable"
                                hide-details="auto"
                                clearable
                                :rules="ruleForm.required"
                            ></v-select>
                        </v-col>

                        <!-- Unit Sale -->
                        <v-col md="6">
                            <v-select
                                v-model="productForm.unit_sale_id"
                                :items="
                                    units_sub.map((units_sub) => ({
                                        title: units_sub.name,
                                        value: units_sub.id,
                                    }))
                                "
                                :label="productLabel.unit_sale_id"
                                item-title="title"
                                item-value="value"
                                variant="outlined"
                                density="comfortable"
                                hide-details="auto"
                                clearable
                                :rules="ruleForm.required"
                            ></v-select>
                        </v-col>
                        <!---->
                        <!-- Unit Purchase -->
                        <v-col md="6">
                            <v-select
                                v-model="productForm.unit_purchase_id"
                                :items="
                                    units_sub.map((units_sub) => ({
                                        title: units_sub.name,
                                        value: units_sub.id,
                                    }))
                                "
                                :label="productLabel.unit_purchase_id"
                                item-title="title"
                                item-value="value"
                                variant="outlined"
                                density="comfortable"
                                hide-details="auto"
                                clearable
                                :rules="ruleForm.required"
                            ></v-select>
                        </v-col>
                        <!---->
                        <!-- Stock Alert -->
                        <v-col md="6">
                            <v-text-field
                                :label="productLabel.stock_alert"
                                v-model="productForm.stock_alert"
                                :placeholder="productLabel.stock_alert"
                                :rules="ruleForm.numberWithDecimal"
                                variant="outlined"
                                density="comfortable"
                                hide-details="auto"
                            >
                            </v-text-field>
                        </v-col>
                        <!---->

                        <v-col md="12">
                            <v-textarea
                                rows="4"
                                :label="productLabel.note"
                                v-model="productForm.note"
                                :placeholder="productLabel.note"
                                variant="outlined"
                                density="comfortable"
                                hide-details="auto"
                            ></v-textarea>
                        </v-col>
                        <!---->
                        <!-- Multiple Variants -->
                        <v-col md="12">
                            <v-checkbox
                                v-model="productForm.is_variant"
                                :label="productLabel.is_variant"
                                density="comfortable"
                                hide-details="auto"
                            ></v-checkbox>
                        </v-col>
                        <v-col md="12" v-show="productForm.is_variant">
                            <vue3-tags-input
                                :tags="variants"
                                placeholder="+ añadir"
                                @on-tags-changed="onChangeTag"
                            />
                        </v-col>

                        <!-- This_Product_Not_For_Selling -->
                        <v-col md="12">
                            <v-checkbox
                                v-model="productForm.not_selling"
                                :label="productLabel.not_selling"
                                density="comfortable"
                                hide-details="auto"
                            ></v-checkbox>
                        </v-col>
                    </v-row>
                    <v-row>
                        <v-col md="12" class="mt-3">
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
