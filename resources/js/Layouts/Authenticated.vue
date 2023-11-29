<script setup>
import Menu from "@/Components/Menu.vue";
import Snackbar from "@/Components/snackbar.vue";

import moment from "moment";

const props = defineProps({
    loading: { type: Boolean, default: false },
    snackbarView: { type: Boolean, default: false },
    snackbarColor: { type: String, default: "" },
    snackbarText: { type: String, default: "" },
});
</script>
<template>
    <v-overlay
        :model-value="loading"
        class="align-center justify-center"
    ></v-overlay>

    <Snackbar
        :snackbarView="snackbarView"
        :snackbarColor="snackbarColor"
        :snackbarText="snackbarText"
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
