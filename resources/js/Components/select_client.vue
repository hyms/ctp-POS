<script setup>
import { onMounted, ref } from "vue";
import { api, helpers, labels, labelsNew } from "@/helpers";
import Snackbar from "@/Components/snackbar.vue";

const props = defineProps({
    enableForm: { type: Boolean, default: true },
    modelValue: Number,
});
const emit = defineEmits(["update:modelValue"]);
const dialogCustomer = ref(false);
const loading = ref(false);

const clients = ref([]);
const client = ref({
    id: "",
    name: "",
    code: "",
    email: "",
    phone: "",
    country: "",
    tax_number: "",
    city: "",
    adresse: "",
});
const snackbar = ref({
    color: "",
    view: false,
    text: "",
});

function updateValue(value) {
    emit("update:modelValue", value);
}

const form = ref(null);

//------------- Submit Validation Create & Edit Customer
async function Submit_Customer() {
    snackbar.value.view = false;
    const validate = await form.value.validate();
    if (!validate.valid) {
        snackbar.value.view = true;
        snackbar.value.color = "error";
        snackbar.value.text = labels.no_fill_data;
    } else {
        Create_Client();
    }
}

//---------------------------------------- Create new Customer -------------------------------\\
function Create_Client() {
    api.post({
        url: "/clients",
        params: {
            name: client.value.name,
            company_name: client.value.company_name,
            email: client.value.email,
            phone: client.value.phone,
            nit_ci: client.value.nit_ci,
            country: client.value.country,
            city: client.value.city,
            adresse: client.value.adresse,
        },
        loadingItem: loading,
        snackbar: snackbar,
        onSuccess: () => {
            snackbar.value.text = labels.success_message;
            Get_Client_Without_Paginate();
            dialogCustomer.value = false;
        },
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
        company_name: "",
        email: "",
        phone: "",
        tax_number: "",
        country: "",
        city: "",
        adresse: "",
    };
}

//------------------------------------ Get Clients Without Paginate -------------------------\\
function Get_Client_Without_Paginate() {
    api.get({
        url: "/get_clients_without_paginate",
        loadingItem: loading,
        onSuccess: (data) => {
            clients.value = helpers.toArraySelect(data);
        },
    });
}

onMounted(() => {
    Get_Client_Without_Paginate();
});
</script>

<template>
    <snackbar
        v-model="snackbar.view"
        :text="snackbar.text"
        :color="snackbar.color"
    ></snackbar>
    <v-dialog v-model="dialogCustomer" width="800" v-if="props.enableForm">
        <v-card>
            <v-toolbar>
                <v-toolbar-title>{{ labelsNew.AddCustomer }}</v-toolbar-title>
                <v-spacer></v-spacer>
                <v-btn
                    icon="fas fa-times"
                    size="small"
                    density="comfortable"
                    variant="tonal"
                    @click="dialogCustomer = false"
                ></v-btn>
            </v-toolbar>
            <v-form @submit.prevent="Submit_Customer" ref="form">
                <v-card-text>
                    <v-row>
                        <!-- Customer Name -->
                        <v-col cols="12" md="6">
                            <v-text-field
                                :label="labels.client.name + ' *'"
                                v-model="client.name"
                                :placeholder="labels.client.name"
                                :rules="helpers.required"
                                hide-details="auto"
                            >
                            </v-text-field>
                        </v-col>

                        <!-- Customer Company_name -->
                        <v-col cols="12" md="6">
                            <v-text-field
                                :label="labels.client.company_name + ' *'"
                                v-model="client.company_name"
                                :placeholder="labels.client.company_name"
                                :rules="helpers.required"
                                hide-details="auto"
                            >
                            </v-text-field>
                        </v-col>

                        <!-- Customer Email -->
                        <v-col cols="12" md="6">
                            <v-text-field
                                :label="labels.client.email"
                                v-model="client.email"
                                :placeholder="labels.client.email"
                                hide-details="auto"
                            >
                            </v-text-field>
                        </v-col>

                        <!-- Customer Phone -->
                        <v-col cols="12" md="6">
                            <v-text-field
                                :label="labels.client.phone"
                                v-model="client.phone"
                                :placeholder="labels.client.phone"
                                hide-details="auto"
                            >
                            </v-text-field>
                        </v-col>

                        <!-- Customer City -->
                        <v-col cols="12" md="6">
                            <v-text-field
                                :label="labels.client.city"
                                v-model="client.city"
                                :placeholder="labels.client.city"
                                hide-details="auto"
                            >
                            </v-text-field>
                        </v-col>

                        <!-- Customer Tax Number -->
                        <v-col cols="12" md="6">
                            <v-text-field
                                :label="labels.client.nit_ci"
                                v-model="client.nit_ci"
                                :placeholder="labels.client.nit_ci"
                                hide-details="auto"
                            ></v-text-field>
                        </v-col>

                        <!-- Customer Adress -->
                        <v-col md="12" sm="12">
                            <v-textarea
                                rows="4"
                                :label="labels.client.adresse"
                                v-model="client.adresse"
                                :placeholder="labels.client.adresse"
                                hide-details="auto"
                            ></v-textarea>
                        </v-col>
                    </v-row>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn
                        variant="outlined"
                        color="error"
                        class="ma-1"
                        @click="dialogCustomer = false"
                    >
                        {{ labels.cancel }}
                    </v-btn>
                    <v-btn
                        color="primary"
                        variant="flat"
                        class="ma-1"
                        type="submit"
                        :loading="loading"
                        :disabled="loading"
                    >
                        {{ labels.submit }}
                    </v-btn>
                </v-card-actions>
            </v-form>
        </v-card>
    </v-dialog>
    <v-autocomplete
        :model-value="props.modelValue"
        :items="clients"
        :label="labels.sale.client_id"
        @update:modelValue="updateValue"
        hide-details="auto"
        :rules="helpers.required"
        :loading="loading"
        :no-data-text="labels.no_data_table"
    >
        <template v-slot:append v-if="props.enableForm">
            <v-btn
                color="primary"
                icon="fas fa-user-plus"
                class="rounded"
                @click="New_Client()"
            ></v-btn>
        </template>
    </v-autocomplete>
</template>
