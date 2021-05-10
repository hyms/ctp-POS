<template>
    <div>
        <b-nav tabs class="m-b-20">
            <li class="nav-item">
                <inertia-link href="cajas" :class="isActive(1)">Cajas</inertia-link>
            </li>
            <!--            <li class="nav-item">-->
            <!--                <inertia-link href="movimientosCajas" :class="isActive(2)">Movimientos</inertia-link>-->
            <!--            </li>-->
        </b-nav>
    </div>
</template>

<script>
export default {
    props: {
        active: Number
    },
    methods: {
        isActive(item) {
            return 'nav-link' + ((this.active === item) ? ' active' : '')
        }
    }

}
</script>
