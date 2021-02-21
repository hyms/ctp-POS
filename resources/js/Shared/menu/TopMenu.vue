<template>
    <div>
        <div class="top-bar color-scheme-light overflow-auto text-left">
            <ul class="col-12">
                <router-link
                        v-for="(link, key) in links"
                        :to="url + key"
                        :key="key"
                        tag="li"
                        exact
                        exact-active-class="active"
                >
                    <a>{{ link }}</a>
                </router-link>
            </ul>
        </div>
        <div class="content-i">
            <div class="content-box">
                <div class="row">
                    <router-view></router-view>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            url: null,
            links: {}
        }
    };
</script>
