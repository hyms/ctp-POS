<script setup>
import Layout from "@/Layouts/Authenticated.vue";
import { onMounted, ref } from "vue";
import { api, labels, labelsNew } from "@/helpers";
import DialogDelete from "@/Components/dialogs/DeleteDialog.vue";
// import DialogForm from "./form.vue";
import Snackbar from "@/Components/snackbar.vue";

const snackbar = ref({ view: false, color: "", text: "" });

const props = defineProps({
    errors: Object,
});

const loadingTable = ref(false);
const search = ref("");
const dialogForm = ref(false);
const dialogDelete = ref(false);
const itemId = ref(null);
const items = ref([]);
const fields = ref([{ title: labels.user.full_name, key: "full_name" }]);

function showForm(id = null) {
    itemId.value = id;
    dialogForm.value = true;
}

function showDelete(id) {
    itemId.value = id;
    dialogDelete.value = true;
}

function deleteItem() {
    api.delete({
        url: "/admin/items/" + itemId.value,
        snackbar,
        loadingItem: loadingTable,
        onSuccess: (data) => {
            loadTable();
            dialogDelete.value = false;
        },
    });
}

function loadTable() {
    api.get({
        url: "/admin/items-data",
        snackbar,
        loadingItem: loadingTable,
        onSuccess: (data) => {
            items.value = data["data"];
        },
    });
}

onMounted(() => {
    // loadTable();
});
</script>
<template>
    <Layout title-page="">
        <snackbar
            :text="snackbar.text"
            v-model="snackbar.view"
            :color="snackbar.color"
        ></snackbar>
        <dialog-delete
            v-model="dialogDelete"
            :on-save="deleteItem"
        ></dialog-delete>
        <!--        <dialog-form-->
        <!--            v-model="dialogForm"-->
        <!--            :id="itemId"-->
        <!--            @loadTable="loadTable"-->
        <!--        ></dialog-form>-->
        <v-card class="pa-3">
            <v-row align="center">
                <v-col cols="12" sm="4">
                    <v-text-field
                        v-model="search"
                        prepend-icon="fas fa-search"
                        hide-details
                        :label="labelsNew.Search_this_table"
                        single-line
                        variant="underlined"
                    ></v-text-field>
                </v-col>
                <v-spacer></v-spacer>
                <v-col cols="12" sm="auto">
                    <v-btn variant="flat" color="primary" @click="showForm()"
                        >{{ labelsNew.New_Customer }}
                    </v-btn>
                </v-col>
            </v-row>
            <v-data-table
                :headers="fields"
                :items="items"
                :search="search"
                :loading="loadingTable"
            >
                <template v-slot:item.full_name="{ item }">
                    <v-btn
                        color="primary"
                        variant="text"
                        density="compact"
                        @click="showView(item.id)"
                        >{{ item.full_name }}
                    </v-btn>
                    <h6 class="text-subtitle-2">{{ item.email }}</h6>
                </template>
                <template v-slot:item.action="{ item }">
                    <v-btn
                        class="rounded mx-1"
                        icon="fas fa-pen"
                        size="small"
                        color="primary"
                        variant="tonal"
                        @click="showForm(item.id)"
                    ></v-btn>
                    <v-btn
                        class="rounded mx-1"
                        icon="fas fa-trash"
                        size="small"
                        color="error"
                        variant="tonal"
                        @click="showDelete(item.id)"
                    ></v-btn>
                </template>
            </v-data-table>
        </v-card>
    </Layout>
</template>
