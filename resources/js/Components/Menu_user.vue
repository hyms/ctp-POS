<script setup>
import { usePage } from "@inertiajs/vue3";
import { computed } from "vue";
import helpers from "@/helpers";

const roles = computed(() => usePage().props.rolesP);
const user = computed(() => usePage().props.user);
</script>

<template>
    <v-btn
        color="secondary"
        variant="flat"
        prepend-icon="mdi-account"
        class="elevation-2"
    >
        {{ $page.props.user.firstname + " " + $page.props.user.lastname }}

        <v-menu activator="parent">
            <v-list density="compact">
                <v-list-item
                    @click="helpers.linkVisit('/profile')"
                    :active="$page.url === '/profile'"
                >
                    <v-list-item-title>Perfil</v-list-item-title>
                </v-list-item>
                <v-list-item @click="helpers.linkVisit('/logout', 'post')">
                    <v-list-item-title>Salir</v-list-item-title>
                </v-list-item>
            </v-list>
        </v-menu>
    </v-btn>
</template>
