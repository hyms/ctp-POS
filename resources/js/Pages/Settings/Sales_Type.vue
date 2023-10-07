<script setup>
import {ref} from "vue";
import Layout from "@/Layouts/Authenticated.vue";
import Snackbar from "@/Components/snackbar.vue";
import helper from "@/helpers";
import labels from "@/labels";
import {router} from "@inertiajs/vue3";
import DeleteDialog from "@/Components/DeleteDialog.vue";
import DeleteBtn from "@/Components/DeleteBtn.vue";
import ModifyBtn from "@/Components/ModifyBtn.vue";
import NewBtn from "@/Components/NewBtn.vue";

const props = defineProps({
    sales_types: Array,
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
    {title: labels.sales_type.name, key: "name"},
    {title: labels.sales_type.code, key: "code"},
    {title: labels.actions, key: "actions"},
]);
const sales_type = ref({
    id: "",
    name: "",
    code: "",
});


//------------- Submit Validation Create & Edit SalesType
async function Submit_SalesType() {
    const validate = await form.value.validate();
    if (validate.valid)
        if (!editmode.value) {
            Create_SalesType();
        } else {
            Update_SalesType();
        }
}

//------------------------------ Modal  (create SalesType) -------------------------------\\
function New_SalesType() {
    reset_Form();
    editmode.value = false;
    dialog.value = true;
}

function onClose() {
    dialog.value = false;
    reset_Form();
}

//------------------------------ Modal (Update SalesType) -------------------------------\\
function Edit_SalesType(item) {
    reset_Form();
    sales_type.value = item;
    editmode.value = true;
    dialog.value = true;
}

//----------------------------------Create new SalesType ----------------\\
function Create_SalesType() {
    loading.value = true;
    snackbar.value = false;
    axios
        .post("sales_types", {
            name: sales_type.value.name,
            code: sales_type.value.code,
        })
        .then(({data}) => {
            snackbar.value = true;
            snackbarColor.value = "success";
            snackbarText.value = labels.success_message;
            router.reload({
                preserveState: true,
                preserveScroll: true,
            });
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

//---------------------------------- Update SalesType ----------------\\
function Update_SalesType() {
    loading.value = true;
    snackbar.value = false;
    axios
        .put("/sales_types/" + sales_type.value.id, {
            name: sales_type.value.name,
            code: sales_type.value.code,
        })
        .then(({data}) => {
            snackbar.value = true;
            snackbarColor.value = "success";
            snackbarText.value = labels.success_message;
            router.reload({
                preserveState: true,
                preserveScroll: true,
            });
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
    sales_type.value = {
        id: "",
        name: "",
        code: "",
    };
}

//---------------------- delete modal  ------------------------------\\
function Delete_SalesType(item) {
  console.log(item)
    reset_Form();
    sales_type.value = item;
    dialogDelete.value = true;
}

function onCloseDelete() {
    reset_Form();
    dialogDelete.value = false;
}

//--------------------------- Remove SalesType----------------\\
function Remove_SalesType() {
    loading.value = true;
    snackbar.value = false;
    axios
        .delete("/sales_types/" + sales_type.value.id)
        .then(({data}) => {
            snackbar.value = true;
            snackbarColor.value = "success";
            snackbarText.value = labels.delete_message;
            router.reload({
                preserveState: true,
                preserveScroll: true,
            });
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
    <Layout
        :snackbar-view="snackbar"
        :snackbar-color="snackbarColor"
        :snackbar-text="snackbarText">
        <delete-dialog
            :model="dialogDelete"
            :on-close="onCloseDelete"
            :on-save="Remove_SalesType"
        >
        </delete-dialog>
        <v-dialog
            v-model="dialog"
            max-width="400px"
            scrollable
            @update:modelValue="dialog === false ? reset_Form() : dialog"
        >
            <v-card>
                <v-form ref="form" @submit.prevent="Submit_SalesType">
                    <v-toolbar
                        border
                        :title="(editmode ? 'Modificar' : 'Nuevo') + ' Tipo de Venta'"
                    >
                    </v-toolbar>

                    <v-card-text>
                        <v-row>
                            <!-- Code SalesType -->
                            <v-col cols="12">
                                <v-text-field
                                    :label="labels.sales_type.code + ' *'"
                                    v-model="sales_type.code"
                                    :placeholder="labels.sales_type.code"
                                    :rules="helper.required"
                                    hide-details="auto"
                                >
                                </v-text-field>
                            </v-col>

                            <!-- Name SalesType -->
                            <v-col cols="12">
                                <v-text-field
                                    :label="labels.sales_type.name + ' *'"
                                    v-model="sales_type.name"
                                    :placeholder="labels.sales_type.name"
                                    :rules="helper.required"
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
                            type="submit"
                            color="primary"
                            variant="elevated"
                            class="ma-1"
                            @click="Submit_SalesType"
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
                    prepend-icon="mdi-magnify"
                    hide-details
                    :label="labels.search"
                    single-line
                    variant="underlined"
                ></v-text-field>
            </v-col>
            <v-col cols="12" sm="6" class="text-right">
              <new-btn :label="labels.add" :on-click="New_SalesType"></new-btn>
            </v-col>
        </v-row>
        <v-row>
            <v-col cols="12">
                <v-card>
                    <v-data-table
                        :headers="fields"
                        :items="sales_types"
                        :search="search"
                        hover
                        :no-data-text="labels.no_data_table"
                        :loading="loading"
                    >
                        <template v-slot:item.actions="{ item }">
                          <modify-btn :on-click="()=>{Edit_SalesType(item)}"></modify-btn>
                          <delete-btn :on-click="()=>{Delete_SalesType(item)}"></delete-btn>
                        </template>
                    </v-data-table>
                </v-card>
            </v-col>
        </v-row>
    </Layout>
</template>
