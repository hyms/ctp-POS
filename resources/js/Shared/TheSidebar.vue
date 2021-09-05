<template>
    <CSidebar
        fixed
        :minimize="minimize"
        :show="show"
        @update:show="(value) => $store.commit('set', ['sidebarShow', value])"
    >
        <CSidebarBrand class="d-md-down-none" to="/">
            xCTP
        </CSidebarBrand>
        <CSidebarNav>
            <template v-for="(value) in menu">
                <CSidebarNavTitle>
                    {{ value.titulo }}
                </CSidebarNavTitle>
                <template v-for="(link, key) in value.submenu">
                    <template v-if="Object.values(link).length==1">
                        <CSidebarNavDropdown :name="Object.keys(link)[0]">
                            <template v-for="(link2, key2) in Object.values(link)[0]">
                                <template v-if="getPermission(link2.role)">
                                    <li class="c-sidebar-nav-item">
                                        <Link
                                            :href="link2.url"
                                            :key="key2"
                                            :class="'c-sidebar-nav-link '+((getUrl() === link2.url ||getUrl() === link2.url2)?'c-active':'')"
                                        >
                                            <span>{{ link2.label }}</span>
                                        </Link>
                                    </li>
                                </template>
                            </template>
                        </CSidebarNavDropdown>
                    </template>
                    <template v-else>
                        <template v-if="getPermission(link.role)">
                            <li class="c-sidebar-nav-item">
                                <Link
                                    :href="link.url"
                                    :key="key"
                                    :class="'c-sidebar-nav-link '+((getUrl() === link.url ||getUrl() === link.url2)?'c-active':'')"
                                >
                                    <span>{{ link.label }}</span>
                                </Link>
                            </li>
                        </template>
                    </template>

                </template>
            </template>
        </CSidebarNav>
        <!--        <CRenderFunction flat :content-to-render="$options.nav"/>-->

    </CSidebar>
</template>

<script>
// import nav from './_nav'
import {Link} from "@inertiajs/inertia-vue";
export default {
    name: 'TheSidebar',
    // nav,
    components: {
        Link,
    },
    computed: {
        show() {
            return this.$store.state.sidebarShow
        },
        minimize() {
            return this.$store.state.sidebarMinimize
        }
    },
    data() {
        return {
            menu: [
                {
                    titulo: 'Agencia',
                    submenu: [
                        {
                            Ordenes: [
                                {
                                    label: 'Nuevas Ordenes',
                                    url: '/ordenes',
                                    role: 'desing',
                                },
                                // {
                                //     label: 'Reposiciones',
                                //     url: '/reposicion',
                                //     role: 'vendor'
                                // },
                                // {
                                //     label: 'Ordenes en Espera',
                                //     url: '/espera',
                                //     role: 'vendor',
                                // },
                                // {
                                //     label: 'Ordenes en Mora',
                                //     url: '/mora',
                                //     role: 'vendor',
                                // },
                                {
                                    label: 'Buscar Ordenes',
                                    url: '/realizados',
                                    role: 'all',
                                },
                            ]
                        },
                        {
                            label: 'Recibos',
                            url: '/recibosIngreso',
                            url2: '/recibosEgreso',
                            role: 'vendor',
                        },
                        {
                            label: 'Registro de Caja',
                            url: '/cajaDebito',
                            role: 'vendor',
                        },
                        // {
                        //     label: 'Arqueo Diario',
                        //     url: '/arqueo',
                        //     role: 'vendor',
                        // },
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
                        {
                            label: 'Reporte por cliente',
                            url: '/reportes/cliente',
                            role: 'vendor',
                        },
                        {
                            label: 'Inventario',
                            url: '/inventario/ingreso',
                            role: 'vendor'
                        },
                    ]
                },
                {
                    titulo: 'Administracion',
                    submenu: [
                        {
                            label: 'Reportes',
                            url: '/admin/reportes/placas',
                            role: 'admin',
                        },
                        {
                            label: 'Productos',
                            url: '/admin/productos',
                            role: 'admin',
                        },
                        {
                            label: 'Sucursales',
                            url: '/admin/sucursales',
                            role: 'admin',
                        },
                        {
                            label: 'Clientes',
                            url: '/admin/clientes',
                            role: 'all',
                        },
                        {
                            label: 'Cajas',
                            url: '/admin/cajas',
                            role: 'admin',
                        },
                        {
                            label: 'Usuarios',
                            url: '/admin/users',
                            role: 'admin',
                        }
                    ]
                }
            ],
        }
            ;
    },
    methods: {
        getPermission(role) {
            let value = false;
            for (const key in this.$page.props.rolesP) {
                if (key == role) {
                    if (this.$page.props.rolesP[key].includes(this.$page.props.user.role)) {
                        value = true;
                        break;
                    }
                }
            }
            return value;
        },
        getUrl() {
            return window.location.pathname;
        }
    }
}
</script>
