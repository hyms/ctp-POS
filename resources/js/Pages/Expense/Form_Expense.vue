<script setup>
import {ref, onMounted, watch} from "vue";
import Layout from "@/Layouts/Authenticated.vue";
import {router} from "@inertiajs/vue3";
import helper from "@/helpers";
import labels from "@/labels";
import Snackbar from "@/Components/snackbar.vue";

const props = defineProps({
  warehouses: Object,
  expense_category: Object,
  expense: {type: Object, default: null},
  errors: Object,
});
const form = ref(null);
const editmode = ref(false);
const loading = ref(false);
const snackbar = ref(false);
const snackbarText = ref("");
const snackbarColor = ref("info");

const expenseForm = ref({
  date: new Date().toISOString().slice(0, 10),
  warehouse_id: "",
  category_id: "",
  details: "",
  amount: "",
});


//------------- Submit Validation Create Expense
async function Submit_Expense() {
  const validate = await form.value.validate();
  if (validate.valid)
    if (editmode.value) {
      Update_Expense();
    } else {
      Create_Expense();
    }
}

// ---------------------------Create Expense
function Create_Expense() {
  loading.value = true;
  snackbar.value = false;

  axios
      .post("/expenses", {
        expense: expenseForm.value,
      })
      .then(({data}) => {
        snackbar.value = true;
        snackbarColor.value = "success";
        snackbarText.value = labels.success_message;
        router.visit("/expenses");
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

//--------------------------------- Update Expense -------------------------\\
function Update_Expense() {
  loading.value = true;
  snackbar.value = false;
  let id = expenseForm.value.id;
  axios
      .put(`/expenses/${id}`, {
        expense: expenseForm.value,
      })
      .then(({data}) => {
        snackbar.value = true;
        snackbarColor.value = "success";
        snackbarText.value = labels.success_message;
        router.visit("/expenses");
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

watch(
    () => [props.expense],
    () => {
      if (props.expense != null) {
        editmode.value = true;
      } else {
        expenseForm.value = {
          date: new Date().toISOString().slice(0, 10),
          warehouse_id: "",
          category_id: "",
          details: "",
          amount: "",
        };
        editmode.value = false;
      }
    }
);

onMounted(() => {
  if (props.expense != null) {
    expenseForm.value = props.expense;
    editmode.value = true;
  } else if (props.warehouses.length == 1) {
    expenseForm.value.warehouse_id = props.warehouses[0].id;
  }
});
</script>
<template>
  <Layout :loading="loading"
          :snackbar-view="snackbar"
          :snackbar-color="snackbarColor"
          :snackbar-text="snackbarText">

    <v-card :loading="loading">
      <v-toolbar height="15"></v-toolbar>
      <v-card-text>
        <v-form @submit.prevent="Submit_Expense" ref="form">
          <v-row class="mt-3">
            <!-- date  -->
            <v-col lg="4" md="6" cols="12">
              <v-text-field
                  :label="labels.expense.date + ' *'"
                  v-model="expenseForm.date"
                  :placeholder="labels.expense.date"
                  :rules="helper.required"
                  hide-details="auto"
                  type="date"
              >
              </v-text-field>
            </v-col>

            <!-- warehouse -->
            <v-col lg="4" md="6" cols="12">
              <v-select
                  v-model="expenseForm.warehouse_id"
                  :items="warehouses"
                  :label="labels.expense.warehouse_id"
                  item-title="name"
                  item-value="id"
                  hide-details="auto"
                  clearable
                  :rules="helper.required"
              ></v-select>
            </v-col>

            <!-- Expense_Category  -->
            <v-col lg="4" md="6" cols="12">
              <v-select
                  v-model="expenseForm.category_id"
                  :items="expense_category"
                  :label="labels.expense.category_id"
                  item-title="name"
                  item-value="id"
                  hide-details="auto"
                  clearable
                  :rules="helper.required"
              ></v-select>
            </v-col>

            <!-- Amount  -->
            <v-col md="4" cols="12">
              <v-text-field
                  :label="labels.expense.amount + ' *'"
                  v-model="expenseForm.amount"
                  :placeholder="labels.expense.amount"
                  :rules="helper.required.concat(helper.numberWithDecimal)"
                  hide-details="auto"
              >
              </v-text-field>
            </v-col>

            <!-- Details -->
            <v-col md="8" cols="12">
              <v-textarea
                  rows="4"
                  :label="labels.expense.details + ' *'"
                  v-model="expenseForm.details"
                  :placeholder="labels.expense.details"
                  :rules="helper.required"
                  hide-details="auto"
              ></v-textarea>
            </v-col>

            <v-col md="12">
              <v-btn
                  variant="elevated"
                  type="submit"
                  color="primary"
                  :loading="loading"
                  :disabled="loading"
              >{{ labels.submit }}
              </v-btn>
            </v-col>
          </v-row>
        </v-form>
      </v-card-text>
    </v-card>
  </Layout>
</template>
