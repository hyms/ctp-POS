<script setup>
import {labels} from "@/helpers";
const props = defineProps({
    onSave: Function,
    modelValue: Boolean,
});
const emit = defineEmits(['update:modelValue'])
function updateValue(value) {
    emit('update:modelValue', value)
}
</script>
<template>
    <!-- Modal Remove Product -->
    <v-dialog :model-value="modelValue"  max-width="300" @update:modelValue="updateValue">
        <v-card color="surface">
            <v-card-text class="text-h5 text-center">
            Estas seguro?
            </v-card-text>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn
                    small
                     variant="tonal"
                    color="primary"
                    class="ma-1"
                    @click="onSave"
                    min-width="100"
                    >Si
                </v-btn>
                 <v-btn
                 small
                    variant="elevated"
                    color="error"
                    class="ma-1"
                    min-width="100"
                    @click="updateValue(false)"
                    >Cancelar
                </v-btn>
                <v-spacer></v-spacer>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>
