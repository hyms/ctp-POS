<template>
    <TopMenu :links="links" :active="active"></TopMenu>
</template>

<script>
import { Link } from '@inertiajs/inertia-vue'
import TopMenu from "@/Shared/menu/TopMenu";
export default {
    props: {
        active: Number
    },
    components: {
        Link,
        TopMenu
    },
    data(){
        return{
            links: [
                {
                    active: 1,
                    url: 'cajaDebito',
                    text: 'Egresos'
                },
                {
                    active: 2,
                    url: 'cajaCredito',
                    text: 'Ingresos'
                },
            ]
        }
    }
}
</script>
