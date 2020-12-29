<template>
    <ul class="main-menu">
        <li class="sub-header">
            <span>Diseño</span>
        </li>
        <template v-for="(link, key) in menu">
            <li>
                <inertia-link
                    :href="link.url"
                    :key="key"
                >
                    <div class="icon-w">
                        <div class=""></div>
                    </div>
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
                    url: '/diseño/orden'
                },
                {
                    label: 'Reposiciones',
                    url: '/diseño/reposicion'
                },
                {
                    label: 'Clientes',
                    url: '/diseño/clientes'
                }
            ]
        };
    },
    methods: {}
};
</script>
