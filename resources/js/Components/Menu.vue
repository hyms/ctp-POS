<script setup>
import { router } from "@inertiajs/vue3";
import { onMounted, ref } from "vue";
import MenuUser from "@/Components/Menu_user.vue";
import FullScreen from "@/Components/buttons/FullScreen.vue";
import { globals, helpers, labelsNew } from "@/helpers";

const isDrawerOpen = ref(null);
const menuItems = ref([]);
const open = ref([]);

function loadMenu() {
    let subItems = [];
    menuItems.value = [];
    menuItems.value.push({
        label: labelsNew.dashboard,
        url: "/",
        activate: ["/"],
        subItems: [],
    });
    if (
        globals.userPermision(["Sales_add", "Sales_view", "unit", "category"])
    ) {
        subItems = [];
        if (globals.userPermision(["Sales_add"])) {
            subItems.push({
                label: "Crear Orden",
                url: "/sales/create",
                activate: ["/sales/create"],
            });
        }
        if (globals.userPermision(["Sales_view"])) {
            subItems.push({
                label: "Listar Ordenes",
                url: "/sales",
                activate: ["/sales"],
            });
        }
        menuItems.value.push({
            label: "Ordenes",
            url: "",
            activate: ["/sales/create", "/sales"],
            subItems: subItems.slice(),
        });
    }
    if (globals.userPermision(["Customers_view"])) {
        menuItems.value.push({
            label: "Clientes",
            url: "/clients",
            activate: ["/clients"],
            subItems: [],
        });
    }
    if (globals.userPermision(["expense_view", "expense_add"])) {
        subItems = [];
        if (globals.userPermision(["expense_add"])) {
            subItems.push({
                label: "Añadir Gastos",
                url: "/expenses/create",
                activate: ["/expenses/create"],
            });
        }
        if (globals.userPermision(["expense_view"])) {
            subItems.push({
                label: "Listar Gastos",
                url: "/expenses",
                activate: ["/expenses"],
            });
        }
        menuItems.value.push({
            label: "Gastos",
            url: "",
            activate: ["/expenses", "/expenses/create"],

            subItems: subItems.slice(),
        });
    }
    if (globals.userPermision(["transfer_view", "transfer_add"])) {
        subItems = [];
        if (globals.userPermision(["transfer_add"])) {
            subItems.push({
                label: "Crear Transferencia",
                url: "/transfers/create",
                activate: ["/transfers/create"],
            });
        }
        if (globals.userPermision(["transfer_view"])) {
            subItems.push({
                label: "Listar Transferencias",
                url: "/transfers",
                activate: ["/transfers"],
            });
        }
        menuItems.value.push({
            label: "Transferencias de Stock",
            url: "",
            activate: ["/transfers", "/transfers/create"],

            subItems: subItems.slice(),
        });
    }
    if (globals.userPermision(["adjustment_add", "adjustment_view"])) {
        subItems = [];
        if (globals.userPermision(["adjustment_add"])) {
            subItems.push({
                label: "Crear Ajuste",
                url: "/adjustments/create",
                activate: ["/adjustments/create"],
            });
        }
        if (globals.userPermision(["adjustment_view"])) {
            subItems.push({
                label: "Listar Ajustes",
                url: "/adjustments/",
                activate: ["/adjustments"],
            });
        }
        menuItems.value.push({
            label: "Ajustes de Stock",
            url: "",
            activate: ["/adjustments", "/adjustments/create"],

            subItems: subItems.slice(),
        });
    }
    if (
        globals.userPermision([
            "products_add",
            "products_view",
            "unit",
            "category",
        ])
    ) {
        subItems = [];
        if (globals.userPermision(["products_add"])) {
            subItems.push({
                label: "Añadir Productos",
                url: "/products/create",
                activate: ["/products/create"],
            });
        }
        if (globals.userPermision(["products_view"])) {
            subItems.push({
                label: "Listar Productos",
                url: "/products/",
                activate: ["/products/"],
            });
        }
        if (globals.userPermision(["category"])) {
            subItems.push({
                label: "Categorias",
                url: "/products/categories",
                activate: ["/products/categories"],
            });
        }
        if (globals.userPermision(["unit"])) {
            subItems.push({
                label: "Unidades",
                url: "/products/units",
                activate: ["/products/units"],
            });
        }
        menuItems.value.push({
            label: labelsNew.Products,
            url: "",
            activate: [
                "/products/create",
                "/products/",
                "/products/categories",
                "/products/units",
            ],

            subItems: subItems.slice(),
        });
    }
    if (
        globals.userPermision([
            "product_sales_report",
            "product_report",
            "stock_report",
            "users_report",
            "Reports_profit",
            "Warehouse_report",
            "Reports_customers",
            "Reports_sales",
            "Reports_payments_Sales",
            // 'Reports_payments_Purchases',
        ])
    ) {
        subItems = [];
        if (globals.userPermision(["Reports_payments_Sales"])) {
            subItems.push({
                label: "Reporte de Pagos recibidos",
                url: "/payment_sale",
                activate: ["/payment_sale"],
            });
        }
        // if (globals.userPermision(["Reports_payments_Purchases"])) {
        //     subItems.push({
        //         label: "Pagos de compras",
        //         url: "/payments_purchase",
        //         activate: ["/payments_purchase"],
        //     });
        // }
        if (globals.userPermision(["Reports_sales"])) {
            subItems.push({
                label: "Reporte de Ordenes",
                url: "/report/sales",
                activate: ["/report/sales"],
            });
        }
        // if (globals.userPermision(["Reports_purchase"])) {
        //     subItems.push({
        //         label: "Reporte de compras",
        //         url: "/report/purchase_report",
        //         activate: ["/report/purchase_report"],
        //     });
        // }
        if (globals.userPermision(["Reports_customers"])) {
            subItems.push({
                label: "Reporte de Clientes",
                url: "/report/client",
                activate: ["/report/client"],
            });
        }
        // if (globals.userPermision(["Reports_profit"])) {
        //     subItems.push({
        //         label: "Perdidas y Ganancias",
        //         url: "/report/profit_and_loss",
        //         activate: ["/report/profit_and_loss"],
        //     });
        // }

        if (globals.userPermision(["stock_report"])) {
            subItems.push({
                label: "Reporte de Stock",
                url: "/report/stock",
                activate: ["/report/stock"],
            });
        }

        if (globals.userPermision(["Warehouse_report"])) {
            subItems.push({
                label: "Reporte de almacenes",
                url: "/report/warehouse_report",
                activate: ["/report/warehouse_report"],
            });
        }

        // if (globals.userPermision(["product_report"])) {
        //     subItems.push({
        //         label: "Top productos vendidos",
        //         url: "/report/top_products",
        //         activate: ["/report/top_products"],
        //     });
        // }
        if (globals.userPermision(["users_report"])) {
            subItems.push({
                label: "Reporte de usuarios",
                url: "/report/users",
                activate: ["/report/users_report"],
            });
        }

        menuItems.value.push({
            label: "Informes",
            url: "",
            activate: [
                "/payment_sale",
                "/report/sales",
                "/report/client",
                "/report/stock",
                "/report/top_products",
                "/report/users_report",
                "/report/warehouse_report",
            ],
            subItems: subItems.slice(),
        });
    }
    // settings
    if (
        globals.userPermision([
            "setting_system",
            "pos_settings",
            "permissions_view",
            "expense_category_view",
            "sales_types",
            "warehouse",
            "users_view",
        ])
    ) {
        subItems = [];
        if (globals.userPermision(["setting_system"])) {
            subItems.push({
                label: labelsNew.SystemSettings,
                url: "/get_Settings_data",
                activate: ["/system_settings"],
            });
        }
        if (globals.userPermision(["pos_settings"])) {
            subItems.push({
                label: labelsNew.pos_settings,
                url: "/pos_settings",
                activate: ["/pos_settings"],
            });
        }
        if (globals.userPermision(["permissions_view"])) {
            subItems.push({
                label: labelsNew.GroupPermissions,
                url: "/roles",
                activate: ["/roles", "/roles-create"],
            });
        }
        if (globals.userPermision(["expense_category_view"])) {
            subItems.push({
                label: "Categorias de Gastos",
                url: "/expenses_category",
                activate: ["/expenses_category"],
            });
        }
        if (globals.userPermision(["sales_type"])) {
            subItems.push({
                label: "Tipos de ventas",
                url: "/sales_types",
                activate: ["/sales_types"],
            });
        }
        if (globals.userPermision(["warehouse"])) {
            subItems.push({
                label: labelsNew.Warehouses,
                url: "/warehouses",
                activate: ["/warehouses"],
            });
        }
        if (globals.userPermision(["users_view"])) {
            subItems.push({
                label: labelsNew.Users,
                url: "/users",
                activate: ["/users"],
            });
        }
        // if (globals.userPermision(["backup"])) {
        //     subItems.push({
        //         label: labelsNew.Backup,
        //         url: "/backup",
        //         activate: ["/backup"],
        //     });
        // }

        menuItems.value.push({
            label: labelsNew.Settings,
            url: "",
            activate: [
                "/expenses_category",
                "/sales_types",
                "/warehouses",
                "/users",
                "/backup",
                "/roles-create",
                "/roles",
            ],
            subItems: subItems.slice(),
        });
    }
}

onMounted(() => {
    loadMenu();
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
                    <v-list-group :key="key" :value="link.label">
                        <template v-slot:activator="{ props }">
                            <v-list-item v-bind="props">
                                <v-list-item-title class="text-capitalize">
                                    {{ link.label }}
                                </v-list-item-title>
                            </v-list-item>
                        </template>
                        <v-list-item
                            v-for="(subLink, subKey) in link.subItems"
                            :key="key + '' + subKey"
                            @click="helpers.linkVisit(subLink.url)"
                            :active="subLink.activate.includes($page.url)"
                            :value="subLink.label"
                        >
                            <v-list-item-title class="text-capitalize">
                                {{ subLink.label }}
                            </v-list-item-title>
                        </v-list-item>
                    </v-list-group>
                </template>
                <template v-else>
                    <v-list-item
                        @click="helpers.linkVisit(link.url)"
                        :active="link.activate.includes($page.url)"
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
        <full-screen></full-screen>
        <v-btn
            v-if="globals.userPermision(['screen_view_pendings'])"
            color="info"
            variant="flat"
            prepend-icon="fas fa-tv"
            class="mr-3 elevation-2"
            @click="helpers.linkVisit('/screen_pending')"
        >
            Pendientes
        </v-btn>
        <v-btn
            v-if="globals.userPermision(['screen_view'])"
            color="info"
            variant="flat"
            prepend-icon="fas fa-tv"
            class="mr-3 elevation-2"
            @click="helpers.linkVisit('/screen')"
        >
            Pantalla
        </v-btn>
        <v-btn
            v-if="globals.userPermision(['Pos_view'])"
            color="primary"
            variant="flat"
            prepend-icon="fas fa-shopping-cart"
            class="mr-3 elevation-2"
            @click="helpers.linkVisit('/pos')"
        >
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
