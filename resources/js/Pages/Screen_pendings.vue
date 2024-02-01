<script setup>
import { onMounted, ref } from "vue";
import Layout from "@/Layouts/Guest.vue";
import { api } from "@/helpers";
import Snackbar from "@/Components/snackbar.vue";
import DeleteDialog from "@/Components/dialogs/DeleteDialog.vue";

const props = defineProps({
    error: Object,
});
const snackbar = ref({ view: false, color: "", text: "" });
const loading = ref(false);
const items = ref([]);
const items_p = ref([]);
const sale = ref({});
const dialogShow = ref(false);

function getData() {
    api.get({
        url: "/screen/list",
        loadingItem: loading,
        snackbar,
        onSuccess: (data) => {
            // console.log(data);
            items.value = data.sales;
            items_p.value = data.sales_p;
        },
    });
}
function showDialog(item) {
    sale.value = item;
    dialogShow.value = true;
}
function update_completed() {
    api.put({
        url: "/sales/statut/" + sale.value.id,
        params: {
            statut: "completed",
        },
        loadingItem: loading,
        snackbar,
        onSuccess: () => {
            snackbar.value.text = "Actualizacion exitosa";
            getData();
            dialogShow.value = false;
        },
    });
}
onMounted(() => {
    getData();
    setInterval(getData, 30000);
});
</script>
<template>
    <Layout>
        <snackbar
            :text="snackbar.text"
            v-model="snackbar.view"
            :color="snackbar.color"
        ></snackbar>
        <delete-dialog
            v-model="dialogShow"
            :on-save="update_completed"
            text="Estas Seguro de Completar la Orden?"
        ></delete-dialog>
        <v-row>
            <v-col cols="12">
                <v-card :loading="loading">
                    <v-toolbar color="info">
                        <v-toolbar-title class="font-weight-bold text-center"
                            >En cola</v-toolbar-title
                        >
                    </v-toolbar>
                    <v-card-text>
                        <v-data-iterator :items="items_p" items-per-page="42">
                            <template v-slot:default="{ items }">
                                <v-slide-y-transition
                                    group
                                    tag="div"
                                    class="v-row"
                                >
                                    <template
                                        v-for="(item, i) in items"
                                        :key="i"
                                    >
                                        <v-col cols="12" md="3" lg="2">
                                            <v-card
                                                color="info"
                                                @click="showDialog(item.raw)"
                                            >
                                                <v-card-text>
                                                    <div
                                                        class="text-h4 font-weight-regular"
                                                    >
                                                        {{
                                                            item.raw.client_name
                                                        }}
                                                    </div>
                                                    <v-divider />
                                                    <div
                                                        class="text-h5 font-weight-regular"
                                                    >
                                                        Orden:
                                                        {{ item.raw.Ref }}
                                                        <br />
                                                        Trabajo
                                                        {{ item.raw.detail }}
                                                    </div>
                                                </v-card-text>
                                            </v-card>
                                        </v-col>
                                    </template>
                                </v-slide-y-transition>
                            </template>
                        </v-data-iterator>
                    </v-card-text>
                </v-card>
            </v-col>
        </v-row>
    </Layout>
</template>
