<template>
    <v-row>
        <v-col>
            <v-card>
                <v-card-title>Bienvenid@ </v-card-title>
                <v-card-subtitle>
                </v-card-subtitle>
            </v-card>
        </v-col>
    </v-row>
</template>
<script>
import Authenticated from '@/Layouts/Authenticated.vue'

export default {
    props: {
        usernames: String,
        sucursal: Object,
    },
    layout: Authenticated,
}

</script>
