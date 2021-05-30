<template>
    <div id="wrapper">
        <!-- [ navigation menu ] start -->
        <nav class="pcoded-navbar">
            <div class="navbar-wrapper">
                <div class="navbar-brand header-logo">
                    <inertia-link href="/" class="b-brand">
                        <div class="b-bg">
                            <i class="feather icon-trending-up"></i>
                        </div>
                        <span class="b-title">XCTP</span>
                    </inertia-link>
                </div>
                <div class="navbar-content scroll-div">
                    <MainMenu :nombre="$page.props.auth"/>
                </div>
            </div>
        </nav>
        <!-- [ navigation menu ] end -->
        <!-- [ Header ] start -->
        <header class="navbar pcoded-header navbar-expand-lg navbar-light">
            <div class="m-header">
                <a class="mobile-menu" id="mobile-collapse1" v-b-toggle.sidebar-1>
                    <span></span>
                </a>
                <inertia-link href="/" class="b-brand">
                    <div class="b-bg">
                        <i class="feather icon-trending-up"></i>
                    </div>
                    <span class="b-title">XCTP</span>
                </inertia-link>
            </div>
            <b-sidebar
                id="sidebar-1"
                shadow
                class="pcoded-navbar"
                body-class="slimScrollDiv"
                backdrop-variant="dark"
                backdrop
                bg-variant="dark"
                text-variant="light"
            >
                <MainMenu :nombre="$page.props.auth"/>
            </b-sidebar>
        </header>
        <!-- [ Header ] end -->
        <!-- [ Main Content ] start -->
        <div class="pcoded-main-container">
            <div class="pcoded-wrapper">
                <div class="pcoded-content">
                    <div class="pcoded-inner-content">
                        <div class="main-body">
                            <div class="page-wrapper">
                                <slot/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- [ Main Content ] end -->

    </div>
</template>

<script>
import MainMenuMobile from "./menu/MainMenuMobile";
import MainMenu from "./menu/MainMenu";
import axios from "axios";

export default {
    components: {
        MainMenu,
        MainMenuMobile
    },
    data() {
        return {}
    },
    methods: {},
    props: {
        title: String
    },
    created() {
        this.$messaging.getToken({vapidKey: "BCh0lo5r7kS5T777wXDvyN87J2j_uVSWQZy092QuigHK3ZIyYKdGjo7s7YqhRksd8qSBA7Uya_ZVEKA1Bf02L_Q"})
            .then((currentToken) => {
                if (currentToken) {
                    let form = new FormData();
                    form.append('token', currentToken)
                    axios.post('/savePush', form)
                } else {
                    console.log('No registration token available. Request permission to generate one.');
                }
            }).catch((err) => {
            console.log('An error occurred while retrieving token. ', err);
        });
    }
}
</script>
