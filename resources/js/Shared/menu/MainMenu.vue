<template>
    <!--------------------
              START - Main Menu
              -------------------->
    <Menu/>
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
    }
};
</script>
