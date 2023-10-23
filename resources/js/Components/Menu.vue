<script setup>
import {router, usePage} from "@inertiajs/vue3";
import {computed, onMounted, ref} from "vue";
import MenuUser from "@/Components/Menu_user.vue";
import Full_screen from "@/Components/buttons/full_screen.vue";
import helpers from "@/helpers";

const isDrawerOpen = ref(null);
const menuItems = ref([
  {
    label: "Tablero",
    url: "/",
    role: "all",
    icon: "",
    subItems: [],
  },
  {
    label: "Ordenes",
    url: "",
    role: "all",
    icon: "",
    subItems: [
      {
        label: "Crear Orden",
        url: "/sales/create",
        role: "all",
      },
      {
        label: "Listar Ordenes",
        url: "/sales",
        role: "all",
      },
    ],
  },
  {
    label: "Clientes",
    url: "/clients",
    role: "all",
    icon: "",
    subItems: [],
  },
  {
    label: "Gastos",
    url: "",
    role: "vendor",
    icon: "",
    subItems: [
      {
        label: "Añadir Gastos",
        url: "/expenses/create",
        role: "vendor",
      },
      {
        label: "Listar Gastos",
        url: "/expenses",
        role: "vendor",
      },

    ],
  },
  {
    label: "Transferencias de Stock",
    url: "",
    role: "vendor",
    icon: "",
    subItems: [
      {
        label: "Crear Transferencia",
        url: "/transfers/create",
        role: "admin",
      },
      {
        label: "Listar Transferencias",
        url: "/transfers/",
        role: "vendor",
      },
    ],
  },
  {
    label: "Ajustes de Stock",
    url: "",
    role: "vendor",
    icon: "",
    subItems: [
      {
        label: "Crear Ajuste",
        url: "/adjustments/create",
        role: "admin",
      },
      {
        label: "Listar Ajustes",
        url: "/adjustments/list",
        role: "vendor",
      },
    ],
  },
  {
    label: "Productos",
    url: "",
    role: "vendor",
    icon: "",
    subItems: [
      {
        label: "Añadir Productos",
        url: "/products/create",
        role: "admin",
      },
      {
        label: "Listar Productos",
        url: "/products/list",
        role: "vendor",
      },
      {
        label: "Categorias",
        url: "/products/categories",
        role: "admin",
      },
      {
        label: "Unidades",
        url: "/products/units",
        role: "admin",
      },
    ],
  },

  {
    label: "Informes",
    url: "",
    role: "vendor",
    icon: "",
    subItems: [
      {
        label: "Pagos de ventas",
        url: "/payment_sale",
        role: "vendor",
      }, {
        label: "Reporte de ventas",
        url: "/report/sales",
        role: "vendor",
      }, {
        label: "Reporte de Clientes",
        url: "/report/client",
        role: "admin",
      },
      // {
      //   label: "Alerta de Productos",
      //   url: "/report/quantity_alerts",
      //   role: "admin",
      // },
      // {
      //   label: "Reporte de Agencias",
      //   url: "/report/warehouse_report",
      //   role: "admin",
      // },
      {
        label: "Reporte de Stock",
        url: "/report/stock",
        role: "vendor",
      },
      // {
      //   label: "Reporte de Usuarios",
      //   url: "/report/users_report",
      //   role: "admin",
      // },
      {
        label: "Top productos vendidos",
        url: "/report/top_products",
        role: "admin",
      },
      {
        label: "Top Clientes",
        url: "/report/top_customers",
        role: "admin",
      },

    ],
  },
  {
    label: "Configuraciones",
    url: "",
    role: "admin",
    icon: "",
    subItems: [
      {
        label: "Categorias de Gastos",
        url: "/expenses_category",
        role: "admin",
      },
      {
        label: "Tipos de ventas",
        url: "/sales_types",
        role: "admin",
      },
      {
        label: "Almacenes",
        url: "/warehouses",
        role: "admin",
      },
      {
        label: "Usuarios",
        url: "/users",
        role: "admin",
      },

      // {
      //     label: "respaldo",
      //     url: "/backup",
      //     role: "admin",
      //     //newPage: true,
      // },
    ],
  },
]);

const roles = computed(() => usePage().props.rolesP);
const user = computed(() => usePage().props.user);
const open = ref([]);

// const url = computed(() => usePage().url);

function getPermission(role) {
  for (const key in roles.value) {
    for (const [key, item] of Object.entries(roles.value)) {
      if (key === role) {
        if (item.includes(user.value.role)) {
          return true;
        }
      }
    }
  }
  return false;
}

function getAllPermission(data) {
  for (const val of data) {
    let value = getPermission(val.role);
    if (value) {
      return true;
    }
  }
  return false;
}

onMounted(() => {
  router.on("success", (event) => {
    for (let item of menuItems.value) {
      if (item.subItems.length > 0) {
        for (let subItem of item.subItems) {
          if (event.detail.page.url === subItem.url) {
            open.value = [item.label];
          }
        }
      }
    }
  });
});
</script>

<template>
  <v-navigation-drawer
      v-model="isDrawerOpen"
      class="app-navigation-menu"
      theme="dark"
      color="#3c4b64"
      :elevation="2"
  >
    <!-- Navigation Header -->
    <template v-slot:prepend>
      <v-list-item
          lines="two"
          class="vertical-nav-header d-flex text-center justify-center text-center"
      >
        <h2 class="d-flex align-center app-title">xCTP</h2>
      </v-list-item>
    </template>

    <v-divider></v-divider>
    <v-list density="comfortable" :opened="open">
      <template v-for="(link, key) in menuItems">
        <template v-if="link.subItems.length > 0">
          <v-list-group
              v-if="getAllPermission(link.subItems)"
              :key="key"
              :value="link.label"
          >
            <template v-slot:activator="{ props }">
              <v-list-item v-bind="props">
                <v-list-item-title class="text-capitalize">
                  {{ link.label }}
                </v-list-item-title>
              </v-list-item>
            </template>
            <template v-for="(subLink, subKey) in link.subItems">
              <template v-if="getPermission(subLink.role)">
                <v-list-item
                    :key="key + '' + subKey"
                    @click="helpers.linkVisit(subLink.url)"
                    :active="$page.url.includes(subLink.url)"
                    :value="subLink.label"
                >
                  <v-list-item-title class="text-capitalize">
                    {{ subLink.label }}
                  </v-list-item-title>
                </v-list-item>
              </template>
            </template>
          </v-list-group>
        </template>
        <template v-else>
          <v-list-item
              v-if="getPermission(link.role)"
              @click="helpers.linkVisit(link.url)"
              :active="$page.url.includes(link.url)"
              :key="key"
              :value="link.label"
          >
            <v-list-item-title class="text-capitalize">
              {{ link.label }}
            </v-list-item-title>
          </v-list-item>
        </template>
      </template>
    </v-list>
  </v-navigation-drawer>
  <v-app-bar :elevation="2">
    <v-app-bar-nav-icon
        @click.stop="isDrawerOpen = !isDrawerOpen"
    ></v-app-bar-nav-icon>

    <v-toolbar-title>{{ $page.props.titlePage }}</v-toolbar-title>

    <v-spacer></v-spacer>
    <full_screen></full_screen>
    <v-btn color="primary" variant="flat" prepend-icon="mdi-cart" class="mr-3 elevation-2"
           @click="helpers.linkVisit('/pos', )">
      TIENDA
    </v-btn>
    <MenuUser></MenuUser>
  </v-app-bar>
</template>

<style lang="scss" scoped>
.app-title {
  font-size: 1.25rem;
  font-weight: 700;
  font-stretch: expanded;
  font-style: normal;
  line-height: normal;
  letter-spacing: 0.3px;
}

.vertical-nav-header {
  background: rgba(0, 0, 21, 0.2);
  height: 64px;
}
</style>
