<script setup>
const props = defineProps({
  snackbarView: Boolean,
  snackbarColor: String,
  snackbarText: String,
});
</script>

<template>
  <v-snackbar
      :model-value="snackbarView"
      :color="snackbarColor"
      :location="'top right'"
      :timeout="5000"
      elevation="5"
      @input="$emit('update:modelValue', $event.target.value)"
      :close-on-content-click="true"
  >
    {{ snackbarText }}
    <template v-slot:actions>
      <v-btn icon="mdi-close" size="x-small" variant="tonal"></v-btn>
    </template>
  </v-snackbar>
</template>
