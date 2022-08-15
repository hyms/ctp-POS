<template>
    <div class="content-w">
        <div class="content-box">
            <Menu :active="6" v-if="admin"></Menu>
            <div class="row" v-else>
                <div class="col-sm-12">
                    <h4 class="header-title m-t-0 m-b-20">Reporte por clientes</h4>
                </div>
            </div>
            <item-orden
                id="itemModal"
                :isVenta="false"
                :item="itemRow"
                :productos="productosAll"
            ></item-orden>
            <b-card>
                <b-form @submit.prevent="enviar">
                    <b-row>
                        <b-col md="4" sm="6" v-if="admin && Object.keys(sucursales).length>0">
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
                                :label="form.fechaI.label"
                                label-for="fechaI"
                                :state="form.fechaI.state"
                            >
                                <b-input
                                    :type="form.fechaI.type"
                                    :placeholder="form.fechaI.label"
                                    v-model="form.fechaI.value"
                                    id="fechaI"
                                    :state="form.fechaI.state"
                                ></b-input>
                            </b-form-group>
                        </b-col>
                        <b-col md="4" sm="6">
                            <b-form-group
                                :label="form.fechaF.label"
                                label-for="fechaF"
                                :state="form.fechaF.state"
                            >
                                <b-input
                                    :type="form.fechaF.type"
                                    :placeholder="form.fechaF.label"
                                    v-model="form.fechaF.value"
                                    id="fechaF"
                                    :state="form.fechaF.state"
                                ></b-input>
                            </b-form-group>
                        </b-col>
                        <b-col md="4" sm="6">
                            <b-form-group
                                :label="form.cliente.label"
                                label-for="cliente"
                                :state="form.cliente.state"
                            >
                                <b-form-select
                                    :placeholder="form.cliente.label"
                                    v-model="form.cliente.value"
                                    :options="clientes"
                                    id="sucursal"
                                    :state="form.cliente.state"
                                >
                                    <template #first>
                                        <b-form-select-option value="">-- Seleccione un cliente --
                                        </b-form-select-option>
                                    </template>
                                </b-form-select>
                            </b-form-group>
                        </b-col>

                    </b-row>
                    <b-row>
                        <b-col>
                            <b-button type="submit" size="sm" variant="primary">Buscar</b-button>
                        </b-col>
                    </b-row>
                </b-form>
            </b-card>
            <b-card class="mb-1">
                <b-card-body class="table-responsive">
                    <b-table
                        striped
                        hover
                        :items="data['table']"
                        :fields="data['fields']"
                        show-empty
                        small
                    >
                        <template v-slot:cell(created_at)="data">
                            {{ data.value | moment("DD/MM/YYYY HH:mm") }}
                        </template>
                        <template #empty="scope">
                            <p>No existen Datos</p>
                        </template>
                        <template v-slot:cell(Acciones)="row">
                            <div class="row-actions">
                                <b-button variant="primary" v-b-modal="'itemModal'"
                                          @click="loadModal(row.item.tipoOrden,row)"
                                          size="sm">
                                    Ver
                                </b-button>
                            </div>
                        </template>
                    </b-table>
                </b-card-body>
            </b-card>
        </div>
    </div>
</template>

<script>
import Authenticated from '@/Layouts/Authenticated'
import Menu from "./menuReportes";
import itemOrden from "../Ordenes/item";

export default {
    layout: Authenticated,
    name: "xclientes",
    components: {
        Menu,
        itemOrden,
    },
    props: {
        admin: Boolean,
        sucursales: Object,
        clientes: Object,
        request: Object,
        data: Object,
        productosAll: Array,
    },
    data() {
        return {
            form: {
                fechaI: {
                    label: 'Fecha Inicio',
                    value: "",
                    type: "date",
                    state: null,
                    stateText: null
                },
                fechaF: {
                    label: 'Fecha Final',
                    value: "",
                    type: "date",
                    state: null,
                    stateText: null
                },
                sucursal: {
                    label: 'Sucursal',
                    value: "",
                    type: "select",
                    state: null,
                    stateText: null
                },
                cliente: {
                    label: 'Cliente',
                    value: "",
                    type: "select",
                    state: null,
                    stateText: null
                },
            },
            itemRow: {},
        }
    },
    methods: {
        enviar() {
            let form = {};
            form['fechaI'] = this.form.fechaI.value;
            form['cliente'] = this.form.cliente.value;
            form['fechaF'] = this.form.fechaF.value;
            if (this.admin) {
                form['sucursal'] = this.form.sucursal.value;
            }
            this.$inertia.get(window.location.pathname, form);
        },
        loadModal(tipo, item = null) {
            this.tipoProductoFiltro = tipo;
            this.itemRow = {};
            this.itemRow = item.item;
        },
    },
    mounted() {
        for (const key in this.form) {
            if (this.request[key] !== undefined && this.request[key] !== '') {
                this.form[key].value = this.request[key];
            }
        }
    },

}
</script>
