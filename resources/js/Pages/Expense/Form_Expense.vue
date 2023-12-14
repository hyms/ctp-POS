<script setup>
import { onMounted, ref, watch } from "vue";
import Layout from "@/Layouts/Authenticated.vue";
import { router } from "@inertiajs/vue3";
import { api, helpers, labels, rules } from "@/helpers";
import Snackbar from "@/Components/snackbar.vue";

const props = defineProps({
    warehouses: Object,
    expense_category: Object,
    expense: { type: Object, default: null },
    errors: Object,
});
const form = ref(null);
const editmode = ref(false);
const loading = ref(false);
const snackbar = ref({
    view: false,
    color: "",
    text: "",
});

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
    api.post({
        url: "/expenses",
        params: {
            expense: expenseForm.value,
        },
        loadingItem: loading,
        snackbar,
        onSuccess: () => {
            snackbar.value.text = labels.success_message;
            router.visit("/expenses");
        },
    });
}

//--------------------------------- Update Expense -------------------------\\
function Update_Expense() {
    let id = expenseForm.value.id;
    api.put({
        url: `/expenses/${id}`,
        params: {
            expense: expenseForm.value,
        },
        loadingItem: loading,
        snackbar,
        onSuccess: () => {
            snackbar.value.text = labels.success_message;
            router.visit("/expenses");
        },
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
    <Layout>
        <snackbar
            v-model="snackbar.view"
            :text="snackbar.text"
            :color="snackbar.color"
        ></snackbar>
        <v-card :loading="loading">
            <v-toolbar height="15"></v-toolbar>
            <v-card-text>
                <v-form
                    :disabled="loading"
                    @submit.prevent="Submit_Expense"
                    ref="form"
                >
                    <v-row class="mt-3">
                        <!-- date  -->
                        <v-col lg="4" md="6" cols="12">
                            <v-text-field
                                :label="labels.expense.date + ' *'"
                                v-model="expenseForm.date"
                                :placeholder="labels.expense.date"
                                :rules="rules.required"
                                hide-details="auto"
                                type="date"
                            >
                            </v-text-field>
                        </v-col>

                        <!-- warehouse -->
                        <v-col lg="4" md="6" cols="12">
                            <v-select
                                v-model="expenseForm.warehouse_id"
                                :items="helpers.toArraySelect(warehouses)"
                                :label="labels.expense.warehouse_id"
                                hide-details="auto"
                                clearable
                                :rules="rules.required"
                            ></v-select>
                        </v-col>

                        <!-- Expense_Category  -->
                        <v-col lg="4" md="6" cols="12">
                            <v-select
                                v-model="expenseForm.category_id"
                                :items="helpers.toArraySelect(expense_category)"
                                :label="labels.expense.category_id"
                                hide-details="auto"
                                clearable
                                :rules="rules.required"
                            ></v-select>
                        </v-col>

                        <!-- Amount  -->
                        <v-col md="4" cols="12">
                            <v-text-field
                                :label="labels.expense.amount + ' *'"
                                v-model="expenseForm.amount"
                                :placeholder="labels.expense.amount"
                                :rules="
                                    rules.required.concat(
                                        rules.numberWithDecimal
                                    )
                                "
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
                                :rules="rules.required"
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
