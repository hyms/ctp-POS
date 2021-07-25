<template>
    <div>
        <ul class="nav nav-tabs m-b-20">
            <li class="nav-item">
                <Link href="cajas" :class="isActive(1)">Cajas</Link>
            </li>
            <!--            <li class="nav-item">-->
            <!--                <inertia-link href="movimientosCajas" :class="isActive(2)">Movimientos</inertia-link>-->
            <!--            </li>-->
        </ul>
    </div>
</template>

<script>
import { Link } from '@inertiajs/inertia-vue'
export default {
    props: {
        active: Number
    },
    components: {
        Link,
    },
    methods: {
        isActive(item) {
            return 'nav-link' + ((this.active === item) ? ' active' : '')
        }
    }

}
</script>
