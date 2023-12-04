<script setup>
import Layout from "@/Layouts/Authenticated.vue";
import { onMounted, ref } from "vue";
import { api, globals, labelsNew } from "@/helpers";
import DialogDelete from "@/Components/dialogs/DeleteDialog.vue";
import Snackbar from "@/Components/snackbar.vue";
import { router } from "@inertiajs/vue3";

const snackbar = ref({ view: false, color: "", text: "" });

const props = defineProps({
    errors: Object,
});

const loadingTable = ref(false);
const search = ref("");
const dialogDelete = ref(false);
const itemId = ref(null);
const items = ref([]);
const fields = ref([
    { title: labelsNew.RoleName, key: "name" },
    { title: labelsNew.Description, key: "description" },
    { title: labelsNew.Action, key: "action" },
]);

function showForm(id = null) {
    if (id == null) {
        router.visit("/roles-create", {
            method: "get",
        });
    } else {
        router.visit("/roles-edit", {
            method: "get",
            params: { id },
        });
    }
}

function showDelete(id) {
    itemId.value = id;
    dialogDelete.value = true;
}

function deleteItem() {
    api.delete({
        url: "/roles/" + itemId.value,
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
        url: "/roles-data",
        snackbar,
        loadingItem: loadingTable,
        onSuccess: (data) => {
            items.value = data["data"];
        },
    });
}

onMounted(() => {
    loadTable();
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
                    <v-btn
                        v-if="globals.userPermision(['permissions_add'])"
                        variant="flat"
                        color="primary"
                        @click="showForm()"
                        >{{ labelsNew.Add }}
                    </v-btn>
                </v-col>
            </v-row>
            <v-data-table
                :headers="fields"
                :items="items"
                :search="search"
                :loading="loadingTable"
            >
                <template v-slot:item.action="{ item }">
                    <v-btn
                        v-if="globals.userPermision(['permissions_edit'])"
                        color="primary"
                        class="rounded mx-1"
                        @click="showForm(item.id)"
                        icon="fas fa-edit"
                        size="small"
                        variant="tonal"
                    ></v-btn>
                    <v-btn
                        v-if="globals.userPermision(['permissions_delete'])"
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
