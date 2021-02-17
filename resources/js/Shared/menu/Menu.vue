<template>
    <ul class="metismenu" id="side-menu">
        <template v-for="(value,index) in menu">
            <template v-if="value.role.includes($page.props.user.role)">
                <li class="menu-title">{{ value.titulo }}</li>
                <template v-for="(link, key) in value.submenu">
                    <template v-if="value.role.includes($page.props.user.role)">
                        <li>
                            <inertia-link
                                :href="link.url"
                                :key="key"
                            >
                                <span>{{ link.label }}</span>
                            </inertia-link>
                        </li>
                    </template>
                </template>
            </template>
        </template>
        <li>
            <inertia-link href="/logout" method="post">
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
            menu: [{
                titulo: 'Diseño',
                role: [0, 1, 3, 4],
                submenu: [
                    {
                        label: 'Ordenes',
                        url: '/diseño/ordenes',
                        role: [0, 1, 3, 4]
                    },
                    {
                        label: 'Reposiciones',
                        url: '/diseño/reposicion',
                        role: [0, 1, 3, 4]
                    },
                    {
                        label: 'Reportes',
                        url: '/diseño/reporte',
                        role: [0, 1, 3, 4]
                    }]
            },
                {
                    titulo: 'Administracion',
                    role: [0, 1],
                    submenu: [
                        {
                            label: 'Productos',
                            url: '/admin/productos',
                            role: [0, 1],
                        }, {
                            label: 'Sucursales',
                            url: '/admin/sucursales',
                            role: [0, 1],
                        }, {
                            label: 'Clientes',
                            url: '/admin/clientes',
                            role: [0, 1],
                        }, {
                            label: 'Cajas',
                            url: '/admin/cajas',
                            role: [0, 1],
                        }, {
                            label: 'Usuarios',
                            url: '/admin/users',
                            role: [0, 1],
                        }]
                }]
        };
    },
    methods: {}
};
</script>
