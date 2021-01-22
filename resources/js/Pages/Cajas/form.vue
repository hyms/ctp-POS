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
                            :options="(key==='sucursal')?sucursales:cajasPadre"
                        >
                            <template #first>
                                <b-form-select-option :value="null">Seleccione una opcion</b-form-select-option>
                            </template>
                        </b-form-select>
                    </b-form-group>
                    <b-checkbox
                        v-if="item.type==='boolean'"
                        v-model="item.value"
                        :id="key"
                        :state="item.state"
                    >{{ item.label }}
                    </b-checkbox>
                    <input
                        type="hidden"
                        v-if="item.type==='hidden'"
                        v-model="item.value"
                        :id="key"
                    />
                </template>
            </form>
        </b-modal>
    </div>
</template>

<script>
import axios from "axios";

export default {
    name: "cliente",
    props: {
        isNew: Boolean,
        id: String,
        itemRow: Object,
        sucursales: Object,
        cajasPadre: Object
    },
    data() {
        return {
            titulo1: "Nuevo Cliente",
            titulo2: "Modificar Cliente",
            form: {
                nombre: {
                    label: 'nombre',
                    value: "",
                    type: "text",
                    state: null,
                    stateText: null
                },descripcion: {
                    label: 'descripcion',
                    value: "",
                    type: "textarea",
                    state: null,
                    stateText: null
                },sucursal: {
                    label: 'sucursal',
                    value: "",
                    type: "select",
                    state: null,
                    stateText: null,
                    options: this.sucursales
                },dependeDe: {
                    label: 'Depende de',
                    value: "",
                    type: "select",
                    state: null,
                    stateText: null,
                    options: this.cajasPadre
                },enable: {
                    label: 'enable',
                    value: "",
                    type: "boolean",
                    state: null,
                    stateText: null
                },monto: {
                    label: '',
                    value: "",
                    type: "hidden",
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
                Object.keys(this.form).forEach(key => {
                    this.form[key].value = "";
                })
            } else {
                if ('id' in this.itemRow) {
                    this.idForm = this.itemRow['id'];
                }
                Object.keys(this.form).forEach(key => {
                    if (['enable'].includes(key)) {
                        this.form[key].value = (this.itemRow[key] === 1)
                    } else {
                        this.form[key].value = this.itemRow[key];
                    }
                })
            }
        },
        limpiar() {
            Object.keys(this.form).forEach(key => {
                this.form[key].state = null;
                this.form[key].stateText = null;
            })
            this.errors = [];
        },
        handleOk(bvModalEvt) {
            // Prevent modal from closing
            bvModalEvt.preventDefault();
            this.enviar();
        },
        enviar() {
            this.limpiar();
            let producto = new FormData();
            if (this.idForm) {
                producto.append('id', this.idForm);
            }
            Object.keys(this.form).forEach(key => {
                if (['dependeDe'].includes(key)) {
                    if(this.form[key].value!=="" && this.form[key].value!==null){
                        producto.append(key, this.form[key].value);
                    }
                }else if (['enable'].includes(key)) {
                    producto.append(key, this.form[key].value ? 1 : 0);
                } else {
                    producto.append(key, this.form[key].value);
                }
            })

            axios.post('/admin/caja', producto, {headers: {'Content-Type': 'multipart/form-data'}})
                .then(({data}) => {
                    if (data["status"] == 0) {
                        location.href = data["path"];
                    }
                    Object.keys(this.form).forEach(key => {
                        if (key in data.errors) {
                            this.form[key].state = false;
                            this.form[key].stateText = data.errors[key][0];
                        } else {
                            this.form[key].state = true;
                            this.form[key].stateText = "";
                        }
                    })
                })
                .catch(error => {
                    // handle error
                    this.errors = error
                    console.log(error);
                })
        }
    },
}
</script>
