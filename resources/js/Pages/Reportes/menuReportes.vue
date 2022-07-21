<template>
    <TopMenu :active="active" :links="links"></TopMenu>
</template>

<script>
import TopMenu from "@/Shared/menu/TopMenu";

export default {
    props: {
        active: Number
    },
    components: {
        TopMenu
    },
    data() {
        return {
            links: [
                {
                    active: 0,
                    url: 'resumen',
                    text: 'Resumen'
                },
                {
                    active: 1,
                    url: 'placas',
                    text: 'Placas'
                },
                {
                    active: 2,
                    url: 'arqueos',
                    text: 'Arqueos'
                },
                {
                    active: 4,
                    url: 'mora',
                    text: 'Mora'
                },
                {
                    active: 6,
                    text: 'Rendicion Diaria',
                    url: 'rendicion',
                },
            ]
        }
    }
}
</script>
