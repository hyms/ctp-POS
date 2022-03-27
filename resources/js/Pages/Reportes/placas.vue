<template>
    <div class="content-w">
        <div class="content-box">
            <Menu :active="1" v-if="Object.keys(sucursales).length>0"></Menu>
            <div class="tab-content">
                <b-card>
                    <template #header>
                        <h5 class="mb-0">Filtros</h5>
                    </template>
                    <form @submit.prevent="enviar">
                        <b-row>
                            <b-col md="4" sm="6" v-if="Object.keys(sucursales).length>0">
                                <b-form-group
                                    :label="form.sucursal.label"
                                    label-for="sucursal"
                                    :state="form.sucursal.state"
                                >
                                    <b-form-select
                                        :placeholder="form.sucursal.label"
                                        v-model="form.sucursal.value"
                                        :options="sucursales"
                                        id="sucursal"
                                        :state="form.sucursal.state"
                                    >
                                        <template #first>
                                            <b-form-select-option value="">-- Seleccione una sucursal --
                                            </b-form-select-option>
                                        </template>
                                    </b-form-select>
                                </b-form-group>
                            </b-col>
                            <b-col md="4" sm="6">
                                <b-form-group
                                    :label="form.fecha.label"
                                    label-for="fecha"
                                    :state="form.fecha.state"
                                >
                                    <b-input
                                        :type="form.fecha.type"
                                        :placeholder="form.fecha.label"
                                        v-model="form.fecha.value"
                                        id="fecha"
                                        :state="form.fecha.state"
                                    ></b-input>
                                </b-form-group>
                            </b-col>
                            <b-col md="4" sm="6" v-if="Object.keys(sucursales).length>0">
                                <b-form-group
                                    :label="form.fechahasta.label"
                                    label-for="hasta"
                                    :state="form.fechahasta.state"
                                >
                                    <b-input
                                        :type="form.fechahasta.type"
                                        :placeholder="form.fechahasta.label"
                                        v-model="form.fechahasta.value"
                                        id="hasta"
                                        :state="form.fechahasta.state"
                                    ></b-input>
                                </b-form-group>
                            </b-col>
                            <b-col md="4" sm="6">
                                <b-form-group
                                    :label="form.tipoOrden.label"
                                    label-for="tipoOrden"
                                    :state="form.tipoOrden.state"
                                >
                                    <b-form-select
                                        :placeholder="form.tipoOrden.label"
                                        v-model="form.tipoOrden.value"
                                        :options="tipoPlacas"
                                        id="tipoOrden"
                                        :state="form.tipoOrden.state"
                                    >
                                        <template #first>
                                            <b-form-select-option value="">-- Seleccione un tipo --
                                            </b-form-select-option>
                                        </template>
                                    </b-form-select>
                                </b-form-group>
                            </b-col>

                        </b-row>
                        <b-row>
                            <b-col>
                                <b-button @click="enviar()" variant="primary" size="sm">Buscar</b-button>
                            </b-col>
                        </b-row>
                    </form>
                </b-card>
                <b-card v-if="data['table'].length>0">
                    <template #header>
                        <h5 class="mb-0">Registro de Placas</h5>
                        <b-button v-if="Object.keys(sucursales).length>0" @click="exportar()" variant="link">exportar</b-button>
                    </template>
                    <div class="table-responsive">
                        <b-table
                            striped
                            hover
                            :items="data['table']"
                            :fields="data['fields']"
                            show-empty
                            small
                            sticky-header
                        >
                            <template #cell(#)="data">
                                {{ data.index + 1 }}
                            </template>
                            <template v-slot:cell(observaciones)="data">
                                <span v-html="data.value"></span>
                            </template>
                            <template #custom-foot="data">
                                <b-tr>
                                    <b-th colspan="5" class="text-right"><strong>Total</strong></b-th>
                                    <template v-for="(item,key) in data['fields']">
                                        <b-th v-if="(key>=5) && (key<=data['fields'].length-2)"> {{ getTotal(item) }}
                                        </b-th>
                                    </template>
                                    <b-th></b-th>
                                </b-tr>
                            </template>

                        </b-table>
                    </div>
                </b-card>
            </div>
        </div>
    </div>
</template>

<script>
import Layout from '@/Shared/Layout'
import Menu from "./menuReportes";
import axios from "axios";
import FileDownload from 'js-file-download';

export default {
    layout: Layout,
    props: {
        sucursales: Object,
        forms: Object,
        tipoPlacas: Object,
        errors: Object,
        data: Object
    },
    components: {
        Menu
    },
    data() {
        return {
            form: {
                sucursal: {
                    label: 'Sucursal',
                    value: "",
                    type: "select",
                    state: null,
                    stateText: null
                },
                fecha: {
                    label: 'Fecha',
                    value: "",
                    type: "date",
                    state: null,
                    stateText: null
                },
                fechahasta: {
                    label: 'hasta',
                    value: "",
                    type: "date",
                    state: null,
                    stateText: null
                },
                tipoOrden: {
                    label: 'TipoOrden',
                    value: "",
                    type: "select",
                    state: null,
                    stateText: null
                }
            },
        }
    },
    methods: {
        enviar() {
            let form = {};
            for (let key in this.form) {
                form[key] = this.form[key].value;
            }
            let url = '/reportes/placas';
            if (Object.keys(this.sucursales).length > 0) {
                url = '/admin/reportes/placas';
            }
            this.$inertia.get(url, form)
        },
        getTotal(key) {
            let total = 0;
            for (let value of this.data['table']) {
                total += (value[key['key']] * 1);
            }
            return total;
        },
        exportar(){
            let form = {};
            for (let key in this.form) {
                form[key] = this.form[key].value;
            }
            let url = '/admin/reportes/placasE';
            location.href=url+window.location.search;
            // "bootstrap": "^4.6.0",
        }
    },
    created() {
        for (let key in this.form) {
            this.form[key].value = this.forms[key];
            // if (this.errors[key])) {
            //     this.form[key].state = false;
            // }
        }
        for (let key in this.errors) {
            this.form[key].state = false;
        }
    }
}
</script>

<style scoped>

</style>
