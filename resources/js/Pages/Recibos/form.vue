<template>
    <div>
<!--        <b-modal
            :id="id"
            :title="titulo1 + ((tipo)?' de Egreso':' de Ingreso')"
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
        </b-modal>-->
    </div>
</template>

<script>
import axios from "axios";

export default {
    name: "recibo",
    props: {
        id: String,
        itemRow: Object,
        tipo: Number,
    },
    data() {
        return {
            sending: false,
            titulo1: "Nuevo Recibo",
            titulo2: "Modificar Recibo",
            form: {
                nombre: {
                    label: 'nombre',
                    value: "",
                    type: "text",
                    state: null,
                    stateText: null
                }, ciNit: {
                    label: 'ciNit',
                    value: "",
                    type: "text",
                    state: null,
                    stateText: null
                }, detalle: {
                    label: 'detalle',
                    value: "",
                    type: "textarea",
                    state: null,
                    stateText: null
                }, monto: {
                    label: 'Monto',
                    value: "",
                    type: "text",
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
            if ('id' in this.itemRow) {
                this.idForm = null;
            }
            for (let key in this.form) {
                this.form[key].value = "";
            }
        },
        limpiar() {
            for (let key in this.form) {
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
            let recibo = new FormData();
            for (let key in this.form) {
                recibo.append(key, this.form[key].value);
            }
            recibo.append('tipo',this.tipo);

            axios.post('/recibo', recibo, {headers: {'Content-Type': 'multipart/form-data'}})
                .then(({data}) => {
                    if (data["status"] === 0) {
                        this.$bvModal.hide(this.id)
                        this.$inertia.get(data["path"])
                    }
                    else {
                        for (let key in this.form) {
                            if (key in data.errors) {
                                this.form[key].state = false;
                                this.form[key].stateText = data.errors[key][0];
                            } else {
                                this.form[key].state = true;
                                this.form[key].stateText = "";
                            }
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
