<script setup>
import { onMounted, ref } from "vue";
import Layout from "@/Layouts/Guest.vue";
import { api } from "@/helpers";
import Snackbar from "@/Components/snackbar.vue";

const props = defineProps({
    error: Object,
});
const snackbar = ref({ view: false, color: "", text: "" });
const loading = ref(false);
const items = ref([]);
function getData() {
    api.get({
        url: "/screen/list",
        loadingItem: loading,
        snackbar,
        onSuccess: (data) => {
            items.value = data.sales;
        },
    });
}
onMounted(() => {
    getData();
});
</script>
<template>
    <Layout>
        <snackbar
            :text="snackbar.text"
            v-model="snackbar.view"
            :color="snackbar.color"
        ></snackbar>
        <div class="auth-wrapper">
            <v-card>
                <v-toolbar color="secondary">
                    <v-toolbar-title
                        class="text-h3 font-weight-bold text-center"
                        >Trabajos Finalizados</v-toolbar-title
                    >
                </v-toolbar>
                <v-card-text>
                    <v-data-iterator :items="items" items-per-page="42">
                        <template v-slot:default="{ items }">
                            <v-row>
                                <template v-for="(item, i) in items" :key="i">
                                    <v-col cols="12" lg="2" md="3">
                                        <v-card color="secondary">
                                            <v-card-text>
                                                <div
                                                    class="text-h1 font-weight-regular"
                                                >
                                                    {{ item.raw.Ref }}
                                                </div>
                                                <v-divider />
                                                <div
                                                    class="text-h4 font-weight-regular"
                                                >
                                                    {{ item.raw.client_name }}
                                                </div>
                                            </v-card-text>
                                        </v-card>
                                    </v-col>
                                </template>
                            </v-row>
                        </template>
                    </v-data-iterator>
                </v-card-text>
            </v-card>
        </div>
    </Layout>
</template>

<style lang="scss">
//.auth-wrapper {
//    display: flex;
//    min-height: calc(var(--vh, 1vh) * 100);
//    width: 100%;
//    flex-basis: 100%;
//    align-items: center;
//}
</style>
