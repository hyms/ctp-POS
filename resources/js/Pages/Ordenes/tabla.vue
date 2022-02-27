<template>
    <div class="row">
        <div class="col-12">
            <formSearch :report="report" :estados="estados" v-if="typeReport===1" :tiposSelect="tiposSelect"></formSearch>
            <div class="row mb-2" v-if="typeReport===0">
                <div class="col">
                    <b-button-group>
                        <b-button v-for="(tipoProducto,key) in tiposProductos"
                                  :key="key"
                                  v-b-modal="'ordenModal'"
                                  @click="loadModal(tipoProducto.id)"
                                  variant="primary"
                        >
                            {{ boton1 + ' ' + tipoProducto.nombre }}
                        </b-button>
                    </b-button-group>
                    <formOrden
                        :isNew="isNew"
                        id="ordenModal"
                        :itemRow="itemRow"
                        :productos="productos[tipoProductoFiltro]"
                        :productosSell="productosSell()"
                        :tipo="tipoProductoFiltro"
                    ></formOrden>
                </div>
            </div>
            <b-card no-body>
                <b-card-header>
                    <strong>{{ titulo }}</strong>
                </b-card-header>
                <b-card-body>
                    <item-orden
                        id="itemModal"
                        :isVenta="isVenta"
                        :item="itemRow"
                        :productos="productosAll"
                    ></item-orden>
                    <item-reposicion
                        id="itemRModal"
                        :is-new="true"
                        :item="itemRow"
                        :productos="productos[tipoProductoFiltro]"
                        :productosSell="productosSell()"
                    ></item-reposicion>
                    <div class="table-responsive">
                        <b-table
                            striped
                            hover
                            :items="ordenes"
                            :fields="fields"
                            show-empty
                            small
                            :current-page="currentPage"
                            :per-page="perPage"
                            :sort-by.sync="sortBy"
                            :sort-desc.sync="sortDesc"
                            :sort-direction="sortDirection"
                            sticky-header
                        >
                            <template #empty="scope">
                                <p>{{ textoVacio }}</p>
                            </template>
                            <template v-slot:cell(estado)="data">
                                {{ estados[data.value] }}
                            </template>
                            <template v-slot:cell(tipoOrden)="data">
                                {{ getTipoOrden(data.value) }}
                            </template>
                            <template v-slot:cell(created_at)="data">
                                {{ data.value | moment("DD/MM/YYYY HH:mm") }}
                            </template>
                            <template v-slot:cell(Acciones)="row">
                                <div class="row-actions">
                                    <b-button variant="dark" v-b-modal="'ordenModal'"
                                              @click="loadModal(row.item.tipoOrden,false,row)"
                                              size="sm" v-if="!isVenta && viewModify(row.item.created_at)">
                                        {{ boton4 }}
                                    </b-button>
                                    <b-button variant="primary" v-b-modal="'itemModal'"
                                              @click="loadModal(row.item.tipoOrden,false,row)"
                                              size="sm">
                                        {{ boton2 }}
                                    </b-button>
                                    <b-button variant="danger" @click="borrar(row.item.id)" size="sm"
                                              v-if="row.item.estado==1 && viewModify(row.item.created_at)">
                                        {{ boton3 }}
                                    </b-button>
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
import Layout from '@/Shared/Layout'
import formOrden from './form'
import itemOrden from './item'
import itemReposicion from './itemReposicion'
import formSearch from "./formSearch";
import moment from 'moment';

export default {
    name: "Ordenes",
    layout: Layout,
    data() {
        return {
            isNew: true,
            titulo: 'Ordenes',
            boton1: "Nuevo",
            boton2: "Ver",
            boton3: "Anular",
            boton4: "Modificar",
            boton5: "Reposicion",
            textoVacio: 'No existen Ordenes',
            tipoProductoFiltro: null,
            fields: [],
            itemRow: {},
            totalRows: 1,
            currentPage: 1,
            perPage: 20,
            sortBy: '',
            sortDesc: false,
            sortDirection: 'asc',
        }
    },
    props: {
        ordenes: Array,
        productos: Object,
        productosAll: Array,
        estados: Object,
        report: Object,
        isVenta: Boolean,
        typeReport: Number,
        tiposProductos: Array,
        tiposSelect: Object,
        reposicion: Number,
    },
    components: {
        formOrden,
        itemOrden,
        itemReposicion,
        formSearch,
    },
    methods: {
        loadModal(tipo, isNew = true, item = null) {
            this.tipoProductoFiltro = tipo;
            this.isNew = isNew;
            this.itemRow = {};
            if (!isNew) {
                this.itemRow = item.item;
            }
        },
        borrar(id) {
            this.$inertia.delete(`orden/${id}`, {
                onBefore: () => confirm('Esta seguro?'),
            })
        },
        productosSell() {
            let sell = [];
            if (this.tipoProductoFiltro != null) {
                const tipoProducto = this.tipoProductoFiltro;
                for (let key in this.productos[tipoProducto]) {
                    sell[key] = {
                        id: this.productos[tipoProducto][key].id,
                        cantidad: 0,
                        costo: this.productos[tipoProducto][key].precioUnidad,
                        producto: this.productos[tipoProducto][key].producto
                    };
                }
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
        getTipoOrden(value) {
            let text = "";
            for (let tipoProducto of this.tiposProductos) {
                if (tipoProducto.id === value) {
                    text = tipoProducto.nombre;
                    break;
                }
            }
            return text;
        }
    },
    mounted() {
        // Set the initial number of items
        this.totalRows = this.ordenes.length;
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
        if(this.isVenta)
        {
            this.fields=[
                { key: 'tipoOrden', label: 'Tipo Orden', sortable: true },
                { key: 'codigoServicio', label: 'Codigo', sortable: true },
                { key: 'estado', label: 'Estado', sortable: true },
                { key: 'responsable', label: 'Cliente', sortable: true },
                { key: 'montoVenta', label: 'Monto', sortable: true },
                { key: 'created_at', label: 'Fecha', sortable: true },
                'Acciones'
            ];
        }
        else {
            this.fields=[
                { key: 'tipoOrden', label: 'Tipo Orden', sortable: true },
                { key: 'codigoServicio', label: 'Codigo', sortable: true },
                { key: 'estado', label: 'Estado', sortable: true },
                { key: 'responsable', label: 'Cliente', sortable: true },
                { key: 'telefono', label: 'Telefono', sortable: true },
                { key: 'created_at', label: 'Fecha', sortable: true },
                'Acciones'
            ];
        }
    },
    computed: {
        sortOptions() {
            // Create an options list from our fields
            return this.fields
                .filter(f => f.sortable)
                .map(f => {
                    return { text: f.label, value: f.key }
                })
        }
    },
}
</script>
