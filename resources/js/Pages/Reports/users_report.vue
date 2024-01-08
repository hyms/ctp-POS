<script setup>
import { onMounted, ref } from "vue";
import Layout from "@/Layouts/Authenticated.vue";
import { api, helpers, labels, labelsNew } from "@/helpers";
import Snackbar from "@/Components/snackbar.vue";

const props = defineProps({
    errors: Object,
});
const snackbar = ref({
    view: false,
    color: "",
    text: "",
});
const loading = ref(false);
const menu = ref(false);
const search = ref("");
const warehouse_id = ref("");
const users = ref([]);
const user = ref({});

const columns_user = ref([
    { title: labelsNew.username, key: "username" },
    { title: labelsNew.TotalSales, key: "total_sales" },
    // { title: labelsNew.TotalPurchases, key: "total_purchases" },
    // { title: labelsNew.Total_quotations, key: "total_quotations" },
    { title: labelsNew.Total_transfers, key: "total_transfers" },
    { title: labelsNew.Total_adjustments, key: "total_adjustments" },
    { title: labelsNew.Action, key: "actions" },
]);
//--------------------------- Get Customer Report -------------\\

function Get_Users_Report(page) {
    api.get({
        url: "/report/users/list",
        params: {
            page,
            SortField: "",
            SortType: "",
            search: "",
            limit: "",
        },
        loadingItem: loading,
        snackbar,
        onSuccess: (data) => {
            users.value = data.report;
            // this.totalRows = data.totalRows;
        },
    });
} //end Methods
onMounted(() => {
    Get_Users_Report(1);
});
</script>
<template>
    <layout>
        <snackbar
            v-model="snackbar.view"
            :text="snackbar.text"
            :color="snackbar.color"
        ></snackbar>
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
            <!--        <v-col cols="auto" class="text-right">-->
            <!--          <ExportBtn-->
            <!--              :data="report"-->
            <!--              :fields="jsonFields"-->
            <!--              name-file="reporte_stock"-->
            <!--          ></ExportBtn>-->
            <!--        </v-col>-->
        </v-row>
        <v-data-table
            :headers="columns_user"
            :items="users"
            :search="search"
            hover
            :no-data-text="labels.no_data_table"
            :loading="loading"
        >
            <template v-slot:item.actions="{ item }">
                <v-btn
                    class="ma-1"
                    color="primary"
                    icon="fas fa-eye"
                    size="x-small"
                    variant="outlined"
                    @click="helpers.linkVisit('/report/detail_user/' + item.id)"
                >
                </v-btn>
            </template>
        </v-data-table>
    </layout>
</template>
