<template>
    <div>
        <b-modal
            :id="id"
            :title="`${(isNew ? newText : modifyText)} ${titleForm}`"
            @show="reset"
            @hidden="reset"
            @ok="handleOk">
            <form @submit.stop.prevent="sendForm">
                <b-alert dismissible :show="errors.length">
                    {{ errors }}
                </b-alert>
                <template v-for="(item,key) in form">
                    <b-form-group
                        :label="item.label"
                        :label-for="key"
                        :state="item.state"
                        :invalid-feedback="item.stateText"
                        v-if="['text','password','date','textarea','email','select'].includes(item.type)"
                    >
                        <b-input
                            :type="item.type"
                            :placeholder="item.label"
                            v-model="item.value"
                            :id="key"
                            :state="item.state"
                            v-if="['text','password','date','email'].includes(item.type)"
                        ></b-input>
                        <b-textarea
                            v-if="item.type==='textarea'"
                            v-model="item.value"
                            :id="key"
                            :placeholder="item.label"
                            :state="item.state"
                        >
                        </b-textarea>
                        <b-form-select
                            v-if="item.type==='select'"
                            v-model="item.value"
                            :options="item.options"
                        >
                            <template #first>
                                <b-form-select-option :value="null">Seleccione una opcion</b-form-select-option>
                            </template>
                        </b-form-select>
                    </b-form-group>
                    <b-checkbox
                        v-if="item.type==='bool'"
                        v-model="item.value"
                        :id="key"
                        :state="item.state"
                    >{{ item.label }}
                    </b-checkbox>
                    <b-form-checkbox-group
                        v-if="item.type==='group-check'"
                        id="checkbox-group-1"
                        v-model="item.value"
                        :options="item.options"
                        value-field="id"
                        text-field="nombre"
                    ></b-form-checkbox-group>
                </template>
            </form>
            <template #modal-footer="{ ok, cancel }">
                <b-button variant="danger" @click="cancel()" size="sm">
                    Cancelar
                </b-button>
                <loading-button :loading="sending" variant="primary" size="sm"
                                @click.native="ok()" :text="'Guardar'" :textLoad="'Guardando'">Guardar
                </loading-button>
            </template>
        </b-modal>
    </div>
</template>

<script>
import axios from "axios";
import LoadingButton from './LoadingButton'

export default {
    name: "standarForm",
    props: {
        isNew: Boolean,
        id: String,
        itemRow: Object,
        titleForm: String,
        form: Object,
        urlPost: String
    },
    components: {
        LoadingButton
    },
    data() {
        return {
            sending: false,
            newText: "Nuevo",
            modifyText: "Modificar",
            idForm: null,
            errors: Array
        }
    },
    methods: {
        reset() {
            this.removeState();
            if ('id' in this.itemRow) {
                this.idForm = this.isNew ? null : this.itemRow['id'];
            }
            if (this.isNew) {
                this.removeValues();
            } else {
                this.setValues();
            }
        },
        removeState() {
            for (let key in this.form) {
                this.form[key].state = null;
                this.form[key].stateText = null;
            }
            this.errors = [];
        },
        removeValues() {
            for (let key in this.form) {
                this.form[key].value = "";
            }
        },
        setValues() {
            for (let key in this.form) {
                if (['enable'].includes(key)) {
                    this.form[key].value = (this.itemRow[key] === 1);
                } else {
                    this.form[key].value = this.itemRow[key];
                }
            }
        },
        setErrors(data) {
            for (let key in this.form) {
                if (key in data.errors) {
                    this.form[key].state = false;
                    this.form[key].stateText = data.errors[key][0];
                } else {
                    this.form[key].state = true;
                    this.form[key].stateText = "";
                }
            }
        },
        loadFormData() {
            let formData = new FormData();
            if (this.idForm) {
                formData.append('id', this.idForm);
            }
            for (let key in this.form) {
                if (this.form[key].value != null) {
                    if (['enable'].includes(key)) {
                        formData.append(key, this.form[key].value ? '1' : '0');
                    } else {
                        formData.append(key, this.form[key].value);
                    }
                }
            }
            return formData;
        },
        handleOk(bvModalEvt) {
            // Prevent modal from closing
            bvModalEvt.preventDefault();
            this.sendForm();
        },
        sendForm() {
            this.sending = true;
            this.removeState();
            axios.post(this.urlPost, this.loadFormData(), {headers: {'Content-Type': 'multipart/form-data'}})
                .then(({data}) => {
                    if (data["status"] === 0) {
                        this.$bvModal.hide(this.id)
                        this.$inertia.reload();
                    }
                    this.setErrors(data);
                })
                .catch(error => {
                    // handle error
                    this.errors = error
                    console.log(error);
                }).finally(() => {
                this.sending = false;
            })

        }
    },
}
</script>
