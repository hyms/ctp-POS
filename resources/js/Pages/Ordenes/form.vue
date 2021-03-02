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
<b-row>
                <template v-for="(product,key) in productos">
                    <div class="col-sm-6 m-b-20">
                    <b-card :header="product.formato" >
                        <b-card-text>
                    Dimension: {{product.dimension}}<br>
                            Cantidad: {{ product.cantidad}}
                    <b-form-spinbutton id="demo-sb" v-model="productosSell[key].cantidad" min="1" max="100"></b-form-spinbutton>
                        </b-card-text>
                    </b-card>
                    </div>
                </template>
</b-row>
                <template v-for="(item,key) in form">
                    <b-form-group
                        :label="item.label"
                        :label-for="key"
                        :state="item.state"
                        :invalid-feedback="item.stateText"
                        v-if="['text','password','date','textarea','select'].includes(item.type)"
                    >
                        <b-input
                            :type="item.type"
                            :placeholder="item.label"
                            v-model="item.value"
                            :id="key"
                            :state="item.state"
                            v-if="['text','password','date'].includes(item.type)"
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
                            :options="sucursalPadre"
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
                </template>
            </form>
        </b-modal>
    </div>
</template>

<script>
import axios from "axios";

export default {
    name: "Orden",
    props: {
        isNew: Boolean,
        id: String,
        itemRow: Object,
        productos: Array,
        productosSell: Array
    },
    data() {
        return {
            titulo1: "Nueva Orden",
            titulo2: "Modificar Orden",
            form: {
                responsable: {
                    label: 'Cliente',
                    value: "",
                    type: "text",
                    state: null,
                    stateText: null
                },
                telefono: {
                    label: 'Telefono',
                    value: "",
                    type: "text",
                    state: null,
                    stateText: null
                },
                observaciones:{
                    label: 'Observaciones',
                    value: "",
                    type: "textarea",
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
                    if (['central', 'enable'].includes(key)) {
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
                    producto.append(key, this.form[key].value);
            })
            let items = [];
            Object.keys(this.productosSell).forEach(key => {
                if(this.productosSell[key].cantidad>0) {
                    items=[...items,this.productosSell[key]];
                }
            })
            if(items.length>0)
            {
                producto.append('productos',JSON.stringify(items));
            }
            axios.post('/diseno/orden', producto, {headers: {'Content-Type': 'multipart/form-data'}})
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
