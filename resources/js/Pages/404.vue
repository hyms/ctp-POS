<script setup>
import Guest from "@/Layouts/Guest.vue";
import helpers from "@/helpers";

const history = window.history;
</script>
<template>
    <guest>
        <div class="auth-wrapper auth-v1">
            <div class="auth-inner">
                <v-card class="auth-card" :elevation="5">
                    <v-card-title>
                        The Page you were looking for, couldn't be found.
                    </v-card-title>
                    <v-card-text>
                        <p>
                            The page you are looking for might have been
                            removed, had its name changed, or is temporarily
                            unavailable.
                        </p>
                    </v-card-text>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn
                            variant="text"
                            @click="history.back()"
                            color="primary"
                            >Back</v-btn
                        >
                        <v-btn
                            variant="tonal"
                            @click="helpers.linkVisit('/')"
                            color="primary"
                            >Back To Home</v-btn
                        >
                    </v-card-actions>
                </v-card>
            </div>
        </div>
    </guest>
</template>
