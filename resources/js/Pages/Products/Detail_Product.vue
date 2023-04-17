<script setup>
import { ref } from "vue";
import Layout from "@/Layouts/Authenticated.vue";
import Snackbar from "@/Components/snackbar.vue";
import printJS from "print-js";

defineProps({
    product: Object,
});

const loading = ref(false);
const snackbar = ref(false);
const snackbarText = ref("");
const snackbarColor = ref("info");

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

//------- printproduct
function print_product() {
    printJS("print_product", "html");
}
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
            <v-toolbar>
                <v-btn
                    @click="print_product()"
                    variant="outlined"
                    color="primary"
                    prepend-icon="mdi-printer"
                    >Imprimir</v-btn
                >
            </v-toolbar>
            <v-card-text>
                <v-row id="print_product">
                    <v-col>
                        <v-row>
                            <v-col md="8">
                                <v-table hover density="compact">
                                    <tbody>
                                        <tr>
                                            <td>Codigo</td>
                                            <td class="font-weight-bold">
                                                {{ product.code }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Nombre</td>
                                            <td class="font-weight-bold">
                                                {{ product.name }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Categoria</td>
                                            <td class="font-weight-bold">
                                                {{ product.category }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Costo</td>
                                            <td class="font-weight-bold">
                                                Bs
                                                {{
                                                    formatNumber(
                                                        product.cost,
                                                        2
                                                    )
                                                }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Precio</td>
                                            <td class="font-weight-bold">
                                                Bs
                                                {{
                                                    formatNumber(
                                                        product.price,
                                                        2
                                                    )
                                                }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Unidad</td>
                                            <th>{{ product.unit }}</th>
                                        </tr>
                                        <!--                                        <tr>-->
                                        <!--                                            <td>Facturar</td>-->
                                        <!--                                            <td class="font-weight-bold">-->
                                        <!--                                                {{-->
                                        <!--                                                    formatNumber(-->
                                        <!--                                                        product.taxe,-->
                                        <!--                                                        2-->
                                        <!--                                                    )-->
                                        <!--                                                }}-->
                                        <!--                                                %-->
                                        <!--                                            </td>-->
                                        <!--                                        </tr>-->
                                        <tr v-if="product.taxe != '0.00'">
                                            <td>Metodo de factura</td>
                                            <td class="font-weight-bold">
                                                {{ product.tax_method }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Alerta de Existencias</td>
                                            <td class="font-weight-bold">
                                                <span
                                                    class="badge badge-outline-warning"
                                                    >{{
                                                        formatNumber(
                                                            product.stock_alert,
                                                            2
                                                        )
                                                    }}</span
                                                >
                                            </td>
                                        </tr>
                                        <tr v-if="product.is_variant == 'yes'">
                                            <td>Variante de Producto</td>
                                            <td class="font-weight-bold">
                                                <span
                                                    v-for="variant in product.ProductVariant"
                                                    >{{ variant }},</span
                                                >
                                            </td>
                                        </tr>
                                    </tbody>
                                </v-table>
                            </v-col>
                        </v-row>
                        <v-row class="mt-4">
                            <!-- Warehouse Quantity -->
                            <v-col md="5">
                                <v-table hover density="compact">
                                    <thead>
                                        <tr>
                                            <th>Agencia</th>
                                            <th>Cantidad</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="prod_w in product.CountQTY">
                                            <td>{{ prod_w.mag }}</td>
                                            <td>
                                                {{
                                                    formatNumber(prod_w.qte, 2)
                                                }}
                                                {{ product.unit }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </v-table>
                            </v-col>
                            <!-- Warehouse Variants Quantity -->
                            <v-col md="7" v-if="product.is_variant == 'yes'">
                                <v-table density="compact" hover>
                                    <thead>
                                        <tr>
                                            <th>Agencia</th>
                                            <th>Variante</th>
                                            <th>Cantidad</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr
                                            v-for="prod_v in product.CountQTY_variants"
                                        >
                                            <td>{{ prod_v.mag }}</td>
                                            <td>{{ prod_v.variant }}</td>
                                            <td>
                                                {{
                                                    formatNumber(prod_v.qte, 2)
                                                }}
                                                {{ product.unit }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </v-table>
                            </v-col>
                        </v-row>
                    </v-col>
                </v-row>
                <hr v-if="product.note" class="mt-4" />
                <v-row class="mt-4">
                    <v-col md="12">
                        <p>{{ product.note }}</p>
                    </v-col>
                </v-row>
            </v-card-text>
        </v-card>
    </Layout>
</template>
