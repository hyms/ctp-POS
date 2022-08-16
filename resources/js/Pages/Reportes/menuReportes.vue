<template>
</template>

<script>

export default {
    props: {
        active: Number
    },
    components: {
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
