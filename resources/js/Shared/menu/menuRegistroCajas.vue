<template>
    <div>
        <b-nav tabs class="m-b-20">
            <li class="nav-item">
                <inertia-link href="cajaDebito" :class="isActive(1)">Egresos</inertia-link>
            </li>
            <li class="nav-item">
                <inertia-link href="cajaCredito" :class="isActive(2)">Ingresos</inertia-link>
            </li>
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
            return 'nav-link'+((this.active===item)?' active':'')
        }
    }

}
</script>
