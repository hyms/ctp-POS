<template>
    <div>
        <v-app-bar
            app
            flat
            absolute
            color="white"
            class=""
            elevation="1"
        >
            <v-app-bar-nav-icon @click="isDrawerOpen = !isDrawerOpen"></v-app-bar-nav-icon>

            <v-toolbar-title>{{ $page.props.titlePage }}</v-toolbar-title>

            <v-spacer></v-spacer>
            <Link  href="/logout" method="post"
                  class="v-btn v-btn--is-elevated v-btn--has-bg theme--light v-size--default primary theme--light">
                Salir
                <v-icon right color="white">
                    mdi-exit-to-app
                </v-icon>
            </Link>

        </v-app-bar>
        <v-navigation-drawer
            v-model="isDrawerOpen"
            app
            class="app-navigation-menu"
            mobile-breakpoint="961"
            dark
            color="#3c4b64"
        >
            <!-- Navigation Header -->
            <div class="vertical-nav-header d-flex text-center justify-center">
                <h2 class="d-flex align-center app-title white--text">
                    xCTP
                </h2>
            </div>


            <template v-for="(value) in menu">
                <template>
                    <v-subheader>
                        <span class="title-wrapper text-uppercase">{{ value.titulo }}</span>
                    </v-subheader>
                    <v-list
                        nav
                    >
                        <v-list-item-group>
                            <!-- Navigation Items -->
                            <template v-for="(link, key) in value.submenu">
                                <template v-if="Object.values(link).length===1">
                                    <v-list-group
                                        v-if="getAllPermission(Object.values(link)[0])" class="text-capitalize"
                                        :value="validateMenu(Object.values(link)[0])"
                                    >
                                        <template #activator>
                                            <v-list-item-content>
                                                <v-list-item-title>
                                                    {{ Object.keys(link)[0] }}
                                                </v-list-item-title>
                                            </v-list-item-content>
                                        </template>
                                        <template v-for="(link2, key2) in Object.values(link)[0]">
                                            <template v-if="getPermission(link2.role)">
                                                <template v-if="link2.newPage===true">
                                                    <a
                                                        :key="key2"
                                                        :href="link2.url"
                                                        target="_blank"
                                                        class="v-list-item v-list-item--link theme--dark"
                                                    >
                                                        <v-list-item-title>
                                                        {{ link2.label }}
                                                        </v-list-item-title>
                                                    </a>
                                                </template>
                                                <Link
                                                    v-else
                                                    :href="link2.url"
                                                    :key="key2"
                                                    :class="'v-list-item v-list-item--link theme--dark' + (getUrl()===link2.url?' v-list-item--active':'') "
                                                >
                                                    <v-list-item-title>
                                                        {{ link2.label }}
                                                    </v-list-item-title>
                                                </Link>
                                            </template>
                                        </template>

                                    </v-list-group>
                                </template>
                                <template v-else>
                                    <template v-if="getPermission(link.role)">
                                        <Link
                                            :href="link.url"
                                            :key="key"
                                            :class="'v-list-item v-list-item--link theme--dark' + (getUrl()===link.url?' v-list-item--active':'') "
                                        >
                                            <v-list-item-content color>
                                                <v-list-item-title>
                                                    <span class="text-capitalize">{{ link.label }}</span>
                                                </v-list-item-title>
                                            </v-list-item-content>

                                        </Link>
                                    </template>
                                </template>
                            </template>
                        </v-list-item-group>
                    </v-list>
                </template>
            </template>

        </v-navigation-drawer>
    </div>
</template>

<script>
// eslint-disable-next-line object-curly-newline
import {Link} from '@inertiajs/inertia-vue'

export default {
    components: {
        Link
    },
    data() {
        return {
            isDrawerOpen: null,
            menu: [
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
                                    url: '/admin/reportes/mora',
                                    role: 'admin',
                                },
                                {
                                    label: 'Ordenes',
                                    url: '/admin/reportes/ordenes',
                                    role: 'admin',
                                },
                                {
                                    label: 'Auditar',
                                    url: '/admin/reportes/auditar',
                                    role: 'admin',
                                },
                                {
                                    label: 'Inventario',
                                    url: '/admin/reportes/inventario',
                                    role: 'admin',
                                },
                            ]
                        },
                        {
                            label: 'Clientes',
                            url: '/admin/clientes',
                            role: 'all',
                        },
                        {
                            Productos: [
                                {
                                    label: 'Productos',
                                    url: '/admin/productos',
                                    role: 'admin',
                                },
                                {
                                    url: '/admin/tipoProductos',
                                    label: 'Tipo Productos',
                                    role: 'admin',
                                },
                                {
                                    url: '/admin/stocks',
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
                                    url: '/admin/sucursales',
                                    role: 'admin',
                                },
                                {
                                    label: 'Cajas',
                                    url: '/admin/cajas',
                                    role: 'admin',
                                },*/
                                {
                                    label: 'Usuarios',
                                    url: '/admin/users',
                                    role: 'admin',
                                },
                                {
                                    label: 'respaldo',
                                    url: '/admin/backup',
                                    role: 'admin',
                                    newPage:true
                                }
                            ]
                        },
                    ]
                }
            ],
        }
    },
    methods: {
        getPermission(role) {
            //for (const key in this.$page.props.rolesP) {
            for (const [key, item] of Object.entries(this.$page.props.rolesP)) {
                if (key === role) {
                    if (item.includes(this.$page.props.user.role)) {
                        return true;
                    }
                }
            }
            return false;
        },
        getAllPermission(data) {
            for (const val of data) {
                let value = this.getPermission(val.role);
                if (value) {
                    return true;
                }
            }
            return false;
        },
        getUrl() {
            return this.$page.url;
        },
        validateMenu(data) {
            for (let value of data) {
                if (this.$page.url === value.url) {
                    return true;
                }
            }
            return false;
        }
    }
}
</script>

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

.vertical-nav-menu-group {

}
</style>
