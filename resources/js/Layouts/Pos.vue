<script setup>
const props = defineProps({ loading: { type: Boolean, default: false } });
</script>
<template>
    <v-layout full-height>
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
        <v-main v-else>
            <slot></slot>
        </v-main>
    </v-layout>
</template>
