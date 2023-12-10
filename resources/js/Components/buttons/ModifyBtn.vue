<script setup>
const props = defineProps({
    onClick: Function,
});
</script>
<template>
    <v-btn
        class="ma-1"
        color="primary"
        icon="fas fa-pen"
        size="x-small"
        variant="outlined"
        @click="onClick"
    >
    </v-btn>
</template>
