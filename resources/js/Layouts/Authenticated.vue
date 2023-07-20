<script setup>
import Menu from "@/Components/Menu.vue";
import moment from "moment";
import {ref} from "vue";

const props = defineProps({loading: {type: Boolean, default: false}});
</script>
<template>
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
    <Menu></Menu>

    <v-main>
      <v-container fluid>
        <slot></slot>
      </v-container>
    </v-main>

    <v-footer app border>
      <v-container fluid class="py-1">
                    <span>
                        &copy; {{ moment().year() }}
                        <v-chip size="small" variant="text" color="primary" class="text-uppercase">copito</v-chip>
                    </span>
      </v-container>
    </v-footer>
  </v-layout>
</template>
