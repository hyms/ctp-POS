<script setup>
import {ref} from "vue";
import Layout from "@/Layouts/Authenticated.vue";
import Snackbar from "@/Components/snackbar.vue";
import helper from "@/helpers";
import labels from "@/labels";
import {router} from "@inertiajs/vue3";
import DeleteDialog from "@/Components/DeleteDialog.vue";

const props = defineProps({
  warehouses: Array,
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
  {title: "Nombre", key: "name"},
  {title: "Tefono", key: "mobile"},
  {title: "Ciudad", key: "city"},
  {title: "Correo", key: "email"},
  {title: "Acciones", key: "actions"},
]);

//form
const warehouse = ref({
  id: "",
  name: "",
  mobile: "",
  email: "",
  country: "",
  city: "",
});


//------------------------------ Modal (create Warehouse) -------------------------------\\
function New_Warehouse() {
  reset_Form();
  editmode.value = false;
  dialog.value = true;
}

//------------------------------ Modal (Update Warehouse) -------------------------------\\
function Edit_Warehouse(item) {
  reset_Form();
  warehouse.value = item;
  editmode.value = true;
  dialog.value = true;
}

//------------------------------- Create Warehouse ------------------------\\
function Create_Warehouse() {
  loading.value = true;
  snackbar.value = false;
  axios
      .post("/warehouses", {
        name: warehouse.value.name,
        mobile: warehouse.value.mobile,
        email: warehouse.value.email,
        city: warehouse.value.city,
      })
      .then(({data}) => {
        dialog.value = false;
        snackbar.value = true;
        snackbarColor.value = "success";
        snackbarText.value = labels.success_message;
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

//------------------------------- Update Warehouse ------------------------\\
function Update_Warehouse() {
  loading.value = true;
  snackbar.value = false;
  axios
      .put("/warehouses/" + warehouse.value.id, {
        name: warehouse.value.name,
        mobile: warehouse.value.mobile,
        email: warehouse.value.email,
        city: warehouse.value.city,
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

//------------------------------- reset Form ------------------------\\
function reset_Form() {
  warehouse.value = {
    id: "",
    name: "",
    mobile: "",
    email: "",
    country: "",
    city: "",
  };
}

//---------------------- modal  ------------------------------\\
async function onSave() {
  const validate = await form.value.validate();
  if (validate.valid)
    if (!editmode.value) {
      Create_Warehouse();
    } else {
      Update_Warehouse();
    }
}

function onClose() {
  dialog.value = false;
  reset_Form();
}

//---------------------- delete modal  ------------------------------\\
function Delete_Warehouse(item) {
  reset_Form();
  warehouse.value = item;
  dialogDelete.value = true;
}

//------------------------------- Delete Warehouse ------------------------\\
function Remove_Warehouse() {
  loading.value = true;
  snackbar.value = false;
  axios
      .delete("/warehouses/" + warehouse.value.id)
      .then(({data}) => {
        dialogDelete.value = false;
        snackbar.value = true;
        snackbarColor.value = "success";
        snackbarText.value = labels.delete_message;
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

function onCloseDelete() {
  reset_Form();
  dialogDelete.value = false;
}
</script>

<template>
  <Layout>
    <snackbar
        :snackbar="snackbar"
        :snackbar-color="snackbarColor"
        :snackbar-text="snackbarText"
    ></snackbar>
    <delete-dialog
        :model="dialogDelete"
        :on-save="Remove_Warehouse"
        :on-close="onCloseDelete"
    >
    </delete-dialog>
    <v-dialog
        v-model="dialog"
        max-width="600px"
        scrollable
        @update:modelValue="dialog === false ? reset_Form() : dialog"
    >
      <v-card>
        <v-form ref="form">
          <v-toolbar
              border
              density="compact"
              :title="(editmode ? 'Modificar' : 'Nuevo') + ' Almacen'"
          >
          </v-toolbar>

          <v-card-text>
            <v-row>
              <!-- First name -->
              <v-col cols="12" md="6">
                <v-text-field
                    :label="labels.warehouse.name + ' *'"
                    v-model="warehouse.name"
                    :placeholder="labels.warehouse.name"
                    :rules="helper.required"
                    variant="outlined"
                    density="comfortable"
                    hide-details="auto"
                >
                </v-text-field>
              </v-col>

              <!-- Last name -->
              <v-col cols="12" md="6">
                <v-text-field
                    :label="labels.warehouse.mobile"
                    v-model="warehouse.mobile"
                    :placeholder="labels.warehouse.mobile"
                    variant="outlined"
                    density="comfortable"
                    hide-details="auto"
                >
                </v-text-field>
              </v-col>

              <!-- Username -->
              <v-col cols="12" md="6">
                <v-text-field
                    :label="labels.warehouse.city"
                    v-model="warehouse.city"
                    :placeholder="labels.warehouse.city"
                    variant="outlined"
                    density="comfortable"
                    hide-details="auto"
                >
                </v-text-field>
              </v-col>

              <!-- Phone -->
              <v-col cols="12" md="6">
                <v-text-field
                    :label="labels.warehouse.email"
                    v-model="warehouse.email"
                    :placeholder="labels.warehouse.email"
                    variant="outlined"
                    density="comfortable"
                    hide-details="auto"
                >
                </v-text-field>
              </v-col>
            </v-row>
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
              {{ labels.cancel }}
            </v-btn>
            <v-btn
                type="submit"
                size="small"
                color="primary"
                variant="elevated"
                class="ma-1"
                @click="onSave"
                :loading="loading"
                :disabled="loading"
            >
              {{ labels.submit }}
            </v-btn>
          </v-card-actions>
        </v-form>
      </v-card>
    </v-dialog>
    <v-row>
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
        <v-btn
            size="small"
            color="primary"
            class="ma-1"
            prepend-icon="mdi-account-plus"
            @click="New_Warehouse"
        >
          {{ labels.add }}
        </v-btn>
      </v-col>
      <v-col cols="12">
        <v-card>
          <v-data-table
              :headers="fields"
              :items="warehouses"
              :search="search"
              hover
              density="compact"
              :no-data-text="labels.no_data_table"
              :loading="loading"
          >
            <template v-slot:item.actions="{ item }">
              <v-btn
                  class="ma-1"
                  color="primary"
                  icon="mdi-pencil"
                  size="x-small"
                  variant="outlined"
                  @click="Edit_Warehouse(item.raw)"
              >
              </v-btn>
              <v-btn
                  class="ma-1"
                  color="error"
                  icon="mdi-delete"
                  size="x-small"
                  variant="outlined"
                  @click="Delete_Warehouse(item.raw)"
              >
              </v-btn>
            </template>
          </v-data-table>
        </v-card>
      </v-col>
    </v-row>
  </Layout>
</template>
