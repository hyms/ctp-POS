<script setup>
import { computed, ref } from "vue";
import Layout from "@/Layouts/Authenticated.vue";
import { router, usePage } from "@inertiajs/vue3";
import labels from "@/labels";

const props = defineProps({
    products: Object,
});
const loading = ref(false);
const search = ref("");

const filterForm = ref({
    from: "",
    to: "",
});
const currency = computed(() => usePage().props.currency);

const fields = ref([
    { title: labels.product.code, key: "code" },
    { title: labels.product.name, key: "name" },
    { title: "Vendidos (Total)", key: "total_sales" },
    { title: "Monto Total", key: "total" },
]);

//----------------------------- Get_top_products------------------\\
function Get_top_products(page = 1) {
    router.get(
        "/report/top_products",
        {
            page: page,
            limit: "",
            to: filterForm.value.to,
            from: filterForm.value.from,
            search: "",
        },
        {
            only: [props.products],
            onStart: () => {
                loading.value = true;
            },
            onFinish: (visit) => {
                loading.value = false;
            },
        }
    );
}
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
            <v-col cols="12" sm="6">
                <v-row>
                    <v-col cols="12" sm="6">
                        <v-text-field
                            v-model="filterForm.from"
                            variant="outlined"
                            clearable
                            hide-details="auto"
                            type="date"
                            :label="labels.start_date"
                        ></v-text-field>
                    </v-col>
                    <v-col cols="12" sm="6">
                        <v-text-field
                            v-model="filterForm.to"
                            variant="outlined"
                            clearable
                            hide-details="auto"
                            type="date"
                            :label="labels.end_date"
                        ></v-text-field>
                    </v-col>
                </v-row>
            </v-col>
            <v-col cols="12" sm="2">
                <v-btn
                    type="submit"
                    variant="tonal"
                    size="small"
                    color="primary"
                    @click="Get_top_products"
                >
                    {{ labels.search }}
                </v-btn>
            </v-col>
        </v-row>
        <v-card>
            <v-data-table
                :headers="fields"
                :items="products"
                :search="search"
                hover
                :no-data-text="labels.no_data_table"
                :loading="loading"
                class="text-center"
            >
                <template v-slot:item.total="{ item }">
                    <span>{{ currency }} {{ item.total }}</span>
                </template>
            </v-data-table>
        </v-card>
    </layout>
</template>
