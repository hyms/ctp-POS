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
                        :item="itemRow"
                        :productos="productos"
                    ></item-orden>

                </div>
            </div>
            <div class="col">
                <b-table
                    striped
                    hover
                    responsive
                    :items="ordenes"
                    :fields="fields"
                    show-empty
                    small
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
                    <template v-slot:cell(Acciones)="row">
                        <div class="row-actions">
                            <b-button v-b-modal="'itemModal'" @click="loadModal(false,row)">
                                {{ boton2 }}
                            </b-button>
                            <b-button class="btn-danger" @click="borrar(row.item.id)">{{ boton3 }}</b-button>
                        </div>
                    </template>
                </b-table>
            </div>
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
            boton3: "Borrar",
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
        }
    },
    props: {
        ordenes: Array,
        productos: Array,
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
        borrar(id){
            this.$inertia.delete(`orden/${id}`, {
                onBefore: () => confirm('Esta seguro?'),
            })
        },
        productosSell()
        {
            let sell = [];
            Object.keys(this.productos).forEach(key=>
            {
                sell[key]={
                    id:this.productos[key].id,
                    cantidad:0,
                    costo:this.productos[key].costo,
                    producto:this.productos[key].producto
                };
            })
            return sell;
        }
    }
}
</script>
