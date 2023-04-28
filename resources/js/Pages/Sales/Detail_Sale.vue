<script setup>
import { ref } from "vue";
import Layout from "@/Layouts/Authenticated.vue";
import Snackbar from "@/Components/snackbar.vue";
import printJS from "print-js";
import rules from "@/rules";

const props = defineProps({
    details: Object,
    sale: Object,
    company: Object,
});

const loading = ref(false);
const snackbar = ref(false);
const snackbarText = ref("");
const snackbarColor = ref("info");

const variants = ref([]);

//----------------------------------- Invoice Sale PDF  -------------------------\\
function Sale_PDF(id) {
    loading.value = true;

    axios
        .get(`/sale_pdf/${id}`, {
            responseType: "blob", // important
            headers: {
                "Content-Type": "application/json",
            },
        })
        .then((response) => {
            const url = window.URL.createObjectURL(new Blob([response.data]));
            const link = document.createElement("a");
            link.href = url;
            link.setAttribute("download", "Sale_" + props.sale.Ref + ".pdf");
            document.body.appendChild(link);
            link.click();
            // Complete the animation of the  progress bar.
        })
        .catch(() => {
            // Complete the animation of the  progress bar.
        })
        .finally(() => {
            loading.value = false;
        });
}

//------------------------------ Print -------------------------\\
function print() {
    printJS("print_Invoice", "html");
}

//------------------------------------------ DELETE Sale ------------------------------\\
function Delete_Sale() {}
</script>
<template>
    <Layout :loading="loading">
        <snackbar
            v-model="snackbar"
            :snackbarColor="snackbarColor"
            :snackbarText="snackbarText"
        >
        </snackbar>

        <v-row>
            <v-col cols="12" class="mb-5">
                <v-btn
                    class="mr-1"
                    v-if="sale.sale_has_return == 'no'"
                    color="success"
                    size="small"
                    :to="{
                        name: 'edit_sale',
                        params: { id: sale.id },
                    }"
                    prepend-icon="mdi-pencil-box-outline"
                >
                    <span>Editar Venta</span>
                </v-btn>
                <v-btn
                    class="mr-1"
                    @click="Sale_PDF()"
                    color="primary"
                    size="small"
                    prepend-icon="mdi-file-pdf-box"
                >
                    <i class="i-File-TXT"></i>
                    PDF
                </v-btn>
                <v-btn
                    class="mr-1"
                    @click="print()"
                    color="warning"
                    size="small"
                    prepend-icon="mdi-printer"
                >
                    Imprimir
                </v-btn>
                <v-btn
                    class="mr-1"
                    v-if="sale.sale_has_return == 'no'"
                    @click="Delete_Sale()"
                    color="error"
                    size="small"
                    prepend-icon="mdi-close-circle"
                >
                    Borrar
                </v-btn>
            </v-col>
        </v-row>
        <v-card>
            <v-card-text class="ma-4">
                <div class="invoice" id="print_Invoice">
                    <div class="invoice-print">
                        <v-row class="justify-content-md-center">
                            <v-col cols="12 text-center mb-3">
                                <p class="font-weight-bold text-h5">
                                    Detalle de Venta : {{ sale.Ref }}
                                </p>
                            </v-col>
                        </v-row>
                        <v-divider></v-divider>
                        <v-row class="mt-5">
                            <v-col lg="4" md="4" cols="12" class="mb-4">
                                <p class="font-weight-bold text-h6">
                                    Información del Cliente
                                </p>
                                <div>{{ sale.client_name }}</div>
                                <div>{{ sale.client_email }}</div>
                                <div>{{ sale.client_phone }}</div>
                                <div>{{ sale.client_adr }}</div>
                            </v-col>
                            <v-col
                                lg="4"
                                md="4"
                                cols="12"
                                class="mb-4"
                                v-if="company"
                            >
                                <p class="font-weight-bold text-h6">
                                    Información de la Empresa
                                </p>
                                <div>{{ company.CompanyName }}</div>
                                <div>{{ company.email }}</div>
                                <div>{{ company.CompanyPhone }}</div>
                                <div>{{ company.CompanyAdress }}</div>
                            </v-col>
                            <v-col lg="4" md="4" cols="12" class="mb-4">
                                <p class="font-weight-bold text-h6">
                                    Información de la Orden
                                </p>
                                <div>Codigo : {{ sale.Ref }}</div>
                                <div>
                                    Estado de Pago :
                                    <v-chip
                                        v-if="sale.payment_status == 'paid'"
                                        color="success"
                                        variant="outlined"
                                        size="small"
                                        density="compact"
                                        >Pagado</v-chip
                                    >
                                    <v-chip
                                        v-else-if="
                                            sale.payment_status == 'partial'
                                        "
                                        color="primary"
                                        variant="outlined"
                                        size="small"
                                        density="compact"
                                        >Parcial</v-chip
                                    >
                                    <v-chip
                                        v-else
                                        color="error"
                                        variant="outlined"
                                        size="small"
                                        density="compact"
                                        >Sin Pagar</v-chip
                                    >
                                </div>
                                <div>Agencia : {{ sale.warehouse }}</div>
                                <div>
                                    Estado :
                                    <v-chip
                                        v-if="sale.statut == 'completed'"
                                        color="success"
                                        variant="outlined"
                                        size="small"
                                        density="compact"
                                        >Completo</v-chip
                                    >
                                    <v-chip
                                        v-else-if="sale.statut == 'pending'"
                                        color="info"
                                        variant="outlined"
                                        size="small"
                                        density="compact"
                                        >Pendiente</v-chip
                                    >
                                    <v-chip
                                        v-else
                                        color="warning"
                                        variant="outlined"
                                        size="small"
                                        density="compact"
                                        >Ordenado</v-chip
                                    >
                                </div>
                            </v-col>
                        </v-row>
                        <v-row class="mt-3">
                            <v-col cols="12">
                                <p class="font-weight-bold text-h6">
                                    Resumen de la Orden
                                </p>
                                <div class="table-responsive">
                                    <v-table density="compact" hover>
                                        <thead class="bg-gray-300">
                                            <tr>
                                                <th>Producto</th>
                                                <th>Precio x Unidad</th>
                                                <th>Cantidad</th>
                                                <th>Precio unitario</th>
                                                <th>SubTotal</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="detail in details">
                                                <td>
                                                    <span
                                                        >{{ detail.code }} ({{
                                                            detail.name
                                                        }})</span
                                                    >
                                                </td>
                                                <td>
                                                    Bs
                                                    {{
                                                        rules.formatNumber(
                                                            detail.Net_price,
                                                            3
                                                        )
                                                    }}
                                                </td>
                                                <td>
                                                    {{
                                                        rules.formatNumber(
                                                            detail.quantity,
                                                            2
                                                        )
                                                    }}
                                                    {{ detail.unit_sale }}
                                                </td>
                                                <td>
                                                    Bs
                                                    {{
                                                        rules.formatNumber(
                                                            detail.price,
                                                            2
                                                        )
                                                    }}
                                                </td>
                                                <td>
                                                    Bs
                                                    {{
                                                        detail.total.toFixed(2)
                                                    }}
                                                </td>
                                            </tr>
                                        </tbody>
                                    </v-table>
                                </div>
                            </v-col>
                        </v-row>
                        <v-row class="mt-4">
                            <v-col md="3" offset-md="9">
                                <v-table density="compact" hover>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <span class="font-weight-bold"
                                                    >Total</span
                                                >
                                            </td>
                                            <td>
                                                <span class="font-weight-bold"
                                                    >Bs
                                                    {{ sale.GrandTotal }}</span
                                                >
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <span class="font-weight-bold"
                                                    >Pagado</span
                                                >
                                            </td>
                                            <td>
                                                <span class="font-weight-bold"
                                                    >Bs
                                                    {{ sale.paid_amount }}</span
                                                >
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <span class="font-weight-bold"
                                                    >Deuda</span
                                                >
                                            </td>
                                            <td>
                                                <span class="font-weight-bold"
                                                    >Bs {{ sale.due }}</span
                                                >
                                            </td>
                                        </tr>
                                    </tbody>
                                </v-table>
                            </v-col>
                        </v-row>
                        <v-divider v-if="sale.note"></v-divider>
                        <v-row class="mt-4">
                            <v-col cols="12">
                                <p>Nota : {{ sale.note }}</p>
                            </v-col>
                        </v-row>
                    </div>
                </div>
            </v-card-text>
        </v-card>
    </Layout>
</template>
