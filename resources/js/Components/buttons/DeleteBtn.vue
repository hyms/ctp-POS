<script setup>
const props = defineProps({
  onClick: {type:Function}
})
</script>
<template>
  <v-btn
      class="ma-1"
      color="error"
      icon="mdi-delete"
      size="x-small"
      variant="outlined"
      @click="onClick"
  >
  </v-btn>
</template>