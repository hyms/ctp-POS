<template>
    <div class="row">
        <div class="col-12">
            <div class="row mb-2">
                <div class="col">
                    <b-button v-b-modal="'sucursalModal'" variant="primary" @click="loadModal()">{{ boton1 }}</b-button>
                    <FormSucursal :isNew="isNew" id="sucursalModal" :itemRow="itemRow" :sucursalPadre="sucursalPadre"></FormSucursal>
                </div>
            </div>
            <b-card no-body>
                <b-card-header>
                    <strong>{{ titulo }}</strong>
                </b-card-header>
                <b-card-body>
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
                            {{ (data.value === 1) ? "Si" : "No" }}
                        </template>
                        <template v-slot:cell(enable)="data">
                            {{ (data.value === 1) ? "Si" : "No" }}
                        </template>
                        <template v-slot:cell(Acciones)="row">
                            <div class="row-actions">
                                <b-button variant="primary" v-b-modal="'sucursalModal'" @click="loadModal(false,row)">
                                    {{ boton2 }}
                                </b-button>
                                <b-button variant="danger" @click="borrar(row.item.id)">{{ boton3 }}</b-button>
                            </div>
                        </template>
                    </b-table>
                </b-card-body>
            </b-card>
        </div>
    </div>
</template>

<script>
import Layout from '@/Shared/Layout'
import FormSucursal from './form'

export default {
    layout: Layout,
    props: {
        sucursales: Array,
        errors: Object,
    },
    components: {
        FormSucursal
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
            for(let key in this.sucursales){
                if (item == null || item.item.id !== this.sucursales[key].id) {
                    sucursalP[this.sucursales[key].id] = this.sucursales[key].nombre;
                }
            }
            this.sucursalPadre = sucursalP;
        },
        borrar(id) {
            this.$inertia.delete(`sucursal/${id}`, {
                onBefore: () => confirm('Esta seguro?'),
            })
        }
    }
}
</script>

