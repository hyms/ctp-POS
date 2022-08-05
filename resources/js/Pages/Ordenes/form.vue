<template>
    <v-dialog v-model="dialog" max-width="500px" scrollable>
        <v-card>
            <v-card-title>{{ formTitle }}</v-card-title>
            <v-card-text>
                <form>
                    <v-alert
                        dismissible
                        class="mb-4"
                        v-if="Object.keys(errorsData).length > 0"
                        v-model="alert"
                        color="red"
                        outlined
                        text
                    >
                        <ul class="text-sm">
                            <li v-for="(error, key) in errorsData" :key="key">{{ key }}: {{ error }}</li>
                        </ul>
                    </v-alert>
                    <template v-for="(item,key) in form">
                        <template v-if="['text','password','date','textarea','select','search'].includes(item.type)">
                            <v-text-field
                                v-model="item.value"
                                :id="key"
                                v-if="['text','password','date'].includes(item.type)"
                                outlined
                                dense
                                hide-details="auto"
                                :type="item.type"
                                :label="item.label"
                                :error="item.state"
                                :error-messages="item.stateText"
                                class="my-2"
                            ></v-text-field>
                            <v-textarea
                                v-if="item.type==='textarea'"
                                :id="key"
                                v-model="item.value"
                                rows="2"
                                outlined
                                dense
                                hide-details="auto"
                                :label="item.label"
                                :error="item.state"
                                :error-messages="item.stateText"
                                class="my-2"
                            ></v-textarea>
                            <v-autocomplete
                                v-if="item.type==='search'"
                                v-model="responsableValue"
                                :loading="loading"
                                :items="clients"
                                :search-input.sync="search"
                                :search-input.prop="selectSeachDemo(responsableValue)"
                                hide-no-data
                                hide-selected
                                item-text="nombreResponsable"
                                label="Nombre"
                                return-object
                                dense
                                outlined
                                hide-details="auto"
                                class="my-2"
                            ></v-autocomplete>
                        </template>
                    </template>
                    <v-simple-table dense class="overflow-x-auto" >
                        <template v-slot:default>
                            <thead>
                            <tr>
                                <th>Formato</th>
                                <th>Dimension</th>
                                <th>Cant.</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            <template v-for="(product,key) in productos">
                                <tr>
                                    <td>{{ product.formato }}</td>
                                    <td>{{ product.dimension }}</td>
                                    <td>{{ product.cantidad }}</td>
                                    <td>
                                        <v-text-field
                                            type="number"
                                            v-model="productosSell[key].cantidad"
                                            outlined
                                            dense
                                            style="min-width: 50px; max-width: 100px"
                                            class="my-1 texto-small"
                                            hide-details="auto"
                                            min="0"></v-text-field>
                                    </td>
                                </tr>
                            </template>
                            </tbody>
                        </template>
                    </v-simple-table>
                </form>
            </v-card-text>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn small color="error" class="ma-1" @click="close">
                    Cancelar
                </v-btn>
                <v-btn small color="primary" class="ma-1" @click="enviar"
                       :loading="sending" :disabled="sending">
                    Guardar
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
import axios from "axios";

export default {
    name: "Orden",
    components: {},
    props: {
        isNew: Boolean,
        title: String,
        id: String,
        itemRow: Object,
        productos: Array,
        productosSell: Array,
        tipo: Number,
        dialog: {
            type: Boolean,
            default: false
        },
    },
    computed: {
        formTitle() {
            return `${this.isNew ? this.titulo1 : this.titulo2} ${this.title}`;
        },
    },
    data() {
        return {
            sending: false,
            titulo1: "Nuevo - ",
            titulo2: "Modificar - ",
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
            idForm: -1,
            errorsData: [],
            options: [],
            responsableValue: "",
            cliente: "",
            idCliente: null,
            loading: false,
            clients: [],
            search: null,
            alert: false,
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
                for (let key in this.productosSell) {
                    for (let value of this.itemRow.detallesOrden) {
                        if (this.productosSell[key].id === value.stock) {
                            this.productosSell[key].cantidad = value.cantidad;
                        }
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
            axios.post('/orden', producto, {headers: {'Content-Type': 'multipart/form-data'}})
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
        //autoComplete
        selectSeachDemo(item) {
            this.form.responsable.value = item?.nombreResponsable;
            this.form.telefono.value = item?.telefono;
            this.idCliente = item?.id
        },
        querySelections(v) {
            // Items have already been requested
            if (this.isLoading) return
            this.loading = true
            // Simulated ajax query
            axios.get('/search/' + escape(v)).then(({data}) => {
                this.clients = data.items;
            })
                .finally(() => {
                    this.loading = false
                });
        }
    },
    watch: {
        search(val) {
            val && val !== this.responsableValue && this.querySelections(val)
        },
        dialog(val) {
            val || this.close()
        },
    }
}
</script>
<style>
.texto-small {
    font-size: 0.85em !important;
}
</style>
