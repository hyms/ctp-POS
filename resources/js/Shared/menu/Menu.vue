<template>
    <ul class="metismenu" id="side-menu">
        <template v-for="(value,index) in menu">
            <li class="menu-title">{{ value.titulo }}</li>
            <template v-for="(link, key) in value.submenu">
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
                submenu: [
                    {
                        label: 'Ordenes',
                        url: '/diseño/ordenes'
                    },
                    {
                        label: 'Reposiciones',
                        url: '/diseño/reposicion'
                    },
                    {
                        label: 'Reportes',
                        url: '/diseño/reporte'
                    }]
            },
                {
                titulo: 'Administracion',
                submenu: [
                    {
                        label: 'Productos',
                        url: '/admin/productos'
                    }, {
                        label: 'Sucursales',
                        url: '/admin/sucursales'
                    }]
            }]
        };
    },
    methods: {}
};
</script>
