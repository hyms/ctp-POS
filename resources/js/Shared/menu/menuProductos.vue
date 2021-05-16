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
                    url: 'productos',
                    text: 'Productos'
                },
                {
                    active: 3,
                    url: 'tipoProductos',
                    text: 'Tipo Productos'
                },
                {
                    active: 0,
                    url: 'stocks',
                    text: 'Stocks'
                },
                {
                    active: 2,
                    url: 'movimientosStock',
                    text: 'Movimientos'
                },
            ]
        }
    }
}
</script>
