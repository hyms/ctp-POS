<script setup>
import {ref} from "vue";
import {labels} from "@/helpers";

const props = defineProps({
    onFilter: Function,
    size: {
        type: String,
        default: 'default'
    },
    modelValue: Boolean,
});
const emit = defineEmits(['update:modelValue'])

function updateValue(value) {
    emit('update:modelValue', value)
}

function onSave() {
    if (props.onFilter != undefined && typeof (props.onFilter) === 'function') {
        props.onFilter()
    }
}

</script>
<template>
    <v-menu :model-value="modelValue" @update:modelValue="updateValue" :close-on-content-click="false"
            location="bottom">
        <template v-slot:activator="{ props }">
            <v-btn
                color="primary"
                variant="outlined"
                :size="size"
                v-bind="props"
                prepend-icon="fas fa-filter"
            >
                {{ labels.btn.filters }}
            </v-btn>
        </template>
        <v-card max-width="500" class="pt-2">
            <v-card-text>
                <slot></slot>
            </v-card-text>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn
                    variant="text"
                    size="small"
                    color="error"
                    @click="updateValue(false)"
                >
                    {{ labels.btn.cancel }}
                </v-btn>
                <v-btn
                    variant="tonal"
                    size="small"
                    color="primary"
                    @click="onSave()"
                >
                    {{ labels.btn.filter }}
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-menu>
</template>