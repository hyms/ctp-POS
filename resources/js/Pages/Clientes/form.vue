<template>
    <div>
        <b-modal
            :id="id"
            :title="(isNew)?titulo1:titulo2"
            @show="reset"
            @hidden="reset"
            @ok="handleOk">
            <form @submit.stop.prevent="enviar">
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
                            :placeholder="item.label"
                            v-model="item.value"
                            :id="key"
                            :state="item.state"
                        ></b-textarea>
                        <b-form-select
                            v-if="item.type==='select'"
                            v-model="item.value"
                            :options="sucursales"
                        ></b-form-select>
                    </b-form-group>
                </template>
            </form>
            <template #modal-footer="{ ok, cancel }">
                <b-button variant="danger" @click="cancel()" size="sm">
                    Cancel
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
import LoadingButton from '@/Shared/LoadingButton'

export default {
    name: "cliente",
    props: {
        isNew: Boolean,
        id: String,
        itemRow: Object,
        sucursales: Object
    },
    components: {
        LoadingButton
    },
    data() {
        return {
            sending: false,
            titulo1: "Nuevo Cliente",
            titulo2: "Modificar Cliente",
            form: {
                nombreCompleto: {
                    label: 'nombreCompleto',
                    value: "",
                    type: "text",
                    state: null,
                    stateText: null
                }, nombreNegocio: {
                    label: 'nombreNegocio',
                    value: "",
                    type: "text",
                    state: null,
                    stateText: null
                }, nombreResponsable: {
                    label: 'nombreResponsable',
                    value: "",
                    type: "text",
                    state: null,
                    stateText: null
                }, correo: {
                    label: 'correo',
                    value: "",
                    type: "email",
                    state: null,
                    stateText: null
                }, telefono: {
                    label: 'telefono',
                    value: "",
                    type: "text",
                    state: null,
                    stateText: null
                }, direccion: {
                    label: 'direccion',
                    value: "",
                    type: "text",
                    state: null,
                    stateText: null
                }, nitCi: {
                    label: 'nitCi',
                    value: "",
                    type: "text",
                    state: null,
                    stateText: null
                }, codigo: {
                    label: 'codigo',
                    value: "",
                    type: "text",
                    state: null,
                    stateText: null
                }, sucursal: {
                    label: 'sucursal',
                    value: "",
                    type: "select",
                    state: null,
                    stateText: null
                }
            },
            idForm: null,
            errors: Array
        }
    },
    methods: {
        reset() {
            this.limpiar();

            if (this.isNew) {
                if ('id' in this.itemRow) {
                    this.idForm = null;
                }
                for(let key in this.form){
                    this.form[key].value = "";
                }
            } else {
                if ('id' in this.itemRow) {
                    this.idForm = this.itemRow['id'];
                }
                for(let key in this.form){
                    if (['central', 'enable'].includes(key)) {
                        this.form[key].value = (this.itemRow[key] === 1)
                    } else {
                        this.form[key].value = this.itemRow[key];
                    }
                }
            }
        },
        limpiar() {
            for(let key in this.form){
                this.form[key].state = null;
                this.form[key].stateText = null;
            }
            this.errors = [];
        },
        handleOk(bvModalEvt) {
            // Prevent modal from closing
            bvModalEvt.preventDefault();
            this.enviar();
        },
        enviar() {
            this.sending = true;
            this.limpiar();
            let producto = new FormData();
            if (this.idForm) {
                producto.append('id', this.idForm);
            }
            for(let key in this.form){
                if (['central', 'enable'].includes(key)) {
                    producto.append(key, this.form[key].value ? 1 : 0);
                } else {
                    producto.append(key, this.form[key].value);
                }
            }

            axios.post('/admin/cliente', producto, {headers: {'Content-Type': 'multipart/form-data'}})
                .then(({data}) => {
                    if (data["status"] == 0) {
                        this.$bvModal.hide(this.id)
                        this.$inertia.get(data["path"])
                    }
                    for(let key in this.form){
                        if (key in data.errors) {
                            this.form[key].state = false;
                            this.form[key].stateText = data.errors[key][0];
                        } else {
                            this.form[key].state = true;
                            this.form[key].stateText = "";
                        }
                    }
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
