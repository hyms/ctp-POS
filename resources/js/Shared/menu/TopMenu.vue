<template>
    <ul class="nav nav-tabs">
        <template v-for="link in links">
            <li class="nav-item">
                <Link :href="link.url" :class="isActive(link.active)">{{ link.text }}</Link>
            </li>
        </template>
    </ul>
</template>

<script>
import { Link } from '@inertiajs/inertia-vue'

export default {
    props: {
        active: Number,
        links: Array
    },
    components: {
        Link,
    },
    methods: {
        isActive(item) {
            return 'nav-link' + ((this.active === item) ? ' active' : '')
        }
    }
};
</script>
