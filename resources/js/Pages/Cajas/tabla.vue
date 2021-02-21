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
                    <b-button v-b-modal="'cajaModal'" @click="loadModal()">{{ boton1 }}</b-button>
                    <FormProducto :isNew="isNew" id="cajaModal" :itemRow="itemRow"
                                  :sucursales="sucursales" :cajasPadre="cajasPadre"></FormProducto>
                </div>
            </div>

            <div class="row m-b-20">
                <b-card>
                    <b-table
                        striped
                        hover
                        responsive
                        :items="cajas"
                        :fields="fields"
                        show-empty
                        small
                    >
                        <template #empty="scope">
                            <p>{{ textoVacio }}</p>
                        </template>
                        <template v-slot:cell(sucursal)="data">
                            {{ getSucursal(data.value) }}
                        </template>
                        <template v-slot:cell(dependeDe)="data">
                            {{ getCaja(data.value) }}
                        </template>
                        <template v-slot:cell(enable)="data">
                            {{ (data.value === 1) ? "Si" : "No" }}
                        </template>

                        <template v-slot:cell(Acciones)="row">
                            <div class="row-actions">
                                <b-button v-b-modal="'cajaModal'" @click="loadModal(false,row)">
                                    {{ boton2 }}
                                </b-button>
                                <b-button class="btn-danger" @click="borrar(row.item.id)">{{ boton3 }}</b-button>
                            </div>
                        </template>
                    </b-table>
                </b-card>
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
        cajas: Array,
        sucursales: Object,
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
            titulo: 'Cajas',
            textoVacio: 'No existen Cajas',
            fields:
                [
                    'nombre',
                    'monto',
                    'enable',
                    'sucursal',
                    'dependeDe',
                    'Acciones'
                ],
            itemRow: {},
            cajasPadre: {}
        }
    },
    methods: {
        loadModal(isNew = true, item = null) {
            this.isNew = isNew;
            this.itemRow = {};
            let cajasP = {};
            if (!isNew) {
                this.itemRow = item.item;
            }
            Object.keys(this.cajas).forEach(key => {
                if (item == null || item.item.id !== this.cajas[key].id) {
                    cajasP[this.cajas[key].id] = this.cajas[key].nombre;
                }
            })
            this.cajasPadre = cajasP;
        },
        borrar(id) {
            this.$inertia.delete(`caja/${id}`, {
                onBefore: () => confirm('Esta seguro?'),
            })
        },
        getSucursal($id) {
            return this.sucursales[$id];
        },
        getCaja($id) {
            return this.cajas[$id];
        }
    }
}
</script>

