<template>
    <v-row>
        <v-col>
            <formOrden
                :edited-index="editedIndex"
                :edited-item="editedItem"
                :productos="productos[tipoProductoFiltro]"
                :productosSell="productosSell()"
                :tipo="tipoProductoFiltro"
                :title="titleForm"
                :dialog="dialogOrden"
                v-if="typeReport===0"
                @close="close()"
            ></formOrden>
            <delete-item
                :dialog="dialogDelete"
                :edited-index="editedIndex"
                :base-path="baseDeletePath"
                :delete-text="deleteText"
                @close="close()"
            />
            <item-orden
                :dialog="dialogItem"
                :isVenta="isVenta"
                :item="editedItem"
                :productos="productosAll"
                @close="close()"
            ></item-orden>
            <item-reposicion
                :dialog="dialogReposicion"
                :isVenta="isVenta"
                :item="editedItem"
                :edited-index="editedIndex"
                :productos="productos[tipoProductoFiltro]"
                :productosSell="productosSell()"
                @close="close()"
            ></item-reposicion>
            <v-card>
                <v-card-title>
                    <template v-if="typeReport===0" v-for="(tipoProducto,key) in tiposProductos">
                        <v-btn
                            :key="key"
                            @click="loadOrden(tipoProducto.id,tipoProducto.nombre)"
                            color="primary"
                            small
                            elevation="1"
                            class="mr-2 my-1"
                        >
                            {{ tipoProducto.nombre }}
                        </v-btn>
                    </template>
                    <formSearch
                        :report="report"
                        :estados="estados"
                        v-if="typeReport===1"
                        :tiposSelect="tiposSelect"
                    ></formSearch>
                    <template v-if="report.total">
                        <v-spacer></v-spacer>
                        <v-btn outlined color="secondary">
                            <strong>Total:</strong> {{ report.total }}
                        </v-btn>
                    </template>
                </v-card-title>
                <v-data-table
                    :items="ordenes"
                    :headers="fields"
                    :no-data-text="emptyText"
                    mobile-breakpoint="540">
                    <template v-slot:item.created_at="{item}">
                        {{ item.created_at | moment("DD/MM/YYYY HH:mm") }}
                    </template>
                    <template v-slot:item.updated_at="{item}">
                        {{ item.updated_at | moment("DD/MM/YYYY HH:mm") }}
                    </template>
                    <template v-slot:item.Acciones="{ item }">
                        <v-btn
                            small
                            class="ma-1"
                            color="secondary"
                            @click="loadOrden(item.tipoOrden,item.tipoOrdenView,item.id,item)"
                            v-if="!isVenta && viewModify(item.created_at)"
                        >
                            <v-icon>
                                mdi-file-document-edit
                            </v-icon>
                        </v-btn>
                        <v-btn
                            color="error"
                            class="ma-1"
                            small
                            @click="deleted(item.id)"
                            v-if="item.estado===1 && viewModify(item.created_at)">
                            <v-icon>
                                mdi-file-document-remove
                            </v-icon>
                        </v-btn>
                        <v-btn
                            color="primary"
                            class="ma-1"
                            small
                            @click="loadItem(item)"
                        >
                            <v-icon>
                                mdi-file-document-check
                            </v-icon>
                        </v-btn>
                        <v-btn
                            color="info"
                               class="ma-1"
                               small
                               @click="loadReposicion(item.tipoOrden,item)"
                               v-if="[0,2].includes(item.estado) && viewReposicion(item.created_at)">
                            <v-icon>
                                mdi-file-document-alert
                            </v-icon>
                        </v-btn>
                    </template>
                </v-data-table>
            </v-card>
        </v-col>
    </v-row>
</template>

<script>
import Authenticated from '@/Layouts/Authenticated.vue'
import formOrden from './form.vue'
import itemOrden from './item.vue'
import itemReposicion from './itemReposicion.vue'
import formSearch from './formSearch.vue'
import deleteItem from "@/Layouts/components/deleteItem.vue";
import moment from 'moment'

export default {
    layout: Authenticated,
    data() {
        return {
            emptyText: 'No existen Ordenes',
            deleteText: "Anular",
            titleForm: "",
            tipoProductoFiltro: null,
            fields: [],
            editedIndex: -1,
            editedItem: {},
            //dialogs
            dialogOrden: false,
            dialogDelete: false,
            dialogItem: false,
            dialogReposicion: false,
            baseDeletePath: "orden"
        }
    },
    props: {
        ordenes: Array,
        productos: Object,
        productosAll: Array,
        estados: Array,
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
        deleteItem,
    },
    methods: {
        loadOrden(tipo, title, id = -1, item = null) {
            this.loadDialog(tipo, title, id, item)
            this.dialogOrden = true;
        },
        loadItem(item) {
            this.loadDialog(null, "", item.id, item)
            this.dialogItem = true;
        },
        loadReposicion(tipoOrden, item) {
            this.loadDialog(tipoOrden, "", item.id, item)
            this.dialogReposicion = true;
        },
        loadDialog(tipo, title, id, item) {
            this.tipoProductoFiltro = tipo;
            this.editedIndex = id;
            this.editedItem = {};
            if (id > 0) {
                this.editedItem = item;
            }
            this.titleForm = title;
        },
        close() {
            this.dialogOrden = false;
            this.dialogDelete = false;
            this.dialogItem = false;
            this.dialogReposicion = false;
            this.$nextTick(() => {
                this.editedIndex = -1
                this.editedItem = Object.assign({}, {})
            })
        },
        deleted(id) {
            this.editedIndex = id
            this.dialogDelete = true
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
}
</script>
