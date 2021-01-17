<template>
    <div id="wrapper">
        <div class="topbar">

            <!-- LOGO -->
            <div class="topbar-left">
                <inertia-link href="/" class="logo">
<span>
                            <img src="../../img/logo.png" alt="">
                        </span>
                    <i>
                        <img src="../../img/logo_sm.png" alt="">
                    </i>
                </inertia-link>
            </div>

            <nav class="navbar-custom">

                <ul class="list-unstyled topbar-right-menu float-right mb-0">
                    <li class="dropdown notification-list">
                        <a class="nav-link dropdown-toggle waves-effect waves-light nav-user" data-toggle="dropdown"
                           href="#" role="button"
                           aria-haspopup="false" aria-expanded="false">
                            <img src="../../img/users/avatar-1.jpg" alt="user" class="rounded-circle"> <span
                            class="ml-1">Anderson <i class="mdi mdi-chevron-down"></i> </span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right profile-dropdown ">
                            <inertia-link href="" class="dropdown-item notify-item">
                                <i class="ti-user"></i> <span>Mi cuenta</span>
                            </inertia-link>
                            <inertia-link href="" class="dropdown-item notify-item">
                                <i class="ti-power-off"></i> <span>Salir</span>
                            </inertia-link>
                        </div>
                    </li>

                </ul>

                <ul class="list-inline menu-left mb-0">
                    <li class="float-left">
                        <b-button v-b-toggle.sidebar-1 class="btn-sidebar">Menu</b-button>
                    </li>
                </ul>

            </nav>

        </div>

        <b-sidebar id="sidebar-1" title="" shadow>
            <MainMenu :nombre="$page.props.auth"/>
        </b-sidebar>
        <div class="left side-menu">
            <MainMenu :nombre="$page.props.auth"/>
            <!-- Sidebar -->
            <div class="clearfix"></div>

        </div>


        <div class="content-page">
            <div class="content">
                <div class="container-fluid">
                    <slot/>
                </div>

                <div class="footer">
                    <div class="pull-right hide-phone">
                        Project Completed <strong class="text-custom">57%</strong>.
                    </div>
                    <div>
                        <strong>Simple Admin</strong> - Copyright © 2017 - 2019
                    </div>
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
        return {}
    },
    methods: {},

}
</script>
