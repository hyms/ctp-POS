<template>
    <div class="content-w">
        <div class="content-box">
            <div class="row">
                <div class="col-sm-12">
                    <h4 class="header-title m-t-0 m-b-20">{{ titulo }}</h4>
                </div>
            </div>
            <div class="row m-b-20">
                <div class="col">
                    <b-button v-b-modal="'ordenModal'" @click="loadModal()">{{ boton1 }}</b-button>
                    <formOrden
                        :isNew="isNew"
                        id="ordenModal"
                        :itemRow="itemRow"
                        :productos="productos"
                        :productosSell="productosSell()"
                    ></formOrden>
                    <item-orden
                        id="itemModal"
                        :isVenta="isVenta"
                        :item="itemRow"
                        :productos="productos"
                    ></item-orden>
                </div>
            </div>
            <b-card>
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
                        <template v-slot:cell(central)="data">
                            {{ (data.value === 1) ? "Si" : "No" }}
                        </template>
                        <template v-slot:cell(enable)="data">
                            {{ (data.value === 1) ? "Si" : "No" }}
                        </template>
                        <template v-slot:cell(estado)="data">
                            {{ estados[data.value] }}
                        </template>
                        <template v-slot:cell(created_at)="data">
                            {{ data.value | moment("DD/MM/YYYY HH:mm") }}
                        </template>
                        <template v-slot:cell(Acciones)="row">
                            <div class="row-actions">
                                <b-button variant="dark" v-b-modal="'ordenModal'" @click="loadModal(false,row)" size="sm" v-if="!isVenta">
                                    {{ boton5 }}
                                </b-button>
                                <b-button variant="secondary" v-b-modal="'itemModal'" @click="loadModal(false,row)"
                                          size="sm">
                                    {{ boton2 }}
                                </b-button>
                                <b-button variant="danger" @click="borrar(row.item.id)" size="sm"
                                          v-if="row.item.estado==1">
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
            boton4: "Imprimir",
            boton5: "Modificar",
            textoVacio: 'No existen Ordenes',
            fields: [
                'correlativo',
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
        productos: Array,
        estados: Object,
        isVenta: Boolean,
    },
    components: {
        formOrden,
        itemOrden
    },
    methods: {
        loadModal(isNew = true, item = null) {
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
            Object.keys(this.productos).forEach(key => {
                sell[key] = {
                    id: this.productos[key].id,
                    cantidad: 0,
                    costo: this.productos[key].precioUnidad,
                    producto: this.productos[key].producto
                };
            })
            return sell;
        },
        printPdf(item) {
            this.itemRow = item.item;
        }
    },
    mounted() {
        // Set the initial number of items
        this.totalRows = this.ordenes.length
    },
}
</script>
