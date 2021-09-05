<template>
  <CHeader fixed with-subheader light>
    <CToggler
      in-header
      class="ml-3 d-lg-none"
      @click="$store.commit('toggleSidebarMobile')"
    />
    <CToggler
      in-header
      class="ml-3 d-md-down-none"
      @click="$store.commit('toggleSidebarDesktop')"
    />
    <CHeaderBrand class="mx-auto d-lg-none" to="/">
      <CIcon name="logo" height="48" alt="Logo"/>
    </CHeaderBrand>
      <CHeaderNav class="d-md-down-none mr-auto">
      </CHeaderNav>
      <CHeaderNav class="mr-4">
          <CHeaderNavItem class="mx-2">
              <Link href="/logout" method="post" class="c-header-nav-link">
                  <i class="mdi mdi-directions"></i>
                  <span>Salir</span>
              </Link>
          </CHeaderNavItem>
      </CHeaderNav>
  </CHeader>
</template>

<script>
import {Link} from "@inertiajs/inertia-vue";

export default {
  name: 'TheHeader',
    components: {
        Link,
    },
}
</script>
