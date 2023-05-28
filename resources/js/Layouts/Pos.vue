<script setup>
import Menu from "@/Components/Menu.vue";
import moment from "moment";
import {ref} from "vue";

const props = defineProps({loading: {type: Boolean, default: false}});
</script>
<template>
  <v-theme-provider theme="customLight" with-background>
    <v-dialog :model-value="loading" persistent width="auto">
      <v-card color="surface">
        <v-card-text>
          <div class="text-center ma-1 pa-1">
            <v-progress-circular color="primary" indeterminate :size="65" :width="6"></v-progress-circular>
          </div>
        </v-card-text>
      </v-card>
    </v-dialog>
    <v-layout full-height>
      <v-main>
        <slot></slot>
      </v-main>
    </v-layout>
  </v-theme-provider>
</template>
