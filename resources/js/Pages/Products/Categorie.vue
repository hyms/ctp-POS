<script setup>
import { onMounted, ref } from "vue";
import Layout from "@/Layouts/Authenticated.vue";
import { api, globals, labels, rules } from "@/helpers";
import DeleteDialog from "@/Components/dialogs/DeleteDialog.vue";
import Snackbar from "@/Components/snackbar.vue";

const props = defineProps({
    errors: Object,
});

//declare variable
const form = ref(null);
const categories = ref([]);
const search = ref("");
const loading = ref(false);
const snackbar = ref({
    view: false,
    color: "",
    text: "",
});
const dialog = ref(false);
const dialogDelete = ref(false);
const editmode = ref(false);

const fields = ref([
    { title: labels.category.name, key: "name" },
    { title: labels.category.code, key: "code" },
    { title: labels.actions, key: "actions" },
]);
const category = ref({
    id: "",
    name: "",
    code: "",
});

//------------- Submit Validation Create & Edit Category
async function Submit_Category() {
    const validate = await form.value.validate();
    if (validate.valid)
        if (!editmode.value) {
            Create_Category();
        } else {
            Update_Category();
        }
}

//------------------------------ Modal  (create category) -------------------------------\\
function New_category() {
    reset_Form();
    editmode.value = false;
    dialog.value = true;
}

function onClose() {
    dialog.value = false;
    reset_Form();
}

//------------------------------ Modal (Update category) -------------------------------\\
function Edit_category(item) {
    reset_Form();
    category.value = item;
    editmode.value = true;
    dialog.value = true;
}

//----------------------------------Create new Category ----------------\\
function Create_Category() {
    api.post({
        url: "/products/categories/",
        params: {
            name: category.value.name,
            code: category.value.code,
        },
        snackbar,
        loadingItem: loading,
        onSuccess: () => {
            snackbar.value.text = labels.success_message;
            loadData();
            dialog.value = false;
        },
    });
}

//---------------------------------- Update Category ----------------\\
function Update_Category() {
    api.put({
        url: "/products/categories/" + category.value.id,
        params: {
            name: category.value.name,
            code: category.value.code,
        },
        snackbar,
        loadingItem: loading,
        onSuccess: () => {
            snackbar.value.text = labels.success_message;
            loadData();
            dialog.value = false;
        },
    });
}

//--------------------------- reset Form ----------------\\

function reset_Form() {
    category.value = {
        id: "",
        name: "",
        code: "",
    };
}

//---------------------- delete modal  ------------------------------\\
function Delete_Category(item) {
    reset_Form();
    category.value = item;
    dialogDelete.value = true;
}

function onCloseDelete() {
    reset_Form();
    dialogDelete.value = false;
}

//--------------------------- Remove Category----------------\\
function Remove_Category() {
    api.delete({
        url: "/products/categories/" + category.value.id,
        snackbar,
        loadingItem: loading,
        onSuccess: () => {
            snackbar.value.text = labels.success_message;
            loadData();
            dialogDelete.value = false;
        },
    });
}
function loadData() {
    api.get({
        url: "/products/categories/list",
        snackbar,
        loadingItem: loading,
        onSuccess: (data) => {
            categories.value = data.categories;
        },
    });
}

onMounted(() => {
    loadData();
});
</script>
<template>
    <Layout>
        <snackbar
            v-model="snackbar.view"
            :text="snackbar.text"
            :color="snackbar.color"
        ></snackbar>
        <delete-dialog
            v-model="dialogDelete"
            :on-save="Remove_Category"
            :on-close="onCloseDelete"
        >
        </delete-dialog>
        <v-dialog
            v-model="dialog"
            max-width="400px"
            scrollable
            @update:modelValue="dialog === false ? reset_Form() : dialog"
        >
            <v-card>
                <v-toolbar
                    border
                    :title="(editmode ? 'Modificar' : 'Nueva') + ' Categoria'"
                >
                </v-toolbar>
                <v-form ref="form">
                    <v-card-text>
                        <v-row>
                            <!-- Code category -->
                            <v-col cols="12">
                                <v-text-field
                                    :label="labels.category.code + ' *'"
                                    v-model="category.code"
                                    :placeholder="labels.category.code"
                                    :rules="rules.required"
                                    hide-details="auto"
                                >
                                </v-text-field>
                            </v-col>

                            <!-- Name category -->
                            <v-col cols="12">
                                <v-text-field
                                    :label="labels.category.name + ' *'"
                                    v-model="category.name"
                                    :placeholder="labels.category.name"
                                    :rules="rules.required"
                                    hide-details="auto"
                                >
                                </v-text-field>
                            </v-col>
                        </v-row>
                    </v-card-text>

                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn
                            variant="outlined"
                            color="error"
                            class="ma-1"
                            @click="onClose"
                        >
                            {{ labels.cancel }}
                        </v-btn>
                        <v-btn
                            color="primary"
                            variant="elevated"
                            class="ma-1"
                            @click="Submit_Category"
                            :loading="loading"
                            :disabled="loading"
                        >
                            {{ labels.submit }}
                        </v-btn>
                    </v-card-actions>
                </v-form>
            </v-card>
        </v-dialog>

        <v-row align="center">
            <v-col cols="12" sm="6">
                <v-text-field
                    v-model="search"
                    prepend-icon="fas fa-search"
                    hide-details
                    :label="labels.search"
                    single-line
                    variant="underlined"
                ></v-text-field>
            </v-col>
            <v-col cols="12" sm="6" class="text-right">
                <v-btn
                    v-if="globals.userPermision(['category'])"
                    color="primary"
                    class="ma-1"
                    prepend-icon="fas fa-plus"
                    @click="New_category"
                >
                    {{ labels.add }}
                </v-btn>
            </v-col>
        </v-row>
        <v-row>
            <v-col cols="12">
                <v-data-table
                    :headers="fields"
                    :items="categories"
                    :search="search"
                    hover
                    class="elevation-2"
                    :no-data-text="labels.no_data_table"
                    :loading="loading"
                    loading-text="Cargando..."
                >
                    <template v-slot:item.actions="{ item }">
                        <v-btn
                            class="ma-1"
                            color="primary"
                            icon="fas fa-pen"
                            size="x-small"
                            variant="outlined"
                            @click="Edit_category(item)"
                        >
                        </v-btn>
                        <v-btn
                            class="ma-1"
                            color="error"
                            icon="fas fa-trash"
                            size="x-small"
                            variant="outlined"
                            @click="Delete_Category(item)"
                        >
                        </v-btn>
                    </template>
                </v-data-table>
            </v-col>
        </v-row>
    </Layout>
</template>
