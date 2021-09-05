<template>
    <div>
        <b-modal
            :id="id"
            :title="(ingreso)?titulo1:titulo2"
            @show="reset"
            @hidden="reset"
            @ok="handleOk">
            <form @submit.stop.prevent="enviar">
                <b-alert dismissible :show="errors.length">
                    {{ errors }}
                </b-alert>

                <div class="table-responsive">
                    <b-table-simple class="table-hover table-small texto-small">
                        <b-thead>
                            <b-tr>
                                <b-th>Formato</b-th>
                                <b-th>Dimension</b-th>
                                <b-th>Cant.</b-th>
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
                                        <b-form-spinbutton id="demo-sb" v-model="productosSell[key].cantidad" min="0"
                                                           max="100" size="sm" inline></b-form-spinbutton>
                                    </b-td>
                                </b-tr>
                            </template>
                        </b-tbody>
                    </b-table-simple>
                </div>
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
                    </b-form-group>
                </template>
            </form>
            <template #modal-footer="{ ok, cancel }">
                <b-button variant="danger" @click="cancel()" size="sm">
                    Cancel
                </b-button>
                <loading-button :loading="sending" :variant="'primary'" size="sm"
                                @click.native="ok()" :text="'Guardar'" :textLoad="'Guardando'">Guardar
                </loading-button>
            </template>
        </b-modal>
    </div>
</template>

<script>
import axios from "axios";
import VueBootstrapTypeahead from 'vue-bootstrap-typeahead'
import LoadingButton from '@/Shared/LoadingButton'

export default {
    name: "Orden",
    components: {
        VueBootstrapTypeahead,
        LoadingButton
    },
    props: {
        ingreso: Boolean,
        isVenta: Boolean,
        id: String,
        itemRow: Object,
        productos: Array,
        productosSell: Array,
        tipo: Number,
    },
    data() {
        return {
            sending: false,
            titulo1: "Nuevo Ingreso",
            titulo2: "Nuevo Egreso",
            form: {
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
            responsableValue: "",
            cliente: "",
            idCliente: null
        }
    },
    methods: {
        reset: function () {
            this.limpiar();
            if (this.isNew) {
                this.idForm = null;
                for (let key in this.form) {
                    this.form[key].value = "";
                }
                this.responsableValue = ""
            } else {
                if ('id' in this.itemRow) {
                    this.idForm = this.itemRow['id'];
                }
                for (let key in this.form) {
                    if (['central', 'enable'].includes(key)) {
                        this.form[key].value = (this.itemRow[key] === 1)
                    } else if (['responsable'].includes(key)) {
                        this.responsableValue = this.itemRow[key];
                    } else {
                        this.form[key].value = this.itemRow[key];
                    }
                }
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
            let producto = new FormData();
            producto.append('tipo', this.tipo);
            if (this.idForm) {
                producto.append('id', this.idForm);
            }
            if (this.responsableValue) {
                this.form.responsable.value = this.responsableValue;
            }
            for (let key in this.form) {
                producto.append(key, this.form[key].value);
            }
            if (this.idCliente) {
                producto.append('cliente', this.idCliente);
            }
            let items = [];
            for (let value of this.productosSell) {
                if (value.cantidad > 0) {
                    items = [...items, value];
                }
            }
            if (items.length > 0) {
                producto.append('productos', JSON.stringify(items));
            }
            axios.post('/inventario/' + ((this.ingreso) ? 'ingreso' : 'egreso'), producto, {headers: {'Content-Type': 'multipart/form-data'}})
                .then(({data}) => {
                    if (data["status"] == 0) {
                        this.$bvModal.hide(this.id)
                        this.$inertia.get(data["path"])
                    } else {
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
        },
        fetchOptions(text) {
            this.search(text);
        },
        async search(search) {
            if (search) {
                axios.get('/search/' + escape(search)).then(({data}) => {
                    this.options = data.items
                });
            }
        },
        selectSeach(item) {
            this.form.responsable.value = item.nombreResponsable;
        }
    },
    watch: {
        responsableValue: function (data) {
            this.search(data)
        },
        cliente: function (data) {
            this.form.telefono.value = data.telefono;
            this.idCliente = data.id
        }
    }
}
</script>
<style>
.texto-small {
    font-size: 0.85em;
}
</style>
