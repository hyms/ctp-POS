<script setup>
const props = defineProps({
    onClick: { type: Function },
});
</script>
<template>
    <v-btn
        class="ma-1"
        color="error"
        icon="fas fa-trash"
        size="x-small"
        variant="outlined"
        @click="onClick"
    >
    </v-btn>
</template>
