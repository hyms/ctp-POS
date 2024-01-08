<script setup>
import { labelsNew } from "@/helpers";
import { ref } from "vue";

const props = defineProps({
    onSave: {
        type: Function,
        default: null,
    },
    loading: Boolean,
    submitText: {
        type: String,
        default: labelsNew.submit,
    },
});
const form = ref({});

async function submitForm() {
    const validate = await form.value.validate();
    if (props.onSave != undefined && typeof props.onSave === "function") {
        props.onSave(validate.valid);
    }
}
</script>
<template>
    <v-card :loading="loading">
        <v-form @submit.prevent="submitForm" ref="form" :disabled="loading">
            <v-toolbar height="15"></v-toolbar>
            <v-card-text>
                <slot></slot>
            </v-card-text>
            <v-card-actions>
                <v-row>
                    <v-col cols="12">
                        <v-btn
                            type="submit"
                            color="primary"
                            variant="elevated"
                            :loading="loading"
                        >
                            {{ submitText }}
                        </v-btn>
                    </v-col>
                </v-row>
            </v-card-actions>
        </v-form>
    </v-card>
</template>
