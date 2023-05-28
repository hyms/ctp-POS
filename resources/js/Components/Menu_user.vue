<script setup>
import {router, usePage} from "@inertiajs/vue3";
import {computed, ref} from "vue";

const roles = computed(() => usePage().props.rolesP);
const user = computed(() => usePage().props.user);

function linkVisit(url, type = "get") {
  router.visit(url, {
    method: type,
    preserveState: true,
  });
}
</script>

<template>
  <v-btn color="secondary" variant="outlined" prepend-icon="mdi-account">
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
