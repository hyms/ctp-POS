<template>
    <div>
        <b-nav tabs>
            <b-nav-item :active="(active===0)"><inertia-link href="stocks">Stoks</inertia-link></b-nav-item>
            <b-nav-item :active="(active===1)"><inertia-link href="productos">Productos</inertia-link></b-nav-item>
        </b-nav>
    </div>
</template>

<script>
export default {
props:{
    active:Number
}

}
</script>

<style scoped>

</style>
