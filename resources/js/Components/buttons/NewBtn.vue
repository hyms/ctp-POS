<script setup>
const props = defineProps({
  onClick:Function,
  label:String
})
</script>
<template>
  <v-btn
      color="primary"
      class="ma-1"
      prepend-icon="mdi-plus"
      @click="onClick"
  >
    {{ label }}
  </v-btn>
</template>