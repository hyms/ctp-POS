<script setup>
import { onMounted, ref } from "vue";
import Layout from "@/Layouts/Authenticated.vue";
import ExportBtn from "@/Components/buttons/ExportBtn.vue";
import { router } from "@inertiajs/vue3";
import DeleteDialog from "@/Components/dialogs/DeleteDialog.vue";
import Snackbar from "@/Components/snackbar.vue";
import { api, globals, labels } from "@/helpers";

const props = defineProps({
    errors: Object,
});
const warehouses = ref([]);
const categories = ref([]);
const products = ref([]);
const search = ref("");
const loading = ref(false);
const snackbar = ref({
    view: false,
    color: "",
    text: "",
});
const dialogDelete = ref(false);

const fields = ref([
    { title: labels.product.name, key: "name" },
    { title: labels.product.code, key: "code" },
    { title: labels.product.category_id, key: "category" },
    { title: labels.product.price, key: "price" },
    { title: labels.product.unit_id, key: "unit" },
    { title: labels.product.qty, key: "quantity" },
    { title: labels.actions, key: "actions" },
]);
const jsonFields = ref({
    Nombre: "name",
    Codigo: "code",
    Categoria: "category",
    Precio: "price",
    Unidad: "unit",
    Cantidad: "quantity",
});
const product = ref({
    id: "",
});
//     //----------------------------------- Show import products -------------------------------\\
//     Show_import_products() {
//       this.$bvModal.show("importProducts");
//     },
//
//     //------------------------------ Event Import products -------------------------------\\
//     onFileSelected(e) {
//       this.import_products = "";
//       let file = e.target.files[0];
//       let errorFilesize;
//
//       if (file["size"] < 1048576) {
//         // 1 mega = 1,048,576 Bytes
//         errorFilesize = false;
//       } else {
//         this.makeToast(
//           "danger",
//           this.$t("file_size_must_be_less_than_1_mega"),
//           this.$t("Failed")
//         );
//       }
//
//       if (errorFilesize === false) {
//         this.import_products = file;
//       }
//     },
//
//     //----------------------------------------Submit  import products-----------------\\
//     Submit_import() {
//       // Start the progress bar.
//       NProgress.start();
//       NProgress.set(0.1);
//       var self = this;
//       self.ImportProcessing = true;
//       self.data.append("products", self.import_products);
//       axios
//         .post("products/import/csv", self.data)
//         .then(response => {
//           self.ImportProcessing = false;
//           if (response.data.status === true) {
//             this.makeToast(
//               "success",
//               this.$t("Successfully_Imported"),
//               this.$t("Success")
//             );
//             Fire.$emit("Event_import");
//           } else if (response.data.status === false) {
//             this.makeToast(
//               "danger",
//               this.$t("field_must_be_in_csv_format"),
//               this.$t("Failed")
//             );
//           }
//           // Complete the animation of theprogress bar.
//           NProgress.done();
//         })
//         .catch(error => {
//           self.ImportProcessing = false;
//           // Complete the animation of theprogress bar.
//           NProgress.done();
//           this.makeToast(
//             "danger",
//             this.$t("Please_follow_the_import_instructions"),
//             this.$t("Failed")
//           );
//         });
//     },
//
//-------------------------------- Reset Form -------------------------------\\
function reset_Form() {
    product.value = { id: "" };
}

//----------------------------------- Remove Product ------------------------------\\
function onCloseDelete() {
    reset_Form();
    dialogDelete.value = false;
}

function Delete_Item(item) {
    reset_Form();
    product.value = item;
    dialogDelete.value = true;
}

function Remove_Product() {
    api.delete({
        url: "/products/" + product.value.id,
        loadingItem: loading,
        snackbar,
        onSuccess: (data) => {
            snackbar.value.text = labels.delete_message;
            loadData();
            dialogDelete.value = false;
        },
    });
}

function loadData() {
    api.get({
        url: "/products/list",
        loadingItem: loading,
        onSuccess: (data) => {
            warehouses.value = data.warehouses;
            categories.value = data.categories;
            products.value = data.products;
        },
    });
}

onMounted(() => {
    loadData();
});
</script>
<template>
    <layout>
        <snackbar
            v-model="snackbar.view"
            :text="snackbar.text"
            :color="snackbar.color"
        ></snackbar>
        <!-- Modal Remove Product -->
        <delete-dialog
            v-model="dialogDelete"
            :on-save="Remove_Product"
            :on-close="onCloseDelete"
        ></delete-dialog>
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
                <ExportBtn
                    :data="products"
                    :fields="jsonFields"
                    name-file="Productos"
                ></ExportBtn>
                <!--                <v-btn-->
                <!--                    @click="Show_import_productos()"-->
                <!--                    -->
                <!--                    class="ma-1"-->
                <!--                    color="info"-->
                <!--                    variant="elevated"-->
                <!--                    prepend-icon="mdi-download"-->
                <!--                >-->
                <!--                    Importar-->
                <!--                </v-btn>-->
                <v-btn
                    v-if="globals.userPermision(['products_add'])"
                    color="primary"
                    class="ma-1"
                    prepend-icon="fas fa-user-plus"
                    @click="router.visit('/products/create')"
                >
                    {{ labels.add }}
                </v-btn>
            </v-col>
        </v-row>
        <v-row>
            <v-col cols="12">
                <v-data-table
                    :headers="fields"
                    :items="products"
                    :search="search"
                    hover
                    class="elevation-2"
                    :no-data-text="labels.no_data_table"
                    :loading="loading"
                >
                    <template v-slot:item.actions="{ item }">
                        <v-btn
                            class="ma-1"
                            color="info"
                            icon="fas fa-eye"
                            size="x-small"
                            variant="outlined"
                            @click="router.visit('/products/detail/' + item.id)"
                        >
                        </v-btn>
                        <v-btn
                            v-if="globals.userPermision(['products_edit'])"
                            class="ma-1"
                            color="primary"
                            icon="fas fa-pen"
                            size="x-small"
                            variant="outlined"
                            @click="router.visit('/products/edit/' + item.id)"
                        >
                        </v-btn>
                        <v-btn
                            v-if="globals.userPermision(['products_delete'])"
                            class="ma-1"
                            color="error"
                            icon="fas fa-trash"
                            size="x-small"
                            variant="outlined"
                            @click="Delete_Item(item)"
                        >
                        </v-btn>
                    </template>
                </v-data-table>
            </v-col>
        </v-row>
    </layout>
</template>
