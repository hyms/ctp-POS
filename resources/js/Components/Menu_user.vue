<script setup>
import { globals, helpers,labelsNew } from "@/helpers";

const fullName = globals.fullName();
</script>

<template>
    <v-btn
        color="secondary"
        variant="flat"
        prepend-icon="mdi-account"
        class="elevation-2"
    >
        {{ fullName }}

        <v-menu activator="parent">
            <v-list density="compact">
                <v-list-item
                    @click="helpers.linkVisit('/profile')"
                    :active="$page.url === '/profile'"
                >
                    <v-list-item-title>{{labelsNew.profil}}</v-list-item-title>
                </v-list-item>
                <v-list-item @click="helpers.linkVisit('/logout', 'post')">
                    <v-list-item-title>{{labelsNew.logout}}</v-list-item-title>
                </v-list-item>
            </v-list>
        </v-menu>
    </v-btn>
</template>
