<template>
    <div class="row">
        <div class="col-12">
            <div class="row mb-2">
                <div class="col">
                    <b-button-group>
                        <b-button v-b-modal="'ordenModal'" @click="loadModal()" variant="primary">
                            {{ boton1 }}
                        </b-button>
                    </b-button-group>
                    <formOrden
                        :ingreso="(active===2)"
                        id="ordenModal"
                        :itemRow="itemRow"
                        :productos="productos"
                        :productosSell="productosSell()"
                        :tipo="tipoProductoFiltro"
                    ></formOrden>
                </div>
            </div>
            <form-search :productos="productosSelect" :report="report"></form-search>
            <b-card no-body>
                <b-card-header>
                    <strong>{{ (active === 2) ? "Ingresos" : "Egresos" }}</strong>
                </b-card-header>
                <b-card-body>
                    <div class="table-responsive">
                        <b-table
                            striped
                            hover
                            :items="movimientos"
                            :fields="fields"
                            show-empty
                            small
                            :current-page="currentPage"
                            :per-page="perPage"
                        >
                            <template #empty="scope">
                                <p>{{ textoVacio }}</p>
                            </template>
                            <template v-slot:cell(producto)="data">
                                {{ getProducto(data.value) }}
                            </template>
                            <template v-slot:cell(stockOrigen)="data">
                                {{ getStock(data.value) }}
                            </template>
                            <template v-slot:cell(stockDestino)="data">
                                {{ getStock(data.value) }}
                            </template>
                            <template v-slot:cell(created_at)="data">
                                {{ data.value | moment("DD/MM/YYYY HH:mm") }}
                            </template>
                            <template v-slot:cell(Acciones)="row">
                                <div class="row-actions">
                                    <b-button variant="info" v-b-modal="'itemRModal'"
                                              @click="loadModal(row.item.tipoOrden,false,row)"
                                              v-if="[0,2].includes(row.item.estado) && viewReposicion(row.item.created_at)">
                                        {{ boton5 }}
                                    </b-button>
                                </div>
                            </template>
                        </b-table>
                    </div>
                    <b-col>
                        <b-pagination
                            v-model="currentPage"
                            :total-rows="totalRows"
                            :per-page="perPage"
                            align="center"
                            class="my-0"
                            v-if="totalRows>perPage"
                        ></b-pagination>
                    </b-col>
                </b-card-body>
            </b-card>
        </div>
    </div>
</template>

<script>
import Authenticated from '@/Layouts/Authenticated'
import formOrden from './form'
import formSearch from "./formSearch";
import moment from 'moment';

export default {
    name: "Ordenes",
    layout: Authenticated,
    data() {
        return {
            titulo: 'Ordenes',
            boton1: "Nuevo",
            boton2: "Ver",
            boton3: "Anular",
            boton4: "Modificar",
            boton5: "Reposicion",
            textoVacio: 'No existen Ordenes',
            tipoProductoFiltro: null,
            itemRow: {},
            totalRows: 1,
            currentPage: 1,
            perPage: 20,
        }
    },
    props: {
        productos: Array,
        productosSelect: Array,
        movimientos: Array,
        stocks: Array,
        fields: Array,
        active: Number,
        report: Object
    },
    components: {
        formOrden,
        formSearch
    },
    methods: {
        loadModal() {
            this.itemRow = {};
        },
        borrar(id) {
            this.$inertia.delete(`orden/${id}`, {
                onBefore: () => confirm('Esta seguro?'),
            })
        },
        productosSell() {
            let sell = [];
            for (let key in this.productos) {
                sell[key] = {
                    id: this.productos[key].id,
                    cantidad: 0,
                    costo: this.productos[key].precioUnidad,
                    producto: this.productos[key].producto
                };
            }
            return sell;
        },
        viewModify(date) {
            const today = moment();
            date = moment(date);
            return moment(today).isSame(date, 'day');
        },
        viewReposicion(limitDay) {
            const today = moment();
            limitDay = moment(limitDay).add(this.reposicion, 'days');
            return moment(limitDay).isSameOrAfter(today, 'day');
        },
        getProducto(value) {
            let producto = "";
            for (let val of this.productos) {
                if (val['producto'] == value) {
                    producto = val['formato'] + '(' + val['dimension'] + ')';
                    break;
                }
            }
            return producto;
        },
        getStock(value) {
            let text = "";
            for (let stock of this.stocks) {
                if (stock['id'] === value) {
                    text = stock['sucursal'];
                    break;
                }
            }
            return text;
        }
    },
    mounted() {
        // Set the initial number of items
        this.totalRows = this.movimientos.length;
    },
    created() {
        this.$messaging.onMessage((payload) => {
            if (!window.Notification) {
                console.log('Browser does not support notifications.');
            } else {
                let notification = payload.notification;
                const alert = new Notification(
                    notification.title, {
                        body: notification.body,
                    });
                if (payload.data.orden) {
                    alert.addEventListener('click', () => {
                        window.open('/ordenPdf/' + payload.data.orden, '_blank');
                    });
                    if (payload.data.newOrden) {
                        if (this.ordenes[0].id <= payload.data.orden) {
                            this.$inertia.reload();
                        }
                    }
                }
            }
        });
    }
}
</script>
