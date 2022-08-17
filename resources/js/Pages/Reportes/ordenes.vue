<template>
    <v-row>
        <v-col>
            <v-card>
                <v-card-title>
                    <v-row>
                        <template v-for="(item,key) in form">
                            <v-col cols="4">
                                <v-text-field
                                    v-if="['text','password','date','email'].includes(item.type)"
                                    :id="key"
                                    v-model="item.value"
                                    outlined
                                    dense
                                    clearable
                                    hide-details="auto"
                                    :type="item.type"
                                    :label="item.label"
                                ></v-text-field>
                                <v-select
                                    v-if="item.type==='select'"
                                    :id="key"
                                    v-model="item.value"
                                    item-text="text"
                                    item-value="value"
                                    outlined
                                    dense
                                    clearable
                                    hide-details="auto"
                                    :items="item.options"
                                    :label="item.label"
                                ></v-select>
                                <v-autocomplete
                                    v-if="item.type==='search'"
                                    v-model="item.value"
                                    :items="clientes"
                                    item-text="nombreResponsable"
                                    item-value="id"
                                    label="Cliente"
                                    dense
                                    outlined
                                    hide-details="auto"
                                    clearable
                                ></v-autocomplete>
                            </v-col>
                        </template>
                        <v-col>
                            <v-row>
                                <v-col>
                                    <v-btn left color="primary" @click="sended"
                                           :loading="sending" :disabled="sending">
                                        Consultar
                                        <v-icon right>
                                            mdi-poll
                                        </v-icon>
                                    </v-btn>
                                </v-col>
                                <v-spacer></v-spacer>
                                <v-col align="right">
                                    <v-btn right elevation="1" color="secondary"><h3>Total: {{ total }}</h3></v-btn>
                                </v-col>
                            </v-row>
                        </v-col>
                    </v-row>
                </v-card-title>
                <v-divider></v-divider>
                <v-card-text>
                    <template v-if="data['table'].length>0">
                        <v-simple-table fixed-header height="60vh">
                            <template v-slot:default>
                                <thead>
                                <tr>
                                    <template v-for="field in data['fields']">
                                        <th>
                                            <span class="text-uppercase">{{ field }}</span>
                                        </th>
                                    </template>
                                </tr>
                                </thead>
                                <tbody>
                                <template
                                    v-for="(item,key) in data['table']"
                                >
                                    <tr>
                                        <template v-for="field in data['fields']">
                                            <td>
                                                <template v-if="field==='desde'">{{ getDesde(item) }}</template>
                                                <template v-else-if="field==='hasta'">{{ getHasta(item) }}</template>
                                                <template v-else-if="field==='cantidad'">{{
                                                        getCantidad(item)
                                                    }}
                                                </template>
                                                <template v-else>{{ item[field] }}</template>
                                            </td>
                                        </template>
                                    </tr>
                                </template>
                                </tbody>
                            </template>
                        </v-simple-table>
                    </template>
                </v-card-text>
            </v-card>
        </v-col>
    </v-row>
</template>

<script>
import Authenticated from '@/Layouts/Authenticated.vue'
import moment from 'moment';
import axios from "axios";

export default {
    layout: Authenticated,
    props: {
        sucursales: Array,
        tipoOrdenes: Array,
        tipoProducto: Array,
        request: Object | Array,
        errors: Object,
        data: Object,
        clientes:Array
    },
    components: {},
    data() {
        return {
            form: {
                sucursal: {
                    label: 'Sucursales',
                    value: "",
                    type: "select",
                    state: null,
                    stateText: null,
                    options: this.sucursales,
                },
                fechaI: {
                    label: 'Fecha desde',
                    value: "",
                    type: "date",
                    state: null,
                    stateText: null
                },
                fechaF: {
                    label: 'Fecha hasta',
                    value: "",
                    type: "date",
                    state: null,
                    stateText: null
                },

                responsable: {
                    label: 'Cliente',
                    value: "",
                    type: "search",
                    state: null,
                    stateText: null
                },
                tipoOrden: {
                    label: 'Tipo de orden',
                    value: "",
                    type: "select",
                    state: null,
                    stateText: null,
                    options: this.tipoProducto,
                },
                estado: {
                    label: 'Estado',
                    value: "",
                    type: "select",
                    state: null,
                    stateText: null,
                    options: this.tipoOrdenes,
                },
            },
            item: {},
            sending: false,
            total: 0,
            client: {},
            loading:false,
            search: null,
        }
    },
    methods: {
        sended() {
            this.sending = true
            let form = {};
            for (let key in this.form) {
                form[key] = this.form[key].value;
            }
            this.$inertia.get('/admin/reportes/ordenes', form, {
                onFinish() {
                    this.sending = false;
                }
            })
        },
        loadModal(item) {
            this.item = item;
        },
        getTotal(detalle) {
            let total = 0;
            if (detalle) {
                for (let value of detalle) {
                    if (value) {
                        total += value.costo * value.cantidad;
                    }
                }
            }
            return total;
        },
    },
    created() {
        for (let key in this.request) {
            this.form[key].value = this.request[key];
        }
    },
}
</script>
