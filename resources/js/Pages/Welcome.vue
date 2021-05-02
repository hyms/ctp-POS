<template>
    <div class="content-w">
        <div class="content-box">
            <h2>Bienvenid@
                <b-badge>{{ usernames }}</b-badge>
            </h2>
            <h3>Sucursal:
                <b-badge>{{ sucursal.nombre }}</b-badge>
            </h3>
        </div>
    </div>
</template>
<script>
import Layout from '@/Shared/Layout'

export default {
    props: {
        usernames: String,
        sucursal: Object,
    },
    layout: Layout,
}

</script>
