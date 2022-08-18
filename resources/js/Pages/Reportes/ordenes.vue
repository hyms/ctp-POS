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

                            </v-row>
                        </v-col>
                    </v-row>
                </v-card-title>
                <v-divider></v-divider>
                <v-card-title>
                    <v-row>
                        <v-col>
                            <h5>Resumen</h5>
                            <v-menu
                                :close-on-content-click="false"
                                offset-y>
                                <template v-slot:activator="{ on, attrs }">
                                    <v-btn right elevation="1" color="secondary" class="ma-1" v-bind="attrs"
                                           v-on="on"><h3>Total Venta: {{ getTotalMonto(totals['venta']) }}</h3>
                                    </v-btn>
                                </template>

                                <v-card>
                                    <v-list>
                                        <v-list-item v-for="(value,key) in totals['venta']">
                                            <v-list-item-content>
                                                <v-list-item-title>{{key}}</v-list-item-title>
                                            </v-list-item-content>
                                            <v-list-item-action>
                                                <v-list-item-action-text><h2>{{value}}</h2></v-list-item-action-text>
                                            </v-list-item-action>
                                        </v-list-item>
                                    </v-list>
                                </v-card>
                            </v-menu>
                            <v-menu
                                :close-on-content-click="false"
                                offset-y>
                                <template v-slot:activator="{ on, attrs }">
                                    <v-btn right elevation="1" color="secondary" class="ma-1" v-bind="attrs"
                                           v-on="on"><h3>Total Deuda:  {{ getTotalMonto(totals['deuda']) }}</h3>
                                    </v-btn>
                                </template>

                                <v-card>
                                    <v-list>
                                        <v-list-item v-for="(value,key) in totals['deuda']">
                                            <v-list-item-content>
                                                <v-list-item-title>{{key}}</v-list-item-title>
                                            </v-list-item-content>
                                            <v-list-item-action>
                                                <v-list-item-action-text><h2>{{value}}</h2></v-list-item-action-text>
                                            </v-list-item-action>
                                        </v-list-item>
                                    </v-list>
                                </v-card>
                            </v-menu>
                            <v-menu
                                :close-on-content-click="false"
                                offset-y>
                                <template v-slot:activator="{ on, attrs }">
                                    <v-btn right elevation="1" color="secondary" class="ma-1" v-bind="attrs"
                                           v-on="on"><h3>Total Pagado: {{ getTotalMonto(totals['pagado']) }}</h3>
                                    </v-btn>
                                </template>

                                <v-card>
                                    <v-list>
                                        <v-list-item v-for="(value,key) in totals['pagado']">
                                            <v-list-item-content>
                                                <v-list-item-title>{{key}}</v-list-item-title>
                                            </v-list-item-content>
                                            <v-list-item-action>
                                                <v-list-item-action-text><h2>{{value}}</h2></v-list-item-action-text>
                                            </v-list-item-action>
                                        </v-list-item>
                                    </v-list>
                                </v-card>
                            </v-menu>
                            <v-menu
                                :close-on-content-click="false"
                                offset-y>
                                <template v-slot:activator="{ on, attrs }">
                                    <v-btn right elevation="1" color="secondary" class="ma-1" v-bind="attrs"
                                           v-on="on"><h3># Ordenes: {{ getTotalMonto(totals['ordenes']) }}</h3>
                                    </v-btn>
                                </template>

                                <v-card>
                                    <v-list>
                                        <v-list-item v-for="(value,key) in totals['ordenes']">
                                            <v-list-item-content>
                                                <v-list-item-title>{{key}}</v-list-item-title>
                                            </v-list-item-content>
                                            <v-list-item-action>
                                                <v-list-item-action-text><h2>{{value}}</h2></v-list-item-action-text>
                                            </v-list-item-action>
                                        </v-list-item>
                                    </v-list>
                                </v-card>
                            </v-menu>
                        </v-col>
                    </v-row>
                </v-card-title>
                <v-divider></v-divider>
                <v-data-table
                    :items="data['table']"
                    :headers="data['fields']"
                    no-data-text="No hay datos para mostrar"
                    mobile-breakpoint="540"
                >
                    <template v-slot:item.created_at="{item}">
                        {{ item.created_at | moment("DD/MM/YYYY HH:mm") }}
                    </template>
                    <template v-slot:item.updated_at="{item}">
                        {{ item.updated_at | moment("DD/MM/YYYY HH:mm") }}
                    </template>
                </v-data-table>
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
        totals: Object,
        clientes: Array
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
            loading: false,
            search: null,
            menuventa:false,
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
        getTotalMonto(items){
            let total = 0
            for(let value of Object.values(items)){
                total += value
            }
            return total
        }
    },
    created() {
        for (let key in this.request) {
            this.form[key].value = this.request[key];
        }
    },
}
</script>
