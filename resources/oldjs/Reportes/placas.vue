<template>
    <v-row>
        <v-col>
            <v-card>
                <v-card-title>
                    <v-row>
                        <template v-for="(item,key) in form">
                            <template v-if="item.active">
                                <v-col cols="6">
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
                                </v-col>
                            </template>
                        </template>
                        <v-col>
                            <v-btn small color="primary" @click="sended"
                                   :loading="sending" :disabled="sending">
                                Consultar
                                <v-icon right>
                                    mdi-poll
                                </v-icon>
                            </v-btn>
                        </v-col>
                    </v-row>
                </v-card-title>
                <v-divider></v-divider>
                <template v-if="data['table'].length>0">
                    <v-simple-table dense fixed-header height="60vh">
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
                                            <span v-if="field!=='observaciones'">
                                                {{ (field === '#') ? key + 1 : item[field] }}</span>
                                            <span v-else v-html="item[field]"></span>
                                        </td>
                                    </template>
                                </tr>
                            </template>
                            </tbody>
                            <tfoot>
                            <tr>
                                <th colspan="5" class="text-right"><strong>Total</strong></th>
                                <template v-for="(item,key) in data['fields']">
                                    <th v-if="(key>=5) && (key<=data['fields'].length-2)"> {{ getTotal(item) }}
                                    </th>
                                </template>
                                <th></th>
                            </tr>
                            </tfoot>
                        </template>
                    </v-simple-table>
                </template>
            </v-card>
        </v-col>
    </v-row>
</template>

<script>
import Authenticated from '@/Layouts/Authenticated.vue'
import axios from "axios";
import FileDownload from 'js-file-download';

export default {
    layout: Authenticated,
    props: {
        sucursales: {
            type: [Object, Array],
            default: null
        },
        forms: Array,
        tipoPlacas: Array,
        errors: Object,
        data: Object,
        ventaReport: Boolean
    },
    components: {},
    data() {
        return {
            form: {
                sucursal: {
                    label: 'Sucursal',
                    value: "",
                    type: "select",
                    state: null,
                    stateText: null,
                    active: (!this.ventaReport)
                },
                fecha: {
                    label: 'Fecha',
                    value: "",
                    type: "date",
                    state: null,
                    stateText: null,
                    active: true,
                },
                fechahasta: {
                    label: 'hasta',
                    value: "",
                    type: "date",
                    state: null,
                    stateText: null,
                    active: (!this.ventaReport)
                },
                tipoOrden: {
                    label: 'TipoOrden',
                    value: "",
                    type: "select",
                    state: null,
                    stateText: null,
                    options: this.tipoPlacas,
                    active: true
                }
            },
            sending: false,

        }
    },
    methods: {
        sended() {
            this.sending = true;
            let form = {};
            for (let key in this.form) {
                if (this.form[key] != null) {
                    form[key] = this.form[key].value;
                }
            }
            let url = !this.ventaReport
                ? '/admin/reportes/placas'
                : '/reportes/placas';
            this.$inertia.get(url, form)
            this.sending = false;
        },
        getTotal(key) {
            let total = 0;
            for (let value of this.data['table']) {
                total += (value[key] * 1);
            }
            return total;
        },
        exportar() {
            let form = {};
            for (let key in this.form) {
                form[key] = this.form[key].value;
            }
            let url = '/admin/reportes/placasE';
            location.href = url + window.location.search;
            // "bootstrap": "^4.6.0",
        }
    },
    created() {
        for (let key in this.form) {
            this.form[key].value = this.forms[key];
        }
        for (let key in this.errors) {
            this.form[key].state = false;
        }
    }
}
</script>
<style>
.v-data-table table tfoot th {
    position: sticky;
    bottom: 0;
    background: #FFF;
}
</style>
