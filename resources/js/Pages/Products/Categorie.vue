<script setup>
import { ref } from "vue";
import Layout from "@/Layouts/Authenticated.vue";
import Snackbar from "@/Components/snackbar.vue";
import ruleForm from "@/rules";
import { router } from "@inertiajs/vue3";

const props = defineProps({
    categories: Array,
    errors: Object,
});

//declare variable
const form = ref(null);
const search = ref("");
const loading = ref(false);
const snackbar = ref(false);
const snackbarText = ref("");
const snackbarColor = ref("info");
const dialog = ref(false);
const dialogDelete = ref(false);
const editmode = ref(false);

const fields = ref([
    { title: "Nombre", key: "name" },
    { title: "Codigo", key: "code" },
    { title: "Acciones", key: "actions" },
]);
const category = ref({
    id: "",
    name: "",
    code: "",
});
const categoryLabels = ref({
    name: "Nombre",
    code: "Codigo",
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
    loading.value = true;
    snackbar.value = false;
    axios
        .post("categories", {
            name: category.value.name,
            code: category.value.code,
        })
        .then(({ data }) => {
            snackbar.value = true;
            snackbarColor.value = "success";
            snackbarText.value = "Proceso exitoso";
            router.reload();
            dialog.value = false;
        })
        .catch((error) => {
            console.log(error);
            snackbar.value = true;
            snackbarColor.value = "error";
            snackbarText.value = error.response.data.message;
        })
        .finally(() => {
            setTimeout(() => {
                loading.value = false;
            }, 1000);
        });
}

//---------------------------------- Update Category ----------------\\
function Update_Category() {
    loading.value = true;
    snackbar.value = false;
    axios
        .put("categories/" + category.value.id, {
            name: category.value.name,
            code: category.value.code,
        })
        .then(({ data }) => {
            snackbar.value = true;
            snackbarColor.value = "success";
            snackbarText.value = "Proceso exitoso";
            router.reload();
            dialog.value = false;
        })
        .catch((error) => {
            console.log(error);
            snackbar.value = true;
            snackbarColor.value = "error";
            snackbarText.value = error.response.data.message;
        })
        .finally(() => {
            setTimeout(() => {
                loading.value = false;
            }, 1000);
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
    loading.value = true;
    snackbar.value = false;
    axios
        .delete("categories/" + category.value.id)
        .then(({ data }) => {
            snackbar.value = true;
            snackbarColor.value = "success";
            snackbarText.value = "Borrado exitoso";
            router.reload();
            dialogDelete.value = false;
        })
        .catch((error) => {
            console.log(error);
            snackbar.value = true;
            snackbarColor.value = "error";
            snackbarText.value = error.response.data.message;
        })
        .finally(() => {
            setTimeout(() => {
                loading.value = false;
            }, 1000);
        });
}
</script>
<template>
    <Layout :loading="loading">
        <snackbar
            :snackbar="snackbar"
            :snackbar-color="snackbarColor"
            :snackbar-text="snackbarText"
        ></snackbar>
        <delete_-category
            :model="dialogDelete"
            :on-save="Remove_Category"
            :on-close="onCloseDelete"
        >
        </delete_-category>
        <v-dialog
            v-model="dialog"
            max-width="400px"
            scrollable
            @update:modelValue="dialog === false ? reset_Form() : dialog"
        >
            <v-card>
                <v-toolbar
                    border
                    density="comfortable"
                    :title="(editmode ? 'Modificar' : 'Nueva') + ' Categoria'"
                >
                </v-toolbar>

                <v-card-text>
                    <v-form ref="form">
                        <v-row>
                            <!-- Code category -->
                            <v-col md="12">
                                <v-text-field
                                    :label="categoryLabels.code + ' *'"
                                    v-model="category.code"
                                    :placeholder="categoryLabels.code"
                                    :rules="ruleForm.required"
                                    variant="outlined"
                                    density="comfortable"
                                    hide-details="auto"
                                >
                                </v-text-field>
                            </v-col>

                            <!-- Name category -->
                            <v-col md="12">
                                <v-text-field
                                    :label="categoryLabels.name + ' *'"
                                    v-model="category.name"
                                    :placeholder="categoryLabels.name"
                                    :rules="ruleForm.required"
                                    variant="outlined"
                                    density="comfortable"
                                    hide-details="auto"
                                >
                                </v-text-field>
                            </v-col>
                        </v-row>
                    </v-form>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn
                        size="small"
                        variant="outlined"
                        color="error"
                        class="ma-1"
                        @click="onClose"
                    >
                        Cancelar
                    </v-btn>
                    <v-btn
                        size="small"
                        color="primary"
                        variant="elevated"
                        class="ma-1"
                        @click="Submit_Category"
                        :loading="loading"
                        :disabled="loading"
                    >
                        Guardar
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>

        <v-row align="center">
            <v-col>
                <v-text-field
                    v-model="search"
                    prepend-icon="mdi-magnify"
                    density="compact"
                    hide-details
                    label="Buscar"
                    single-line
                    variant="underlined"
                ></v-text-field>
            </v-col>
            <v-spacer></v-spacer>
            <v-col cols="auto" class="text-right">
                <v-btn
                    size="small"
                    color="primary"
                    class="ma-1"
                    prepend-icon="mdi-account-plus"
                    @click="New_category"
                >
                    Añadir
                </v-btn>
            </v-col>
        </v-row>
        <v-row>
            <v-col>
                <v-data-table
                    :headers="fields"
                    :items="categories"
                    :search="search"
                    hover
                    class="elevation-2"
                    density="compact"
                    no-data-text="No existen datos a mostrar"
                >
                    <template v-slot:item.actions="{ item }">
                        <v-btn
                            class="ma-1"
                            color="primary"
                            icon="mdi-pencil"
                            size="x-small"
                            variant="outlined"
                            @click="Edit_category(item.raw)"
                        >
                        </v-btn>
                        <v-btn
                            class="ma-1"
                            color="error"
                            icon="mdi-delete"
                            size="x-small"
                            variant="outlined"
                            @click="Delete_Category(item.raw)"
                        >
                        </v-btn>
                    </template>
                </v-data-table>
            </v-col>
        </v-row>
    </Layout>
</template>
