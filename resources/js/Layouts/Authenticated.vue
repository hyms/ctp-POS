<template>
    <v-app>
        <Menu>
        </Menu>
        <v-main >
            <v-container fluid>
                <slot></slot>
            </v-container>
        </v-main>

        <v-footer
            app
            inset
            color="transparent"
            absolute
            height="56"
            class="px-0"
        >
            <div class="boxed-container w-full">
                <div class="mx-6 d-flex justify-space-between">
          <span>
            &copy; 2021
              <a
              class="text-decoration-none"
          >copito</a>
          </span>
                </div>
            </div>
        </v-footer>
    </v-app>
</template>

<script>
import Menu from './components/Menu.vue'

export default {
    components: {
        Menu,

    },

}
</script>

<style lang="scss" scoped>

</style>
