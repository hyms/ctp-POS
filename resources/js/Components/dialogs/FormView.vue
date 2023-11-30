<script setup>
import {labels} from "@/helpers";
import {ref} from "vue";

const props = defineProps({
    modelValue: Boolean,
    onSave: {
        type: Function,
        default: null
    },
    loading: Boolean,
    title: String,
    submitText: {
        type: String,
        default: labels.btn.save
    },
});
const form = ref({});

async function submitForm() {
    const validate = await form.value.validate();
        if (props.onSave != undefined && typeof (props.onSave) === 'function') {
            props.onSave(validate.valid);
        }
}

</script>
<template>
        <v-card class="pa-2">
            <v-card-title>{{ title }}</v-card-title>
            <v-form ref="form" @submit.prevent="submitForm" fast-fail :disabled="loading">
                <v-card-text>
                    <slot></slot>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn
                        type="reset"
                        variant="tonal"
                        color="error"
                        :disabled="loading">
                        {{ labels.btn.discard }}
                    </v-btn>
                    <v-btn
                        type="submit"
                        color="primary"
                        variant="elevated"
                        :loading="loading">
                        {{ submitText }}
                    </v-btn>
                </v-card-actions>
            </v-form>
        </v-card>
</template>