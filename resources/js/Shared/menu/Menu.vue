<template>
    <ul class="nav pcoded-inner-navbar">
        <template v-for="(value) in menu">
            <li class="nav-item pcoded-menu-caption"><label>{{ value.titulo }}</label></li>
            <template v-for="(link, key) in value.submenu">
                <template v-if="getPermission(link.role)">
                    <li :class="'nav-item '+(($page.url === link.url ||$page.url === link.url2)?'active':'')">
                        <inertia-link
                            :href="link.url"
                            :key="key"
                            class="nav-link"
                        >
                            <span>{{ link.label }}</span>
                        </inertia-link>
                    </li>
                </template>
            </template>
        </template>
        <li class="nav-item">
            <inertia-link href="/logout" method="post" class="nav-link ">
                <div class="icon-w">
                    <div class=""></div>
                </div>
                <span>Salir</span>
            </inertia-link>
        </li>
    </ul>
</template>

<script>
export default {
    data() {
        return {
            menu: [
                {
                    titulo: 'Agencia',
                    submenu: [
                        {
                            label: 'Nuevas Ordenes',
                            url: '/ordenes',
                            role: 'desing',
                        },
                        // {
                        //     label: 'Reposiciones',
                        //     url: '/diseño/reposicion',
                        //     role: [0, 1, 3, 4]
                        // },
                        {
                            label: 'Ordenes en Espera',
                            url: '/espera',
                            role: 'vendor',
                        },
                        {
                            label: 'Ordenes en Mora',
                            url: '/mora',
                            role: 'vendor',
                        },
                        {
                            label: 'Buscar Ordenes',
                            url: '/realizados',
                            role: 'all',
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
                        {
                            label: 'Arqueo Diario',
                            url: '/arqueo',
                            role: 'vendor',
                        },
                        {
                            label: 'Registro Diario',
                            url: '/reportes/placas',
                            role: 'vendor',
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
        };
    },
    methods: {
        getPermission(role) {
            let value = false;
            for (const key in this.$page.props.roles) {
                if (key == role) {
                    if (this.$page.props.roles[key].includes(this.$page.props.user.role)) {
                        value = true;
                        break;
                    }
                }
            }
            return value;
        }
    }
};
</script>
