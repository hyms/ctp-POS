<template>
    <div class="content-w">
        <div class="content-box">
            <div class="row">
                <div class="col-sm-12">
                    <h4 class="header-title m-t-0 m-b-20">{{ titulo }}</h4>
                </div>
            </div>
            <formSearch :report="report" v-if="typeReport===1"></formSearch>
            <formClientSearch :report="report" v-if="typeReport===2"></formClientSearch>
            <div class="row m-b-20" v-if="typeReport===0">
                <div class="col">
                    <b-button-group>
                        <b-button v-for="(tipoProducto,key) in tiposProductos"
                                  :key="key"
                                  v-b-modal="'ordenModal'"
                                  @click="loadModal(tipoProducto.id)">
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
            <b-card>
                <item-orden
                    id="itemModal"
                    :isVenta="isVenta"
                    :item="itemRow"
                    :productos="productosAll"
                ></item-orden>
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
                                <b-button variant="secondary" v-b-modal="'itemModal'"
                                          @click="loadModal(row.item.tipoOrden,false,row)"
                                          size="sm">
                                    {{ boton2 }}
                                </b-button>
                                <b-button variant="danger" @click="borrar(row.item.id)" size="sm"
                                          v-if="row.item.estado==1 && viewModify(row.item.created_at)">
                                    {{ boton3 }}
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
            </b-card>
        </div>
    </div>
</template>

<script>
import Layout from '@/Shared/Layout'
import formOrden from './form'
import itemOrden from './item'
import formSearch from "./formSearch";
import formClientSearch from "./formClientSearch";
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
            textoVacio: 'No existen Ordenes',
            tipoProductoFiltro: null,
            fields: [
                'tipoOrden',
                'codigoServicio',
                'estado',
                'responsable',
                'telefono',
                {
                    'key': 'created_at',
                    'label': 'Fecha'
                },
                'Acciones'
            ],
            itemRow: {},
            totalRows: 1,
            currentPage: 1,
            perPage: 20,
        }
    },
    props: {
        ordenes: Array,
        productos: Object,
        productosAll: Array,
        estados: Object,
        report: Array,
        isVenta: Boolean,
        typeReport: Number,
        tiposProductos: Array,
    },
    components: {
        formOrden,
        itemOrden,
        formSearch,
        formClientSearch
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
                Object.keys(this.productos[tipoProducto]).forEach(key => {
                    sell[key] = {
                        id: this.productos[tipoProducto][key].id,
                        cantidad: 0,
                        costo: this.productos[tipoProducto][key].precioUnidad,
                        producto: this.productos[tipoProducto][key].producto
                    };
                })
            }
            return sell;
        },
        viewModify(date) {
            const today = moment();
            date = moment(date);
            return moment(today).isSame(date, 'day');
        },
        getTipoOrden(value) {
            let text = "";
            Object.keys(this.tiposProductos).forEach(key => {
                if (this.tiposProductos[key].id === value) {
                    text = this.tiposProductos[key].nombre;
                    return;
                }
            })
            return text;
        }
    },
    mounted() {
        // Set the initial number of items
        this.totalRows = this.ordenes.length;

        // // console.log('Firebase cloud messaging object', this.$messaging);
        // this.$messaging.onMessage((payload) => {
        //     console.log('Message received. ', payload);
        //     // ...
        // });
    },
    // created() {
    //     this.$messaging.onMessage((payload) => {
    //         console.log("Message received. ", payload);
    //     });
    // }
}
</script>
