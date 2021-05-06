<template>
    <b-nav tabs class="m-b-20">
        <template v-for="link in links">
            <li class="nav-item">
                <inertia-link :href="link.url" :class="isActive(link.active)">{{ link.text }}</inertia-link>
            </li>
        </template>
    </b-nav>
</template>

<script>
export default {
    props: {
        active: Number,
        links: Array
    },
    methods: {
        isActive(item) {
            return 'nav-link' + ((this.active === item) ? ' active' : '')
        }
    }
};
</script>
