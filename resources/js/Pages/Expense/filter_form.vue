<script setup>
import { onMounted, ref } from "vue";
import { router } from "@inertiajs/vue3";
import helpers from "@/helpers";
import labels from "@/labels";

const props = defineProps({
    filter_form: Object,
    categories: Object,
    warehouses: Object,
});
const form = ref({
    start_date: "",
    end_date: "",
    ref: "",
    warehouse: "",
    category: "",
});

const menu = ref(false);

onMounted(() => {
    form.value = props.filter_form;
    if (form.value.category) {
        form.value.category = helpers.getObjectValue(
            form.value.category,
            props.categories
        );
    }
    if (form.value.warehouse) {
        form.value.warehouse = helpers.getObjectValue(
            form.value.warehouse,
            props.warehouses
        );
    }
});

function search() {
    router.get(
        "/expenses",
        {
            filter: {
                start_date: form.value.start_date,
                end_date: form.value.end_date,
                ref: form.value.ref,
                category: helpers.getValueObject(form.value.category),
                warehouse: helpers.getValueObject(form.value.warehouse),
            },
        },
        { replace: true }
    );
}
</script>
<template>
    <v-menu v-model="menu" :close-on-content-click="false" location="bottom">
        <template v-slot:activator="{ props }">
            <v-btn
                color="primary"
                variant="outlined"
                elevation="1"
                class="mr-2 my-1"
                v-bind="props"
                append-icon="mdi-magnify"
            >
                {{ labels.filters }}
            </v-btn>
        </template>

        <v-card max-width="500">
            <v-form>
                <v-card-text>
                    <v-row>
                        <v-col cols="12" sm="6">
                            <v-text-field
                                v-model="form.start_date"
                                clearable
                                hide-details="auto"
                                type="date"
                                :label="labels.start_date"
                            ></v-text-field>
                        </v-col>
                        <v-col cols="12" sm="6">
                            <v-text-field
                                v-model="form.end_date"
                                clearable
                                hide-details="auto"
                                type="date"
                                :label="labels.end_date"
                            ></v-text-field>
                        </v-col>
                        <v-col cols="12" sm="6">
                            <v-text-field
                                v-model="form.ref"
                                clearable
                                hide-details="auto"
                                type="text"
                                :label="labels.expense.ref"
                            ></v-text-field>
                        </v-col>
                        <v-col cols="12" sm="6">
                            <v-autocomplete
                                v-model="form.category"
                                item-title="name"
                                item-value="id"
                                clearable
                                hide-details="auto"
                                :items="categories"
                                :label="labels.expense.category_id"
                            ></v-autocomplete>
                        </v-col>
                        <v-col cols="12" sm="6">
                            <v-select
                                v-model="form.warehouse"
                                item-title="name"
                                item-value="id"
                                clearable
                                hide-details="auto"
                                :items="warehouses"
                                :label="labels.expense.warehouse_id"
                            ></v-select>
                        </v-col>
                    </v-row>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn variant="text" color="error" @click="menu = false">
                        {{ labels.cancel }}
                    </v-btn>
                    <v-btn variant="tonal" color="primary" @click="search">
                        {{ labels.search }}
                    </v-btn>
                </v-card-actions>
            </v-form>
        </v-card>
    </v-menu>
</template>
