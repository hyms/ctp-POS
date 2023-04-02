<script setup>
import {ref} from 'vue';
import Layout from '@/Layouts/Guest.vue';

defineProps({
    errors: Object,
    message: String,
    upgraded: Number,
});

const sending = ref(false);
const alert = ref(false);
const errorsData = ref([]);
const errorsMessage = ref("");

function upgrade() {
    sending.value = true;
    axios.post('/upgrade').then(({data}) => {
        errorsMessage.value = JSON.stringify(data['errorsMessage']);
        alert.value = (errorsMessage.value !== '' && errorsMessage.value !== undefined)
        console.log(errorsMessage.value);
    }).catch(error => {
        // handle error
        errorsData.value = error;
        console.log(error);
    }).finally(() => {
        sending.value = false;
    });
}

</script>

<template>
    <Layout>
        <v-card>
            <v-card-text class="text-center">
                <v-alert v-model="alert" closable type="error">
                    {{ errorsMessage }}
                </v-alert>
                <template v-if="upgraded===1">
                    <v-btn color="primary"
                           @click="upgrade"
                           :loading="sending"
                           :disabled="sending">
                        {{ message }}
                    </v-btn>
                </template>
                <template v-else>
                    <v-btn outlined color="primary">{{ message }}</v-btn>
                </template>

            </v-card-text>
        </v-card>
    </Layout>
</template>

