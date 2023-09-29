<script setup>
import {ref} from "vue";
import Layout from "@/Layouts/Authenticated.vue";
import Snackbar from "@/Components/snackbar.vue";
import ExportBtn from "@/Components/ExportBtn.vue";
import {router} from "@inertiajs/vue3";
import DeleteDialog from "@/Components/DeleteDialog.vue";

const props = defineProps({
  warehouses: Array,
  categories: Array,
  products: Array,
  errors: Object,
});
const search = ref("");
const loading = ref(false);
const snackbar = ref(false);
const snackbarText = ref("");
const snackbarColor = ref("info");
const dialogDelete = ref(false);

const fields = ref([
  {title: "Nombre", key: "name"},
  {title: "Codigo", key: "code"},
  {title: "Categoria", key: "category"},
  {title: "Precio", key: "price"},
  {title: "Unidad", key: "unit"},
  {title: "Cantidad", key: "quantity"},
  {title: "Acciones", key: "actions"},
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
  product.value = {id: ""};
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
  snackbar.value = false;
  axios
      .delete("/products/" + product.value.id)
      .then(({data}) => {
        dialogDelete.value = false;
        snackbar.value = true;
        snackbarColor.value = "success";
        snackbarText.value = "Borrado exitoso";
        router.reload({
          preserveState: true,
          preserveScroll: true,
        });

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
  <layout :loading="loading">
    <snackbar
        :snackbar="snackbar"
        :snackbar-text="snackbarText"
        :snackbar-color="snackbarColor"
    ></snackbar>
    <!-- Modal Remove Product -->
    <delete-dialog
        :model="dialogDelete"
        :on-save="Remove_Product"
        :on-close="onCloseDelete"
    ></delete-dialog>
    <v-row align="center">
      <v-col cols="12" sm="6">
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
      <v-col cols="12" sm="6" class="text-right">
        <ExportBtn
            :data="products"
            :fields="jsonFields"
            name-file="Productos"
        ></ExportBtn>
        <!--                <v-btn-->
        <!--                    @click="Show_import_productos()"-->
        <!--                    size="small"-->
        <!--                    class="ma-1"-->
        <!--                    color="info"-->
        <!--                    variant="elevated"-->
        <!--                    prepend-icon="mdi-download"-->
        <!--                >-->
        <!--                    Importar-->
        <!--                </v-btn>-->
        <v-btn
            size="small"
            color="primary"
            class="ma-1"
            prepend-icon="mdi-account-plus"
            @click="router.visit('/products/create')"
        >
          Añadir
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
            density="compact"
            no-data-text="No existen datos a mostrar"
        >
          <template v-slot:item.actions="{ item }">
            <v-btn
                class="ma-1"
                color="info"
                icon="mdi-eye"
                size="x-small"
                variant="outlined"
                @click="
                                router.visit('/products/detail/' + item.id)
                            "
            >
            </v-btn>
            <v-btn
                class="ma-1"
                color="primary"
                icon="mdi-pencil"
                size="x-small"
                variant="outlined"
                @click="
                                router.visit('/products/edit/' + item.id)
                            "
            >
            </v-btn>
            <v-btn
                class="ma-1"
                color="error"
                icon="mdi-delete"
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
