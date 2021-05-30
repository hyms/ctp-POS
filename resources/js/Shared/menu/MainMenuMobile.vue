<template>
    <!--------------------
              START - Mobile Menu
              -------------------->
    <div class="menu-mobile menu-activated-on-click color-scheme-dark">
        <div class="mm-logo-buttons-w">
            <inertia-link to="/" class="mm-logo">
                <img :src="images.logo" alt="logo"/><span>CTP</span>
            </inertia-link>
            <div class="mm-buttons">
                <div class="mobile-menu-trigger" v-b-toggle.my-collapse>
                    <div class="os-icon os-icon-hamburger-menu-1"></div>
                </div>
            </div>
        </div>
        <b-collapse id="my-collapse">
            <div
                class="menu-and-user"
            >
                <div class="logged-user-w">
                    <div class="avatar-w">
                        <img alt="" :src="images.avatar"/>
                    </div>
                    <div class="logged-user-info-w">
                        <div class="logged-user-name">
                            {{ nombre }}
                        </div>
                    </div>
                </div>
                <Menu/>
            </div>
        </b-collapse>
    </div>
    <!--------------------
          END - Mobile Menu
          -------------------->

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
    props: {
        nombre: String,
    },
};
</script>
