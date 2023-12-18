<script setup>
import { computed, ref } from "vue";
import Layout from "@/Layouts/Authenticated.vue";
import ExportBtn from "@/Components/buttons/ExportBtn.vue";
import { usePage } from "@inertiajs/vue3";
import { labels } from "@/helpers";

const props = defineProps({
    customers: Object,
});
const search = ref("");
const currency = computed(() => usePage().props.currency);

const fields = ref([
    { title: labels.client.name, key: "name" },
    { title: labels.client.phone, key: "phone" },
    { title: labels.client.email, key: "email" },
    { title: "Ventas Totales", key: "total_sales" },
    { title: "Monto Total", key: "total" },
]);
let jsonFields = ref({
    Nombre: "name",
    Telefono: "phone",
    Email: "email",
    "Total Ventas": "total_sales",
    "Total Monto": "total",
});
</script>
<template>
    <layout>
        <v-row align="center" class="mb-3">
            <v-col cols="12" sm="4">
                <v-text-field
                    v-model="search"
                    prepend-icon="fas fa-search"
                    hide-details
                    :label="labels.search"
                    single-line
                    variant="underlined"
                ></v-text-field>
            </v-col>
            <v-spacer></v-spacer>
            <v-col cols="auto" class="text-right">
                <ExportBtn
                    :data="customers"
                    :fields="jsonFields"
                    name-file="Top Clientes"
                ></ExportBtn>
            </v-col>
        </v-row>
        <v-card>
            <v-data-table
                :headers="fields"
                :items="customers"
                :search="search"
                hover
                :no-data-text="labels.no_data_table"
                class="text-center"
            >
                <template v-slot:item.total="{ item }">
                    <span>{{ currency }} {{ item.total }}</span>
                </template>
            </v-data-table>
        </v-card>
    </layout>
</template>
