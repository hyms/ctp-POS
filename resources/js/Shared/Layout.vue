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
<!--                <a class="mobile-menu" id="mobile-collapse1" href="javascript:"><span></span></a>-->
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
                        <!-- [ breadcrumb ] start -->
<!--                        <div class="page-header">-->
<!--                            <div class="page-block">-->
<!--                                <div class="row align-items-center">-->
<!--                                    <div class="col-md-12">-->
<!--                                        <div class="page-header-title">-->
<!--                                            <h4 class="m-b-10">{{ title }}</h4>-->
<!--                                        </div>-->
<!--&lt;!&ndash;                                        <ul class="breadcrumb">&ndash;&gt;-->
<!--&lt;!&ndash;                                            <li class="breadcrumb-item"><inertia-link href="/"><i&ndash;&gt;-->
<!--&lt;!&ndash;                                                class="feather icon-home"></i></inertia-link></li>&ndash;&gt;-->
<!--&lt;!&ndash;                                            <li class="breadcrumb-item"><a href="javascript:">Sample Page</a></li>&ndash;&gt;-->
<!--&lt;!&ndash;                                        </ul>&ndash;&gt;-->
<!--                                    </div>-->
<!--                                </div>-->
<!--                            </div>-->
<!--                        </div>-->
                        <!-- [ breadcrumb ] end -->
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

export default {
    components: {
        MainMenu,
        MainMenuMobile
    },
    data() {
        return {}
    },
    methods: {},
    props:{
        title:String
    },
    created() {
        this.$messaging.onMessage((payload) => {

            if(!window.Notification) {
                console.log('Browser does not support notifications.');
            } else {
                const notification = payload.notification;
                const notify = new Notification(notification.title, {
                    body: notification.body,
                });
            }
        });
    }
}
</script>
