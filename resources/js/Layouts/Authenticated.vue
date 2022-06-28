<template>
    <v-app>
        <Menu>
        </Menu>
        <v-main>
            <div class="app-content-container boxed-container pa-6">
                <slot></slot>
            </div>
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
            &copy; 2021 <a
              href="https://themeselection.com"
              class="text-decoration-none"
              target="_blank"
          >ThemeSelection</a></span>

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
