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
import Authenticated from '@/Layouts/Authenticated'

export default {
    props: {
        usernames: String,
        sucursal: Object,
    },
    layout: Authenticated,
}

</script>
