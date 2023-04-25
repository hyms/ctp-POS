<script setup>
import { onMounted, ref } from "vue";
import { router } from "@inertiajs/vue3";

const props = defineProps({
    filter_form: Object,
    sales_types: Object,
    customers: Object,
});

const form = ref({
    start_date: "",
    end_date: "",
    sale_ref: "",
    client: "",
    sale_type: "",
    statut: "",
    status_payment: "",
});
const formLabel = ref({
    start_date: "Fecha desde",
    end_date: "Fecha hasta",
    sale_ref: "Codigo Orden",
    client: "Cliente",
    sale_type: "Tipo de orden",
    statut: "Estado",
    status_payment: "Estado de pago",
});

const menu = ref(false);

onMounted(() => {
    form.value = props.filter_form;
});

function search() {
    router.get(
        "/sales",
        {
            filter: {
                start_date: form.value.start_date,
                end_date: form.value.end_date,
                sale_ref: form.value.sale_ref,
                client: form.value.client,
                sale_type: form.value.sale_type,
                statut: form.value.statut,
                status_payment: form.value.status_payment,
            },
        },
        { replace: true }
    );
    // axios.get("/sales", {
    //     filter: form.value,
    // });
}
</script>
<template>
    <v-menu v-model="menu" :close-on-content-click="false" location="bottom">
        <template v-slot:activator="{ props }">
            <v-btn
                color="primary"
                variant="outlined"
                size="small"
                elevation="1"
                class="mr-2 my-1"
                v-bind="props"
                append-icon="mdi-magnify"
            >
                Filtros
            </v-btn>
        </template>

        <v-card max-width="500">
            <v-card-text>
                <v-form>
                    <v-row>
                        <v-col cols="12" sm="6">
                            <v-text-field
                                v-model="form.start_date"
                                variant="outlined"
                                density="compact"
                                clearable
                                hide-details="auto"
                                type="date"
                                :label="formLabel.start_date"
                            ></v-text-field>
                        </v-col>
                        <v-col cols="12" sm="6">
                            <v-text-field
                                v-model="form.end_date"
                                variant="outlined"
                                density="compact"
                                clearable
                                hide-details="auto"
                                type="date"
                                :label="formLabel.end_date"
                            ></v-text-field>
                        </v-col>
                        <v-col cols="12" sm="6">
                            <v-text-field
                                v-model="form.sale_ref"
                                variant="outlined"
                                density="compact"
                                clearable
                                hide-details="auto"
                                type="text"
                                :label="formLabel.sale_ref"
                            ></v-text-field>
                        </v-col>
                        <v-col cols="12" sm="6">
                            <v-autocomplete
                                v-model="form.client"
                                item-title="name"
                                item-value="id"
                                variant="outlined"
                                density="compact"
                                clearable
                                hide-details="auto"
                                :items="customers"
                                :label="formLabel.client"
                            ></v-autocomplete>
                        </v-col>
                        <v-col cols="12" sm="6">
                            <v-select
                                v-model="form.sale_type"
                                item-title="name"
                                item-value="id"
                                variant="outlined"
                                density="compact"
                                clearable
                                hide-details="auto"
                                :items="sales_types"
                                :label="formLabel.sale_type"
                            ></v-select>
                        </v-col>
                        <v-col cols="12" sm="6">
                            <v-select
                                v-model="form.statut"
                                variant="outlined"
                                density="compact"
                                clearable
                                hide-details="auto"
                                :items="[
                                    { title: 'Completado', value: 'completed' },
                                    { title: 'Pendiente', value: 'pending' },
                                    { title: 'ordenado', value: 'ordered' },
                                ]"
                                :label="formLabel.statut"
                            ></v-select>
                        </v-col>
                        <v-col cols="12" sm="6">
                            <v-select
                                v-model="form.status_payment"
                                item-text="text"
                                item-value="value"
                                variant="outlined"
                                density="compact"
                                clearable
                                hide-details="auto"
                                :items="[
                                    { title: 'Pagado', value: 'paid' },
                                    { title: 'Deuda', value: 'unpaid' },
                                    { title: 'Parcial', value: 'partial' },
                                ]"
                                :label="formLabel.status_payment"
                            ></v-select>
                        </v-col>
                    </v-row>
                </v-form>
            </v-card-text>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn
                    variant="text"
                    size="small"
                    color="error"
                    @click="menu = false"
                >
                    Cancelar
                </v-btn>
                <v-btn
                    variant="tonal"
                    size="small"
                    color="primary"
                    @click="search"
                >
                    Buscar
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-menu>
</template>
