<template>
    <ul class="metismenu" id="side-menu">
        <li class="menu-title">Diseño</li>

        <template v-for="(link, key) in menu">
            <li>
                <inertia-link
                    :href="link.url"
                    :key="key"
                >
                    <span>{{ link.label }}</span>
                </inertia-link>
            </li>
        </template>
        <li>
            <inertia-link href="/logout" method="post" >
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
                }
            ]
        };
    },
    methods: {}
};
</script>
