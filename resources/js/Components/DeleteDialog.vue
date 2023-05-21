<script setup>
const props = defineProps({
    onSave: Function,
    onClose: Function,
    model: Boolean,
});
</script>
<template>
    <!-- Modal Remove Product -->
    <v-dialog :model-value="model" width="auto">
        <v-card color="surface" max-width="300px">
            <v-card-text class="text-h5 text-center"
                >Estas seguro?
            </v-card-text>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn
                    small
                    variant="tonal"
                    color="secondary"
                    class="ma-1"
                    @click="onSave"
                    min-width="100"
                    >Si
                </v-btn>
                <v-btn
                    variant="elevated"
                    small
                    color="error"
                    class="ma-1"
                    min-width="100"
                    @click="onClose"
                    >Cancelar
                </v-btn>
                <v-spacer></v-spacer>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>
