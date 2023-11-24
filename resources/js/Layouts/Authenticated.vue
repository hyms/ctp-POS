<script setup>
import Menu from "@/Components/Menu.vue";
import Snackbar from "@/Components/Snackbar2.vue";

import moment from "moment";

const props = defineProps({
  loading: {type: Boolean, default: false},
  snackbarView: Boolean,
  snackbarColor: String,
  snackbarText: String,
});
</script>
<template>
  <v-overlay
      :model-value="loading"
      class="align-center justify-center"
  ></v-overlay>

  <Snackbar
      v-model="snackbarView"
      :color="snackbarColor"
      :text="snackbarText"
  ></Snackbar>
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
