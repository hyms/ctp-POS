<script setup>
import {ref} from "vue";
import Layout from "@/Layouts/Authenticated.vue";
import Snackbar from "@/Components/snackbar.vue";
import helper from "@/helpers";
import labels from "@/labels";
import {router} from "@inertiajs/vue3";
import DeleteDialog from "@/Components/buttons/DeleteDialog.vue";
import NewBtn from "@/Components/buttons/NewBtn.vue";

const props = defineProps({
  Expenses_category: Object,
});

const form = ref(null);
const search = ref("");
const loading = ref(false);
const snackbar = ref(false);
const snackbarText = ref("");
const snackbarColor = ref("info");
const editmode = ref(false);
const category = ref({
  id: "",
  name: "",
  description: "",
});

const fields = ref([
  {title: "Nombre", key: "name"},
  {title: "Descripcion", key: "description"},
  {title: "Acciones", key: "actions"},
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
  loading.value = true;
  snackbar.value = false;
  axios
      .post("/expenses_category", {
        name: category.value.name,
        description: category.value.description,
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

//---------------------------------- Update Category ----------------\\
function Update_Category() {
  loading.value = true;
  snackbar.value = false;
  axios
      .put("/expenses_category/" + category.value.id, {
        name: category.value.name,
        description: category.value.description,
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
  loading.value = true;
  snackbar.value = false;
  axios
      .delete("/expenses_category/" + category.value.id)
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
      :snackbar-text="snackbarText"
  >
    <delete-dialog
        :model="dialogDelete"
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
              density="compact"
              :title="(editmode ? 'Modificar' : 'Nueva') +' Categoria de gasto'"
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
                    :rules="helper.required"
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
            prepend-icon="mdi-magnify"
            hide-details
            :label="labels.search"
            single-line
            variant="underlined"
        ></v-text-field>
      </v-col>
      <v-col cols="12" sm="6" class="text-right">
        <new-btn :on-click="New_Category" :label="labels.add"></new-btn>
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
                class="ma-1"
                color="primary"
                icon="mdi-pencil"
                size="x-small"
                variant="outlined"
                @click="Edit_Category(item)"
            >
            </v-btn>
            <v-btn
                class="ma-1"
                color="error"
                icon="mdi-delete"
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
