<template>
    <div>
        <ul class="nav nav-tabs m-b-20">
            <li class="nav-item">
                <Link href="cajaDebito" :class="isActive(1)">Egresos</Link>
            </li>
            <li class="nav-item">
                <Link href="cajaCredito" :class="isActive(2)">Ingresos</Link>
            </li>
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
