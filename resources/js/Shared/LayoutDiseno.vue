<template>
  <div>
      <div class="all-wrapper solid-bg-all" >
          <div class="layout-w">
<!--              <MainMenuMobile v-if="isLogged"/>
              <MainMenu v-if="isLogged"/>-->
              <div class="content-w">
                  <div class="content-box">
                  <slot />
                  </div>
              </div>
          </div>
      </div>
  </div>
</template>

<script>

export default {
  components: {
  },
  data() {
    return {
    }
  },
  methods: {
    url() {
    },
    hideDropdownMenus() {
    },
  },
}
</script>
