<template>
    <!--------------------
              START - Main Menu
              -------------------->
    <div
            class="menu-w color-scheme-dark color-style-bright menu-activated-on-hover menu-has-selected-link menu-position-side menu-side-left menu-layout-compact sub-menu-style-inside"
    >
        <div class="logo-w">
            <inertia-link to="/">
                <div class="logo-element"></div>
                <div class="logo-label">
                    CTP
                </div>
            </inertia-link>
        </div>
        <div class="logged-user-w avatar-inline">
            <div class="logged-user-i">
                <div class="avatar-w">
                    <img alt="" :src="images.avatar"/>
                </div>
                <div class="logged-user-info-w">
                    <div class="logged-user-name">
                       {{nombre}}
                    </div>
                </div>
            </div>
        </div>

        <h1 class="menu-page-header">
            CTP
        </h1>

        <Menu/>

        <!--------------------
            END - Main Menu
            -------------------->
    </div>
</template>

<script>
import Menu from "./Menu";

export default {
        data() {
            return {
                images: {
                    logo: "../img/logo.png",
                    avatar: "../img/avatar1.jpg"
                },
                botonMenu: false,
            };
        },
        components: {
            Menu
        },
        props:{
            nombre: String,
        }
    };
</script>
