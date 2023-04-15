<script setup>
const props = defineProps({
    snackbar: Boolean,
    snackbarColor: String,
    snackbarText: String,
});
</script>

<template>
    <v-snackbar
        :model-value="snackbar"
        :color="snackbarColor"
        :location="'top right'"
        :timeout="5000"
        elevation="5"
    >
        {{ snackbarText }}
        <template v-slot:actions>
            <v-btn
                @click="snackbar = false"
                icon="mdi-close"
                size="x-small"
                variant="tonal"
            >
            </v-btn>
        </template>
    </v-snackbar>
</template>
