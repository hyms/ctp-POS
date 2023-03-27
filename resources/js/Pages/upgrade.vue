<template>
    <v-card>
        <v-card-text class="text-center">
            <v-alert v-model="alert" dismissible type="error">
                {{errorsMessage}}
            </v-alert>
            <template v-if="upgraded===1">
                <v-btn color="primary"
                       dark @click="upgrade" :loading="sending" :disabled="sending">{{ message }}
                </v-btn>
            </template>
            <template v-else>
                <v-btn outlined color="primary">{{ message }}</v-btn>
            </template>

        </v-card-text>
    </v-card>
</template>

<script>
import Authenticated from '@/Layouts/Guest.vue'

export default {
    layout: Authenticated,
    data() {
        return {
            sending: false,
            alert: false,
            errorsData: [],
            errorsMessage:"",
        }
    },
    props: {
        errors: Object,
        message: String,
        upgraded: Number,
    },
    methods: {
        upgrade() {
            this.sending=true;
            axios.post('/upgrade').then(({data}) => {
                this.errorsMessage=JSON.stringify(data['errorsMessage']);
                this.alert=(this.errorsMessage!=='' && this.errorsMessage!==undefined)
            }).catch(error => {
                // handle error
                this.errorsData = error;
                console.log(error);
            }).finally(() => {
                this.sending = false;
            });
        }
    }

}
</script>

