<script setup>
import helpers from "@/helpers";

function handleFullScreen() {
    helpers.toggleFullScreen();
}
</script>

<template>
    <v-btn
        color="default"
        variant="tonal"
        icon="mdi-arrow-expand-all"
        size="small"
        class="mr-3 rounded"
        @click="handleFullScreen"
    >
    </v-btn>
</template>
