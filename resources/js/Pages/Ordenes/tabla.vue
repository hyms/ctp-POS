<template>
    <v-row>
        <v-col>
            <!--            <formSearch :report="report" :estados="estados" v-if="typeReport===1" :tiposSelect="tiposSelect"></formSearch>-->
            <formOrden
                :isNew="isNew"
                id="ordenModal"
                :itemRow="itemRow"
                :productos="productos[tipoProductoFiltro]"
                :productosSell="productosSell()"
                :tipo="tipoProductoFiltro"
                :title="titleForm"
                :dialog="dialog"
                v-if="typeReport===0"
            ></formOrden>
            <v-card>
                <v-card-title>
                    <template v-if="typeReport===0" v-for="(tipoProducto,key) in tiposProductos">
                        <v-btn
                            :key="key"
                            @click="loadModal(tipoProducto.id,tipoProducto.nombre)"
                            color="primary"
                            small
                            elevation="1"
                            class="mx-2 my-1"
                        >
                            {{ tipoProducto.nombre }}
                        </v-btn>
                    </template>
                </v-card-title>
                <!--                <item-orden-->
                <!--                    id="itemModal"-->
                <!--                    :isVenta="isVenta"-->
                <!--                    :item="itemRow"-->
                <!--                    :productos="productosAll"-->
                <!--                ></item-orden>-->
                <!--                <item-reposicion-->
                <!--                    id="itemRModal"-->
                <!--                    :is-new="true"-->
                <!--                    :item="itemRow"-->
                <!--                    :productos="productos[tipoProductoFiltro]"-->
                <!--                    :productosSell="productosSell()"-->
                <!--                ></item-reposicion>-->
                <v-data-table
                    :items="ordenes"
                    :headers="fields"
                    :no-data-text="emptyText"
                    mobile-breakpoint="540">
                </v-data-table>
                <!--                <b-table
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
                                >
                                    <template v-slot:cell(created_at)="data">
                                        {{ data.value | moment("DD/MM/YYYY HH:mm") }}
                                    </template>
                                    <template v-slot:cell(updated_at)="data">
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
                                </b-table>-->
            </v-card>
        </v-col>
    </v-row>
</template>

<script>
import Authenticated from '@/Layouts/Authenticated'
import formOrden from './form'
import itemOrden from './item'
import itemReposicion from './itemReposicion'
import formSearch from "./formSearch";
import moment from 'moment';

export default {
    name: "Ordenes",
    layout: Authenticated,
    data() {
        return {
            isNew: true,
            emptyText: 'No existen Ordenes',
            titleForm: "",
            tipoProductoFiltro: null,
            fields: [],
            itemRow: {},
            totalRows: 1,
            currentPage: 1,
            perPage: 20,
            sortBy: '',
            sortDesc: false,
            sortDirection: 'asc',
            //forms
            dialog: false,
            dialogDelete: false,
            dialogItem: false,
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
        tiposSelect: Array,
        reposicion: Number,
    },
    components: {
        formOrden,
        itemOrden,
        itemReposicion,
        formSearch,
    },
    methods: {
        loadModal(tipo, title, isNew = true, item = null) {
            this.dialog = true;
            this.tipoProductoFiltro = tipo;
            this.isNew = isNew;
            this.itemRow = {};
            if (!isNew) {
                this.itemRow = item.item;
            }
            this.titleForm = title;
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
        this.fields = this.isVenta ? [
            {value: 'tipoOrdenView', text: 'Tipo Orden'},
            {value: 'codigoServicio', text: 'Codigo'},
            {value: 'estadoView', text: 'Estado'},
            {value: 'responsable', text: 'Cliente'},
            {value: 'montoVenta', text: 'Monto'},
            {value: 'created_at', text: 'Fecha Nueva Orden'},
            {value: 'updated_at', text: 'Fecha Pago/Deuda'},
            {value: 'Acciones', text: 'Acciones'}
        ] : [
            {value: 'tipoOrdenView', text: 'Tipo Orden'},
            {value: 'codigoServicio', text: 'Codigo'},
            {value: 'estadoView', text: 'Estado'},
            {value: 'responsable', text: 'Cliente'},
            {value: 'telefono', text: 'Telefono'},
            {value: 'created_at', text: 'Fecha'},
            {value: 'Acciones', text: 'Acciones'}
        ];
    },
    computed: {
        sortOptions() {
            // Create an options list from our fields
            return this.fields
                .filter(f => f.sortable)
                .map(f => {
                    return {text: f.label, value: f.key}
                })
        }
    },
}
</script>
