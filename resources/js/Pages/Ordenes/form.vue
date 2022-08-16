<template>
    <v-dialog v-model="dialog" max-width="500px" scrollable persistent>
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
                                v-model="client"
                                :loading="loading"
                                :items="clients"
                                :search-input.sync="search"
                                :search-input.prop="selectSeachDemo(client)"
                                cache-items
                                hide-no-data
                                hide-selected
                                item-text="nombreResponsable"
                                label="Nombre"
                                return-object
                                dense
                                outlined
                                hide-details="auto"
                                class="my-2"
                                clearable
                            ></v-autocomplete>
                        </template>
                    </template>
                    <v-simple-table dense class="overflow-x-auto">
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
                                            single-line
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
                <v-btn small color="error" class="ma-1" @click="closed">
                    Cancelar
                </v-btn>
                <v-btn small color="primary" class="ma-1" @click="sended"
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
    components: {},
    props: {
        title: String,
        editedItem: Object,
        editedIndex: Number,
        productos: Array,
        productosSell: Array,
        tipo: Number,
        dialog: Boolean,
    },
    computed: {
        formTitle() {
            return `${this.editedIndex === -1 ? this.titleNew : this.titleModify} ${this.title}`;
        },
    },
    data() {
        return {
            //table
            titleNew: "Nuevo - ",
            titleModify: "Modificar - ",
            options: [],
            sending: false,
            //autocomplete
            client: null,
            loading: false,
            clients: [],
            //form
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
            idClient: null,
            search: null,
            errorsData: [],
            alert: false,
        }
    },
    methods: {
        closed() {
            this.alert = false;
            this.removeState();
            this.removeValues();
            this.$emit("close");
        },
        removeState() {
            for (let key in this.form) {
                this.form[key].state = null;
                this.form[key].stateText = null;
            }
            this.errorsData = [];
        },
        removeValues() {
            for (let key in this.form) {
                this.form[key].value = "";
            }
            this.client = Object.assign({}, {});
            this.clients = [];
        },
        setValues() {
            for (let key in this.form) {
                this.form[key].value = this.editedItem[key];
            }
            for (let key in this.productosSell) {
                for (let value of this.editedItem?.detallesOrden) {
                    if (this.productosSell[key].id === value.stock) {
                        this.productosSell[key].cantidad = value.cantidad;
                    }
                }
            }
            if (this.editedItem.cliente !== "") {
                this.clients.push({
                    id: this.editedItem.cliente,
                    nombreResponsable: this.editedItem.responsable,
                    telefono: this.editedItem.telefono
                })
                this.client = {
                    id: this.editedItem.cliente,
                    nombreResponsable: this.editedItem.responsable,
                    telefono: this.editedItem.telefono
                }
            }
        },
        setErrors(data) {
            this.errorsData = data.errors;
            for (let key in this.form) {
                if (key in data.errors) {
                    this.alert = true;
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
            formData.append('tipo', this.tipo);
            if (this.editedIndex > 0) {
                formData.append('id', this.editedIndex);
            }
            for (let key in this.form) {
                formData.append(key, this.form[key].value);
            }
            if (this.idClient) {
                formData.append('cliente', this.idClient);
            }
            let items = [];
            for (let value of this.productosSell) {
                if (value.cantidad > 0) {
                    items = [...items, value];
                }
            }
            if (items.length > 0) {
                formData.append('productos', JSON.stringify(items));
            }
            return formData;
        },
        sended() {
            this.sending = true;
            this.removeState();

            axios.post('/orden', this.loadFormData(), {headers: {'Content-Type': 'multipart/form-data'}})
                .then(({data}) => {
                    if (data["status"] === 0) {
                        this.closed();
                        this.$inertia.get(data["path"])
                    } else {
                        this.setErrors(data);
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
            this.idClient = item?.id
        },
        querySelections(search) {
            // Items have already been requested
            if (this.loading) {
                return
            }
            this.loading = true
            // Simulated ajax query
            axios.get('/search/' + escape(search))
                .then(({data}) => {
                    this.clients = data.items;
                })
                .finally(() => {
                    this.loading = false
                });
        }
    },
    watch: {
        search(val) {
            val && val !== this.client && this.querySelections(val)
        },
        dialog(val) {
            if (val) {
                if (this.editedIndex > 0) {
                    this.setValues();
                } else {
                    this.removeValues();
                }
            }
        }
    },
}
</script>
<style>
.texto-small {
    font-size: 0.9em !important;
}
</style>
