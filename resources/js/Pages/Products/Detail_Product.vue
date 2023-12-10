<script setup>
import { ref } from "vue";
import Layout from "@/Layouts/Authenticated.vue";
import printJS from "print-js";
import { globals, helpers, labels } from "@/helpers";

defineProps({
    product: Object,
});
const currency = globals.currency();

const loading = ref(false);

//------- printproduct
function print_product() {
    printJS("print_product", "html");
}
</script>
<template>
    <Layout :loading="loading">
        <v-card>
            <v-toolbar>
                <v-btn
                    @click="print_product()"
                    variant="outlined"
                    color="primary"
                    prepend-icon="fas fa-print"
                    >{{ labels.print }}
                </v-btn>
            </v-toolbar>
            <v-card-text>
                <v-row id="print_product">
                    <v-col>
                        <v-row>
                            <v-col cols="12" md="8">
                                <v-table hover>
                                    <tbody>
                                        <tr>
                                            <td>{{ labels.product.code }}</td>
                                            <td class="font-weight-bold">
                                                {{ product.code }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>{{ labels.product.name }}</td>
                                            <td class="font-weight-bold">
                                                {{ product.name }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                {{ labels.product.category_id }}
                                            </td>
                                            <td class="font-weight-bold">
                                                {{ product.category }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>{{ labels.product.cost }}</td>
                                            <td class="font-weight-bold">
                                                {{ currency }}
                                                {{
                                                    helpers.formatNumber(
                                                        product.cost,
                                                        2
                                                    )
                                                }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>{{ labels.product.price }}</td>
                                            <td class="font-weight-bold">
                                                {{ currency }}
                                                {{
                                                    helpers.formatNumber(
                                                        product.price,
                                                        2
                                                    )
                                                }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                {{ labels.product.unit_id }}
                                            </td>
                                            <th>{{ product.unit }}</th>
                                        </tr>
                                        <!--                                        <tr>-->
                                        <!--                                            <td>Facturar</td>-->
                                        <!--                                            <td class="font-weight-bold">-->
                                        <!--                                                {{-->
                                        <!--                                                    rules.formatNumber(-->
                                        <!--                                                        product.taxe,-->
                                        <!--                                                        2-->
                                        <!--                                                    )-->
                                        <!--                                                }}-->
                                        <!--                                                %-->
                                        <!--                                            </td>-->
                                        <!--                                        </tr>-->
                                        <tr v-if="product.taxe != '0.00'">
                                            <td>
                                                {{ labels.product.tax_method }}
                                            </td>
                                            <td class="font-weight-bold">
                                                {{ product.tax_method }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                {{ labels.product.stock_alert }}
                                            </td>
                                            <td class="font-weight-bold">
                                                <span
                                                    class="badge badge-outline-warning"
                                                    >{{
                                                        helpers.formatNumber(
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
                            <v-col cols="12" md="5">
                                <v-table hover>
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
                                                    helpers.formatNumber(
                                                        prod_w.qty,
                                                        2
                                                    )
                                                }}
                                                {{ product.unit }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </v-table>
                            </v-col>
                            <!-- Warehouse Variants Quantity -->
                            <v-col
                                cols="12"
                                md="7"
                                v-if="product.is_variant == 'yes'"
                            >
                                <v-table hover>
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
                                                    helpers.formatNumber(
                                                        prod_v.qty,
                                                        2
                                                    )
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
                    <v-col cols="12" md="12">
                        <p>{{ product.note }}</p>
                    </v-col>
                </v-row>
            </v-card-text>
        </v-card>
    </Layout>
</template>
