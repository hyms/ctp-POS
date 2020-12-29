<template>
    <div class="">
        <div class="all-wrapper solid-bg-all">
            <div class="layout-w">
                <MainMenuMobile :nombre="$page.props.auth"/>
                <MainMenu :nombre="$page.props.auth"/>
                <div class="content-w">
                        <slot/>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import MainMenuMobile from "./menu/MainMenuMobile";
import MainMenu from "./menu/MainMenu";

export default {
    components: {
        MainMenu,
        MainMenuMobile
    },
    data() {
        return {
        }
    },
    methods: {},

}
</script>
