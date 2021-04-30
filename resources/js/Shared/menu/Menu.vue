<template>
    <ul class="nav pcoded-inner-navbar">
        <template v-for="(value,index) in menu">
            <li class="nav-item pcoded-menu-caption"><label>{{ value.titulo }}</label></li>
            <template v-for="(link, key) in value.submenu">
                <template v-if="getPermission(link.role)">
                    <li :class="'nav-item '+(($page.url === link.url)?'active':'')">
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
                            role: 'all',
                        },
                        // {
                        //     label: 'Reposiciones',
                        //     url: '/diseño/reposicion',
                        //     role: [0, 1, 3, 4]
                        // },
                        // {
                        //     label: 'Buscar Ordenes',
                        //     url: '/reporte',
                        //     role: 'desing',
                        // },
                        {
                            label: 'Ordenes en Espera',
                            url: '/espera',
                            role: 'vendor',
                        },
                        {
                            label: 'Buscar Ordenes',
                            url: '/realizados',
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
                    ]
                },
                {
                    titulo: 'Administracion',
                    submenu: [
                        {
                            label: 'Reportes',
                            url: '/admin/reportes/placas',
                            role: 'vendor',
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
            for (var i = 0; i < Object.keys(this.$page.props.roles).length; i++) {
                Object.keys(this.$page.props.roles).forEach(key => {
                    if (key == role) {
                        if (this.$page.props.roles[key].includes(this.$page.props.user.role)) {
                            value = true;
                            return;
                        }
                    }
                })
            }
            return value;
        }
    }
};
</script>
