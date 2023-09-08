<script setup>
import {ref} from "vue";
import labels from "@/labels";
import helper from "@/helpers";

const props = defineProps({
  defaultClient: {
    value: {},
    type: Object,
  },
  clients: Object,
  enableForm: {
    value: true,
    type: Boolean,
  },
  modelValue: Number,
  error: Object,
})
const emit = defineEmits(['update:modelValue'])
const dialogCustomer = ref(false);
const clientFilter = ref([]);

const client = ref({
  id: "",
  name: "",
  code: "",
  email: "",
  phone: "",
  country: "",
  tax_number: "",
  city: "",
  adresse: ""
});

function updateValue(value) {
  emit('update:modelValue', value)
}

//---------- filter clients
function querySelectionClient(v) {
  clientFilter.value = props.clients.filter((e) => {
    return (
        (e.name || "").toLowerCase().indexOf((v || "").toLowerCase()) > -1
    );
  });
}


//------------- Submit Validation Create & Edit Customer
async function Submit_Customer() {
  snackbar.value = false;
  const validate = await formCreateCustomer.value.validate();
  if (!validate) {
    snackbar.value = true;
    snackbarColor.value = "error";
    snackbarText.value = labels.no_fill_data;
  } else {
    Create_Client();
  }
}

//---------------------------------------- Create new Customer -------------------------------\\
function Create_Client() {
  loading.value = true;
  axios
      .post("/clients", {
        name: client.value.name,
        email: client.value.email,
        phone: client.value.phone,
        tax_number: client.value.tax_number,
        country: client.value.country,
        city: client.value.city,
        adresse: client.value.adresse
      })
      .then(response => {
        snackbar.value = true;
        snackbarColor.value = "success";
        snackbarText.value = labels.success_message;
        // Get_Client_Without_Paginate();
        dialogCustomer.value = false;
      })
      .catch(error => {
        snackbar.value = true;
        snackbarColor.value = "error";
        snackbarText.value = labels.error_message;
        console.log(error)
      })
      .finally(() => {
        loading.value = false;
      });
}

//------------------------------ New Model (create Customer) -------------------------------\\
function New_Client() {
  reset_Form_client();
  dialogCustomer.value = true;
}

//-------------------------------- reset Form -------------------------------\\
function reset_Form_client() {
  client.value = {
    id: "",
    name: "",
    email: "",
    phone: "",
    tax_number: "",
    country: "",
    city: "",
    adresse: ""
  };
}
</script>

<template>
<!--  v-on:input="updateValue($event.target.value)"-->
  <!--      @update:search="querySelectionClient"-->
  <v-autocomplete
      :model-value="props.modelValue"
      :items="props.clients"
      :label="labels.sale.client_id"
      @update:modelValue="updateValue"
      item-title="name"
      item-value="id"
      variant="outlined"
      density="comfortable"
      hide-details="auto"
      clearable
      :rules="helper.required"
      :no-data-text="(props.clients.length==0)?labels.no_data_table:'Escriba el nombre del cliente'"
  >
    <template v-slot:append>
      <v-btn color="primary" icon="mdi-account-plus" density="comfortable" class="rounded"
             @click="New_Client()"></v-btn>
    </template>
  </v-autocomplete>
</template>

