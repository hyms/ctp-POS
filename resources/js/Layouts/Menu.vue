<script setup>
import {Link, router} from '@inertiajs/vue3';
import {ref} from "vue";

const isDrawerOpen = ref(null);
const menu = ref([
    {
        titulo: 'Agencia',
        submenu: [
            {
                label: 'Tablero',
                url: '/',
                role: 'all',
            },
            {
                Ordenes: [
                    {
                        label: 'Nuevas Ordenes',
                        url: '/ordenes',
                        role: 'desing',
                    },
                    {
                        label: 'Buscar Ordenes',
                        url: '/realizados',
                        role: 'desing',
                    },
                ]
            },
            {
                Recibos: [
                    {
                        label: 'Egresos',
                        url: '/recibosEgreso',
                        role: 'vendor',
                    },
                    {
                        label: 'Ingresos',
                        url: '/recibosIngreso',
                        role: 'vendor',
                    },
                ]
            },
            {
                label: 'Caja Chica',
                url: '/cajaDebito',
                role: 'vendor',
            },
            {
                label: 'Inventario',
                url: '/inventario',
                role: 'vendor'
            },
            {
                reportes: [
                    {
                        label: 'Registro Diario',
                        url: '/reportes/placas',
                        role: 'vendor',
                    },
                    {
                        label: 'Rendicion Diaria',
                        url: '/reportes/diario',
                        role: 'vendor',
                    },
                    /* {
                         label: 'Reporte cliente',
                         url: '/reportes/cliente',
                         role: 'vendor',
                     },*/
                ]
            },


        ]
    },
    {
        titulo: 'Administracion',
        submenu: [
            {
                Reportes: [
                    {
                        label: 'Mora Clientes',
                        url: '/reportes/mora',
                        role: 'admin',
                    },
                    {
                        label: 'Ordenes',
                        url: '/reportes/ordenes',
                        role: 'admin',
                    },
                    {
                        label: 'Auditar',
                        url: '/reportes/auditar',
                        role: 'admin',
                    },
                    {
                        label: 'Inventario',
                        url: '/reportes/inventario',
                        role: 'admin',
                    },
                ]
            },
            {
                label: 'Clientes',
                url: '/clientes',
                role: 'all',
            },
            {
                Productos: [
                    {
                        label: 'Productos',
                        url: '/productos',
                        role: 'admin',
                    },
                    {
                        url: '/tipoProductos',
                        label: 'Tipo Productos',
                        role: 'admin',
                    },
                    {
                        url: '/stocks',
                        label: 'Stocks',
                        role: 'admin',
                    },
                    // {
                    //     url: 'movimientosStock',
                    //     label: 'Movimientos',
                    //     role: 'admin',
                    // },
                ]
            },
            {
                Configuraciones: [
                    {
                        label: 'Almacenes',
                        url: '#',
                        role: 'admin',
                    },
                    /*{
                        label: 'Sucursales',
                        url: '/sucursales',
                        role: 'admin',
                    },
                    {
                        label: 'Cajas',
                        url: '/cajas',
                        role: 'admin',
                    },*/
                    {
                        label: 'Usuarios',
                        url: '/users',
                        role: 'admin',
                    },
                    {
                        label: 'respaldo',
                        url: '/backup',
                        role: 'admin',
                        newPage: true
                    }
                ]
            },
        ]
    }
]);
function linkGet(url){
    router.visit(url,
        {
            method: 'get',
            preserveScroll: true
        });
}
function getPermission(role) {
    //for (const key in this.$page.props.rolesP) {
    return true;
    // for (const [key, item] of Object.entries(this.$page.props.rolesP)) {
    //     if (key === role) {
    //         if (item.includes(this.$page.props.user.role)) {
    //             return true;
    //         }
    //     }
    // }
    // return false;
}

function getAllPermission(data) {
    for (const val of data) {
        let value = this.getPermission(val.role);
        if (value) {
            return true;
        }
    }
    return false;
}

function validateMenu(data) {
    return true;
    // for (let value of data) {
    //     if (this.$page.url === value.url) {
    //         return true;
    //     }
    // }
    // return false;
}
</script>

<template>
    <div>
        <v-navigation-drawer
            v-model="isDrawerOpen"
            class="app-navigation-menu"
            theme="dark"
            color="#3c4b64"
            :elevation="2"
        >
            <!-- Navigation Header -->
            <template v-slot:prepend>
                <v-list-item lines="two" class="vertical-nav-header d-flex text-center justify-center">
                    <h2 class="d-flex align-center app-title">
                        xCTP
                    </h2>
                </v-list-item>
            </template>

            <v-divider></v-divider>
            <v-list nav density="compact">
                <template v-for="(value) in menu">
                    <v-list-subheader>
                        <span class="text-uppercase">{{ value.titulo }}</span>
                    </v-list-subheader>

                    <!-- Navigation Items -->
                    <template v-for="(link, key) in value.submenu">
                        <template v-if="Object.values(link).length===1">

                            <v-list-group
                                v-if="getAllPermission(Object.values(link)[0])"
                                :value="Object.values(link)[0]"
                            >
                                <template v-slot:activator="{ props }">
                                    <v-list-item v-bind="props">
                                        <v-list-item-title>
                                            <span class="text-capitalize">{{ Object.keys(link)[0] }}</span>
                                        </v-list-item-title>
                                    </v-list-item>
                                </template>
                                <template v-for="(subLink, subKey) in Object.values(link)[0]">
                                    <template v-if="getPermission(subLink.role)">
                                        <v-list-item
                                            v-if="subLink.newPage===true"
                                            :key="key+''+subKey"
                                            @click="linkGet(subLink.url)"
                                            target="_blank"
                                        >
                                            <v-list-item-title>
                                                <span class="text-capitalize">{{ subLink.label }}</span>
                                            </v-list-item-title>
                                        </v-list-item>
                                        <v-list-item
                                            v-else
                                            :key="key+''+subKey"
                                            @click="linkGet(subLink.url)"
                                            :active="($page.url === subLink.url)"
                                        >
                                            <v-list-item-title>
                                                <span class="text-capitalize">{{ subLink.label }}</span>
                                            </v-list-item-title>
                                        </v-list-item>
                                    </template>
                                </template>

                            </v-list-group>
                        </template>
                        <template v-else>
                            <v-list-item v-if="getPermission(link.role)"
                                         @click="linkGet(link.url)"
                                         :active="($page.url === link.url)"
                                         :key="key"
                            >
                                <v-list-item-title>
                                    <span class="text-capitalize">{{ link.label }}</span>
                                </v-list-item-title>
                            </v-list-item>
                        </template>
                    </template>
                </template>
            </v-list>

        </v-navigation-drawer>
        <v-app-bar :elevation="2">
            <v-app-bar-nav-icon @click.stop="isDrawerOpen = !isDrawerOpen"></v-app-bar-nav-icon>

            <v-toolbar-title>{{ $page.props.titlePage }}</v-toolbar-title>

            <v-spacer></v-spacer>
            <Link href="/logout" method="post" as="button"
                  class="v-btn v-btn--elevated v-theme--customLight bg-primary v-btn--density-default v-btn--size-default v-btn--variant-elevated">
                Salir
                <v-icon right color="white">
                    mdi-exit-to-app
                </v-icon>
            </Link>
        </v-app-bar>
    </div>
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
    background: rgba(0, 0, 21, .2);
    height: 64px;
}

.vertical-nav-menu-link {
    &.v-list-item--active {
        box-shadow: 0 5px 10px -4px rgba(94, 86, 105, 0.42);
    }
}
</style>
