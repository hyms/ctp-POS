<template>
    <div>
        <b-nav tabs class="m-b-20">
            <li class="nav-item">
                <inertia-link href="stocks" :class="isActive(0)">Stocks</inertia-link>
            </li>
            <li class="nav-item">
                <inertia-link href="productos" :class="isActive(1)">Productos</inertia-link>
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
