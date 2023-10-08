<script setup>
import Menu from "@/Components/Menu.vue";
import moment from "moment";
import {ref} from "vue";

const props = defineProps({loading: {type: Boolean, default: false}});
</script>
<template>
  <v-overlay
      :model-value="loading"
      class="align-center justify-center"
  >
    <v-progress-circular color="primary" indeterminate :size="65" :width="6"></v-progress-circular>
  </v-overlay>
  <v-layout full-height>
    <v-main>
      <slot></slot>
    </v-main>
  </v-layout>
</template>
