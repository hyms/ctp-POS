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
                    <b-button v-b-modal="'sucursalModal'" @click="loadModal()">{{ boton1 }}</b-button>
                    <FormProducto :isNew="isNew" id="sucursalModal" :itemRow="itemRow" :sucursalPadre="sucursalPadre"></FormProducto>
                </div>
            </div>

            <div class="row m-b-20">
                <div class="col">
                    <b-table
                        striped
                        hover
                        responsive
                        :items="sucursales"
                        :fields="fields"
                        show-empty
                        small
                    >
                        <template #empty="scope">
                            <p>{{ textoVacio }}</p>
                        </template>
                        <template v-slot:cell(central)="data">
                            {{ (data.value === 1)?"Si":"No" }}
                        </template>
                        <template v-slot:cell(enable)="data">
                            {{ (data.value === 1)?"Si":"No" }}
                        </template>
                        <template v-slot:cell(Acciones)="row">
                            <div class="row-actions">
                                <b-button v-b-modal="'sucursalModal'" @click="loadModal(false,row)">
                                    {{ boton2 }}
                                </b-button>
                                <b-button class="btn-danger" @click="borrar(row.item.id)">{{ boton3 }}</b-button>
                            </div>
                        </template>
                    </b-table>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Layout from '@/Shared/Layout'
import FormProducto from './form'

export default {
    layout: Layout,
    props: {
        sucursales: Array,
        errors: Object,
    },
    components: {
        FormProducto
    },
    data() {
        return {
            isNew: true,
            boton1: "Nuevo",
            boton2: "Modificar",
            boton3: "Borrar",
            titulo: 'Sucursales',
            textoVacio: 'No existen Sucursales',
            fields:
                [
                    'nombre',
                    'telefono',
                    'gmap',
                    'central',
                    {
                        key: 'enable',
                        label:
                            'Habilitado',
                    },
                    'Acciones'
                ],
            itemRow: {},
            sucursalPadre: {}
        }
    },
    methods: {
        loadModal(isNew = true, item = null) {
            this.isNew = isNew;
            this.itemRow = {};
            if (!isNew) {
                this.itemRow = item.item;
            }
            let sucursalP = {};
            Object.keys(this.sucursales).forEach(key => {
                if (item == null || item.item.id !== this.sucursales[key].id) {
                    sucursalP[this.sucursales[key].id] = this.sucursales[key].nombre;
                }
            })
            this.sucursalPadre = sucursalP;
        },
        borrar(id){
            this.$inertia.delete(`sucursal/${id}`, {
                onBefore: () => confirm('Esta seguro?'),
            })
        }
    }
}
</script>

