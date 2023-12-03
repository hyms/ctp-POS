<script setup>
const props = defineProps({
    modelValue: Boolean,
    color: {
        type: String,
        default: "",
    },
    text: {
        type: String,
        default: "",
    },
});
const emit = defineEmits(["update:modelValue"]);

function updateValue(value) {
    emit("update:modelValue", value);
}
</script>
<template>
    <v-snackbar
        :color="color"
        :model-value="modelValue"
        :location="'top right'"
        :timeout="5000"
        @update:modelValue="updateValue"
        elevation="5"
    >
        <div v-html="text"></div>
        <template v-slot:actions>
            <v-btn
                icon="fas fa-times"
                size="x-small"
                variant="tonal"
                @click="updateValue(false)"
            ></v-btn>
        </template>
    </v-snackbar>
</template>
