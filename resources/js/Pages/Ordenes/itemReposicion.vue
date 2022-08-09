<template>
    <v-dialog v-model="dialog" max-width="450px" scrollable persistent>
        <v-card>
            <v-card-title>
                {{ 'Reposicion de ' + titulo + ' ' + (item.codigoServicio ? item.codigoServicio : item.correlativo) }}
            </v-card-title>
            <v-card-text>
                <div><strong>Cliente:</strong> {{ item.responsable }}</div>
                <div><strong>Telefono:</strong> {{ item.telefono }}</div>
                <br>
                <v-simple-table dense class="overflow-x-auto">
                    <template v-slot:default>
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Productos</th>
                            <th>Cant.</th>
                        </tr>
                        </thead>
                        <tbody>
                        <template v-for="(product,key) in productos">
                            <tr>
                                <td>{{ key + 1 }}</td>
                                <td>{{ product.formato }} ({{ product.dimension }})</td>
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
                <div>
                    <v-textarea
                        label="Observaciones"
                        outlined dense
                        v-model="item.observaciones"
                        rows="2"
                        hide-details="auto"
                        class="my-2">
                    </v-textarea>
                </div>
            </v-card-text>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn small color="error" class="ma-1" @click="$emit('close')">
                    Cancelar
                </v-btn>
                <v-btn small color="primary" class="ma-1"
                       v-if="editedIndex>0"
                       @click="saved()"
                       :loading="sending" :disabled="sending">
                    Reponer
                </v-btn>
                <v-btn v-else small color="primary" class="ma-1"
                       outlined :href="getUrlPrint(item.id)" target="_blank">
                    Imprimir
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
import axios from "axios";

export default {
    data() {
        return {
            titulo: "Orden",
            total: 0,
            monto: 0,
            sending: false,
        }
    },
    props: {
        productos: Array,
        item: Object,
        dialog: Boolean,
        editedIndex: Number,
        productosSell: Array,
    },
    methods: {
        getProduct(id) {
            let item = {};
            for (let value of this.productos) {
                if (value.id === id) {
                    item = value;
                    break;
                }
            }
            if (item) {
                return item.formato + ' (' + item.dimension + ')';
            }
            return "";
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
            formData.append('item', JSON.stringify(this.item));
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
        saved() {
            this.sending = true;
            axios.post('/reposicion', this.loadFormData(), {headers: {'Content-Type': 'multipart/form-data'}})
                .then(({data}) => {
                    if (data["status"] === 0) {
                        this.$emit("close")
                        this.$inertia.get(data["path"])
                    } else {
                        for (let key in this.form) {
                           this.setErrors(data)
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
        getObservaciones(item) {
            if (item)
                return item.replace(/\n/g, "<br/>");
            return "";
        },
        getUrlPrint(id) {
            return '/ordenPdf/' + id;
        }
    },
}
</script>
