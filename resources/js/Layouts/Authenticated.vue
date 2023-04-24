<script setup>
import Menu from "@/Components/Menu.vue";
import moment from "moment";
import { ref } from "vue";

const props = defineProps({ loading: { type: Boolean, default: false } });
</script>
<template>
    <v-dialog :model-value="loading" :scrim="false" persistent width="auto">
        <v-card color="primary">
            <v-card-text>
                Cargando ...
                <v-progress-linear
                    indeterminate
                    rounded
                    color="white"
                    :height="5"
                ></v-progress-linear>
            </v-card-text>
        </v-card>
    </v-dialog>
    <v-layout full-height v-if="!loading">
        <Menu> </Menu>
        <v-main>
            <v-container fluid>
                <slot></slot>
            </v-container>
        </v-main>

        <v-footer app border>
            <div class="boxed-container w-full">
                <div class="mx-6 d-flex justify-space-between">
                    <span>
                        &copy; {{ moment().year() }}
                        <a class="text-decoration-none">copito</a>
                    </span>
                </div>
            </div>
        </v-footer>
    </v-layout>
</template>
