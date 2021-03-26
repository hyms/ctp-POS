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
                 <b-table-simple hover small responsive>
                    <b-thead>
                        <b-tr>
                            <b-th>Formato</b-th>
                            <b-th>Dimension</b-th>
                            <b-th>Cantidad</b-th>
                            <b-th></b-th>
                        </b-tr>
                    </b-thead>
                    <b-tbody>
                        <template v-for="(product,key) in productos">
                            <b-tr>
                                <b-td>{{ product.formato }}</b-td>
                                <b-td>{{ product.dimension }}</b-td>
                                <b-td>{{ product.cantidad }}</b-td>
                                <b-td>
                                    <b-form-spinbutton id="demo-sb" v-model="productosSell[key].cantidad" min="1"
                                                       max="100" size="sm" inline></b-form-spinbutton>
                                </b-td>
                            </b-tr>
                        </template>
                    </b-tbody>
                </b-table-simple>
                <template v-for="(item,key) in form">
                    <b-form-group
                        :label="item.label"
                        :label-for="key"
                        :state="item.state"
                        :invalid-feedback="item.stateText"
                        v-if="['text','password','date','textarea','select','search'].includes(item.type)"
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
                        <vue-bootstrap-typeahead
                            :placeholder="item.label"
                            :data="options"
                            v-model="responsableValue"
                            v-if="item.type==='search'"
                            :serializer="s => s.nombreResponsable"
                            @hit="cliente = $event"
                        >
                        </vue-bootstrap-typeahead>
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
import VueBootstrapTypeahead from 'vue-bootstrap-typeahead'

export default {
    name: "Orden",
    components: {
        VueBootstrapTypeahead
    },
    props: {
        isNew: Boolean,
        id: String,
        itemRow: Object,
        productos: Array,
        productosSell: Array,

    },
    data() {
        return {
            titulo1: "Nueva Orden",
            titulo2: "Modificar Orden",
            form: {
                responsable: {
                    label: 'Cliente',
                    value: "",
                    type: "search",
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
                observaciones: {
                    label: 'Observaciones',
                    value: "",
                    type: "textarea",
                    state: null,
                    stateText: null
                }
            },
            idForm: null,
            errors: Array,
            options: [],
            responsableValue:"",
            cliente:"",
            idCliente:null
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

                this.responsableValue=""
            } else {
                if ('id' in this.itemRow) {
                    this.idForm = this.itemRow['id'];
                }
                Object.keys(this.form).forEach(key => {
                    if (['central', 'enable'].includes(key)) {
                        this.form[key].value = (this.itemRow[key] === 1)
                    }else if(['responsable'].includes(key)){
                        this.responsableValue=this.itemRow[key];
                    }
                    else {
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
            if(this.responsableValue){
                this.form.responsable.value=this.responsableValue;
            }
            Object.keys(this.form).forEach(key => {
                producto.append(key, this.form[key].value);
            })
            if(this.idCliente){
                producto.append('cliente', this.idCliente);
            }
            let items = [];
            Object.keys(this.productosSell).forEach(key => {
                if (this.productosSell[key].cantidad > 0) {
                    items = [...items, this.productosSell[key]];
                }
            })
            if (items.length > 0) {
                producto.append('productos', JSON.stringify(items));
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
        },
        fetchOptions (text) {
                this.search(text);
        },
        async search(search){
            if(search) {
                axios.get('/search/' + escape(search)).then(({data}) => {
                    this.options = data.items
                });
            }
        },
        selectSeach(item)
        {
            this.form.responsable.value=item.nombreResponsable;
        }
    },
    watch: {
        responsableValue: function(data) { this.search(data) },
        cliente: function(data) {
            this.form.telefono.value=data.telefono;
            this.idCliente=data.id
        }
    }
}
</script>
