<template>
    <TopMenu :active="active" :links="links"></TopMenu>
</template>

<script>
import TopMenu from "./TopMenu";

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
                    active: 1,
                    url: 'placas',
                    text: 'Placas'
                },
                {
                    active: 2,
                    url: 'arqueos',
                    text: 'Arqueos'
                },
            ]
        }
    }
}
</script>
