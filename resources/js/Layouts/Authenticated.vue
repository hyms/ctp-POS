<script setup>
import Menu from '@/Layouts/Menu.vue'
import moment from "moment";
</script>
<template>
    <v-layout full-height>
        <Menu>
        </Menu>
        <v-main>
            <v-container fluid>
                <slot></slot>
            </v-container>
        </v-main>

        <v-footer
            app
            color="transparent"
            height="56"
        >
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


