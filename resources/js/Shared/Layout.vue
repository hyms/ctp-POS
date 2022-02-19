<template>
    <div class="c-app">
        <TheSidebar/>
        <!--            <MainMenu :nombre="$page.props.auth"/>-->
        <CWrapper>
            <TheHeader/>
            <div class="c-body">
                <main class="c-main">
                    <CContainer fluid>
                        <transition name="fade" mode="out-in">
                            <slot/>
                        </transition>
                    </CContainer>
                </main>
            </div>
        </CWrapper>
    </div>

</template>

<script>
import axios from "axios";
import {Link} from "@inertiajs/inertia-vue";
import TheSidebar from "./TheSidebar";
import TheHeader from "./TheHeader";

export default {
    components: {
        Link,
        TheSidebar,
        TheHeader
    },
    data() {
        return {}
    },
    methods: {},
    props: {
        title: String
    },
    created() {
        // this.$messaging.getToken({vapidKey: "BCh0lo5r7kS5T777wXDvyN87J2j_uVSWQZy092QuigHK3ZIyYKdGjo7s7YqhRksd8qSBA7Uya_ZVEKA1Bf02L_Q"})
        //     .then((currentToken) => {
        //         if (currentToken) {
        //             let form = new FormData();
        //             form.append('token', currentToken)
        //             axios.post('/savePush', form)
        //         } else {
        //             console.log('No registration token available. Request permission to generate one.');
        //         }
        //     }).catch((err) => {
        //     console.log('An error occurred while retrieving token. ', err);
        // });
    }
}
</script>
