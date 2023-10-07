<template>
    <v-row>
        <v-col>
            <v-card>
                <v-card-title>Bienvenid@ </v-card-title>
                <v-card-subtitle>
                  <v-alert
                      color="orange"
                      type="warning"
                      text
                  >
                    <p>El sistema sera dado de baja el 21 de octubre, favor de realizar pruebas en la nueva version</p> <v-btn href="http://104.238.212.177/" target="_blank">Nuevo Sistema v3</v-btn> <a href="http://104.238.212.177/" target="_blank">http://104.238.212.177/</a>
                  </v-alert>

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
