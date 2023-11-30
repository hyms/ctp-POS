<script setup>
import { inject } from "vue";
import Menu from "@/Components/Menu.vue";
import Snackbar from "@/Components/snackbar.vue";

const moment = inject("moment");
const props = defineProps({
    loading: { type: Boolean, default: false },
});
</script>
<template>
    <v-layout full-height>
        <Menu></Menu>

        <v-main>

            <v-container fluid>

                <v-row
                v-if="loading"
        class="fill-height"
        align-content="center"
        justify="center"
      >
        <v-col
          class="text-subtitle-1 text-center"
          cols="12"
        >
          Cargando...
        </v-col>
        <v-col cols="6">
          <v-progress-linear
            color="deep-purple-accent-4"
            indeterminate
            rounded
            height="6"
          ></v-progress-linear>
        </v-col>
      </v-row>
      <slot v-else></slot>
            </v-container>
        </v-main>

        <v-footer app border>
            <v-container fluid class="py-1">
                <span>
                    &copy; {{ moment().year() }}
                    <v-chip
                        size="small"
                        variant="text"
                        color="primary"
                        class="text-uppercase"
                        >copito</v-chip
                    >
                </span>
            </v-container>
        </v-footer>
    </v-layout>
</template>
