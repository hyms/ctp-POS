<template>
    <div class="content-w">
        <div class="content-box">
            <Menu :active="4"></Menu>
            <div class="tab-content">
                <b-row>
                    <b-col md="8">
                        <b-card>
                            <template #header>
                                <h5 class="mb-0">Resumen Mora</h5>
                            </template>
                            <form @submit.prevent="enviar">
                                <b-row>
                                    <b-col md="4" sm="6">
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
                                    <b-col>
                                        <b-button type="submit">Buscar</b-button>
                                    </b-col>
                                </b-row>
                            </form>
                        </b-card>
                    </b-col>
                    <b-col md="4">
                        <b-card>
                            <template #header>
                                <h5 class="mb-0">Total</h5>
                            </template>
                            <b-row>
                                <b-col>
                                    <h2>{{ total }}</h2>
                                </b-col>
                            </b-row>
                        </b-card>
                    </b-col>
                </b-row>

                <b-card v-if="data['table'].length>0">
                    <template #header>
                        <h5 class="mb-0">Resultados</h5>
                    </template>
                    <b-modal id="cliente"
                             :title="'Cliente: '+ item.nombreResponsable" size="lg">
                        <div class="table-responsive">
                            <b-table
                                striped
                                hover
                                :items="item.ordenes"
                                :fields="item.fields"
                                show-empty
                                small>
                                <template #empty="scope">
                                    <p>No existen deudas</p>
                                </template>
                                <template v-slot:cell(created_at)="data">
                                    {{ data.value | moment("DD/MM/YYYY HH:mm") }}
                                </template>

                                <template #cell(detalle)="row">
                                    <b-button size="sm" @click="row.toggleDetails" class="mr-2">
                                        {{ row.detailsShowing ? 'Ocultar' : 'Mostrar' }} Orden
                                    </b-button>
                                </template>

                                <template #row-details="row">
                                    <b-card>
                                        <div class="table-responsive">
                                            <table class="table">
                                                <thead>
                                                <tr>
                                                    <th scope="col">#</th>
                                                    <th scope="col">Productos</th>
                                                    <th scope="col">Cant.</th>
                                                    <th scope="col">Precio</th>
                                                    <th scope="col">Total</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr v-for="(itemOrden,key) in row.item.detallesOrden">
                                                    <td>{{ key + 1 }}</td>
                                                    <td>{{ getProduct(itemOrden.stock) }}</td>
                                                    <td>{{ itemOrden.cantidad }}</td>
                                                    <td>{{ itemOrden.costo }}</td>
                                                    <td>{{ itemOrden.costo * itemOrden.cantidad }}</td>
                                                </tr>
                                                </tbody>
                                                <tfoot>
                                                <tr>
                                                    <td colspan="4" class="text-right"><strong>Total</strong></td>
                                                    <td>{{ getTotal(row.item.detallesOrden) }}</td>
                                                </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </b-card>
                                </template>
                            </b-table>
                        </div>
                    </b-modal>
                    <div class="table-responsive">
                        <b-table
                            striped
                            hover
                            :items="data['table']"
                            :fields="data['fields']"
                            show-empty
                            small
                        >
                            <template #cell(#)="data">
                                {{ data.index + 1 }}
                            </template>
                            <template #empty="scope">
                                <p>No existen clientes</p>
                            </template>
                            <template v-slot:cell(nombreResponsable)="row">
                                <b-button variant="link" v-b-modal="'cliente'" @click="loadModal(row.item)">
                                    {{ row.item.nombreResponsable }}
                                </b-button>
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

export default {
    layout: Layout,
    props: {
        sucursales: Object,
        productos: Array,
        request: Object,
        clientes: Array,
        total: Number,
        errors: Object,
        data: Object,
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
                fechaI: {
                    label: 'Fecha Inicio',
                    value: "",
                    type: "date",
                    state: null,
                    stateText: null
                }, fechaF: {
                    label: 'Fecha Fin',
                    value: "",
                    type: "date",
                    state: null,
                    stateText: null
                },
            },
            item: {},
        }
    },
    methods: {
        enviar() {
            let form = {};
            for(let key in this.form){
                form[key] = this.form[key].value;
            }
            this.$inertia.get('/admin/reportes/mora', form)
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
        getProduct(id) {
            let item = {};
            for (let value of this.productos) {
                if (value.id == id) {
                    item = value;
                    break;
                }
            }
            if (item) {
                return item.formato + ' (' + item.dimension + ')';
            }
            return "";
        },
    },
    created() {
        for(let key in this.request){
            this.form[key].value = this.request[key];
        }
        for(let key in this.errors){
            this.form[key].state = false;
        }
    }
}
</script>
