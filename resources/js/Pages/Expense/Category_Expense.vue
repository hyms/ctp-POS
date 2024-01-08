<script setup>
import { onMounted, ref } from "vue";
import Layout from "@/Layouts/Authenticated.vue";
import { api, globals, labels, rules } from "@/helpers";
import DeleteDialog from "@/Components/dialogs/DeleteDialog.vue";
import Snackbar from "@/Components/snackbar.vue";

const props = defineProps({
    error: Object,
});

const Expenses_category = ref([]);
const form = ref(null);
const search = ref("");
const loading = ref(false);
const snackbar = ref({
    view: false,
    color: "",
    text: "",
});
const editmode = ref(false);
const category = ref({
    id: "",
    name: "",
    description: "",
});

const fields = ref([
    { title: labels.category.name, key: "name" },
    { title: labels.category.description, key: "description" },
    { title: labels.actions, key: "actions" },
]);
const dialog = ref(false);
const dialogDelete = ref(false);

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

//--------------------------Show Modal (new Category) ----------------\\
function New_Category() {
    reset_Form();
    editmode.value = false;
    dialog.value = true;
}

function onClose() {
    dialog.value = false;
    reset_Form();
}

//-------------------------- Show Modal (Edit Category) ----------------\\
function Edit_Category(cat) {
    reset_Form();
    category.value = cat;
    editmode.value = true;
    dialog.value = true;
}

//--------------------------- reset Form ----------------\\
function reset_Form() {
    category.value = {
        id: "",
        name: "",
        description: "",
    };
}

//----------------------------------Create new Category ----------------\\
function Create_Category() {
    api.post({
        url: "/expenses_category",
        params: {
            name: category.value.name,
            description: category.value.description,
        },
        loadingItem: loading,
        snackbar,
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
        url: "/expenses_category/" + category.value.id,
        params: {
            name: category.value.name,
            description: category.value.description,
        },
        loadingItem: loading,
        snackbar,
        onSuccess: () => {
            snackbar.value.text = labels.success_message;
            loadData();
            dialog.value = false;
        },
    });
}

//---------------------- delete modal  ------------------------------\\
function Delete_CategoryExpense(item) {
    reset_Form();
    category.value = item;
    dialogDelete.value = true;
}

function onCloseDelete() {
    reset_Form();
    dialogDelete.value = false;
}

//--------------------------- Delete Category----------------\\
function Delete_Category() {
    api.delete({
        url: "/expenses_category/" + category.value.id,
        loadingItem: loading,
        snackbar,
        onSuccess: () => {
            snackbar.value.text = labels.delete_message;
            loadData();
            dialogDelete.value = false;
        },
    });
}
function loadData() {
    api.get({
        url: "/expenses_category/list",
        snackbar,
        loadingItem: loading,
        onSuccess: (data) => {
            Expenses_category.value = data.Expenses_category;
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
            :on-close="onCloseDelete"
            :on-save="Delete_Category"
        >
        </delete-dialog>
        <v-dialog
            v-model="dialog"
            max-width="400px"
            scrollable
            @update:modelValue="dialog === false ? reset_Form() : dialog"
        >
            <v-card>
                <v-form ref="form">
                    <v-toolbar
                        border
                        :title="
                            (editmode ? 'Modificar' : 'Nueva') +
                            ' Categoria de gasto'
                        "
                    >
                    </v-toolbar>

                    <v-card-text>
                        <v-row>
                            <!-- Name Category -->
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

                            <!-- Description Category -->
                            <v-col cols="12">
                                <v-textarea
                                    :label="labels.category.description"
                                    v-model="category.description"
                                    :placeholder="labels.category.description"
                                    rows="4"
                                    hide-details="auto"
                                >
                                </v-textarea>
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
                            type="submit"
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
                    v-if="globals.userPermision(['expense_category_add'])"
                    color="primary"
                    class="ma-1"
                    prepend-icon="fas fa-user-plus"
                    @click="New_Category"
                >
                    {{ labels.add }}
                </v-btn>
            </v-col>
        </v-row>
        <v-row>
            <v-col cols="12">
                <v-data-table
                    :headers="fields"
                    :items="Expenses_category"
                    :search="search"
                    hover
                    class="elevation-2"
                    :no-data-text="labels.no_data_table"
                    :loading="loading"
                >
                    <template v-slot:item.actions="{ item }">
                        <v-btn
                            v-if="
                                globals.userPermision(['expense_category_edit'])
                            "
                            class="ma-1"
                            color="primary"
                            icon="fas fa-pen"
                            size="x-small"
                            variant="outlined"
                            @click="Edit_Category(item)"
                        >
                        </v-btn>
                        <v-btn
                            v-if="
                                globals.userPermision([
                                    'expense_category_delete',
                                ])
                            "
                            class="ma-1"
                            color="error"
                            icon="fas fa-trash"
                            size="x-small"
                            variant="outlined"
                            @click="Delete_CategoryExpense(item)"
                        >
                        </v-btn>
                    </template>
                </v-data-table>
            </v-col>
        </v-row>
    </Layout>
</template>
