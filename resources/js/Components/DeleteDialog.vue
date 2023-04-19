<script setup>
const props = defineProps({
    onSave: Function,
    onClose: Function,
    model: Boolean,
});
</script>
<template>
    <!-- Modal Remove Product -->
    <v-dialog :model-value="model" max-width="300px">
        <v-card>
            <v-card-text class="text-h5 text-center"
                >Estas seguro?
            </v-card-text>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn
                    small
                    variant="elevated"
                    color="primary"
                    class="ma-1"
                    @click="onSave"
                    >Si
                </v-btn>
                <v-btn
                    variant="elevated"
                    small
                    color="error"
                    class="ma-1"
                    @click="onClose"
                    >Cancelar
                </v-btn>
                <v-spacer></v-spacer>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>
