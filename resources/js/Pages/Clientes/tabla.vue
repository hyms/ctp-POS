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
                    <b-button v-b-modal="'clienteModal'" @click="loadModal()">{{ boton1 }}</b-button>
                    <FormProducto :isNew="isNew" id="clienteModal" :itemRow="itemRow" :sucursales="sucursales"></FormProducto>
                </div>
            </div>

            <div class="row m-b-20">
                <div class="col">
                    <b-table
                        striped
                        hover
                        responsive
                        :items="clientes"
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

                        <template v-slot:cell(Acciones)="row">
                            <div class="row-actions">
                                <b-button v-b-modal="'clienteModal'" @click="loadModal(false,row)">
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
        clientes: Array,
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
            titulo: 'Clientes',
            textoVacio: 'No existen Clientes',
            fields:
                [
                    'nombreCompleto',
                    'nombreResponsable',
                    'correo',
                    'telefono',
                    'sucursal',
                    'Acciones'
                ],
            itemRow: {}
        }
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
            this.$inertia.delete(`cliente/${id}`, {
                onBefore: () => confirm('Esta seguro?'),
            })
        },
        getSucursal($id)
        {
            return this.sucursales[$id];
        }
    }
}
</script>

