<script setup>
import DialogForm from "@/Components/dialogs/DialogForm.vue";
import {onMounted, ref, watch} from "vue";
import {labels,helpers,api} from "@/helpers";
import Snackbar from "@/Components/Snackbar.vue";

const snackbar = ref({view: false, color: '', text: ''});

const props = defineProps({
    modelValue: Boolean,
    id: {
        type: Number,
        default: null
    }
});

const emit = defineEmits(['update:modelValue','loadTable'])

function updateValue(value) {
    emit('update:modelValue', value)
}

const form = ref({});
const formItem = ref({
    id: "",
});
const loading = ref(false);

function loadData() {
    api.get({
        url: '/admin/staff/' + props.id,
        loadingItem: loading,
        onSuccess: (data) => {
            for (let key in data['staff']) {
                if (Object.keys(formItem.value).includes(key)) {
                    formItem.value[key] = data['staff'][key];
                }
            }
        }
    })
}


function saveItem() {
    if (props.id != null && props.id != undefined) {
        updateItem()
    } else {
        createItem();
    }
}

function createItem() {
    api.post({
        url: '/admin/staffs',
        params: formItem.value,
        snackbar,
        loadingItem: loading,
        onSuccess: () => {
            emit('loadTable');
            updateValue(false);
        },
    })
}

function updateItem() {
    api.put({
        url: '/admin/staffs/' + props.id,
        params: formStaff.value,
        snackbar,
        loadingItem: loading,
        onSuccess: () => {
            emit('loadTable');
            updateValue(false);
        }
    })
}

function resetForm() {
    formItem.value = {
        id: "",
    };
}

watch(
    () => [props.modelValue],
    () => {
        if (props.modelValue != undefined && props.modelValue != false) {
            resetForm()
        }
        if (props.id != null && props.id != undefined) {
            loadData()
        }
    }
);
onMounted(() => {

})
</script>
<template>
    <snackbar :text="snackbar.text" v-model="snackbar.view" :color="snackbar.color"></snackbar>
    <dialog-form
        :title="id?'edit':'create'"
        :model-value="modelValue"
        @update:modelValue="updateValue"
        :on-save="saveItem"
        :loading="loading"
    >
        <v-row>
            <v-col cols="12" sm="6">
                <v-text-field
                    v-model="formItem.first_name"
                    :label="labels.staff.first_name +' *'"
                    hide-details="auto"
                    :rules="helpers.required"
                ></v-text-field>
            </v-col>
        </v-row>
    </dialog-form>
</template>