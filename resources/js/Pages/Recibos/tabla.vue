<template>
    <div class="content-w">
        <div class="content-box">
<!--            <Menu :active="1"></Menu>-->
            <h3>Recibos</h3>
            <div class="tab-content">

                <div class="row m-b-20">
                    <b-card>
                        <b-table
                            striped
                            hover
                            :items="recibos"
                            :fields="fields"
                            show-empty
                            small
                        >
                            <template #empty="scope">
                                <p>{{ textoVacio }}</p>
                            </template>
                            <template v-slot:cell(created_at)="data">
                                {{ data.value | moment("DD/MM/YYYY HH:mm") }}
                            </template>
                            <template v-slot:cell(Acciones)="row">
                                <div class="row-actions">
                                    <b-button size="sm" v-b-modal="'cajaModal'" @click="loadModal(false,row)">
                                        {{ boton2 }}
                                    </b-button>
                                    <b-button size="sm" class="btn-danger" @click="borrar(row.item.id)">{{
                                            boton3
                                        }}
                                    </b-button>
                                </div>
                            </template>
                        </b-table>
                    </b-card>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Layout from '@/Shared/Layout'
// import FormProducto from './form'
// import Menu from '@/Shared/menu/menuCajas';

export default {
    layout: Layout,
    props: {
        recibos: Array,
        errors: Object,
    },
    components: {
        // FormProducto,
        // Menu
    },
    data() {
        return {
            isNew: true,
            boton1: "Nuevo",
            boton2: "Modificar",
            boton3: "Borrar",
            titulo: 'Recibos',
            textoVacio: 'No existen recibos',
            fields:
                [
                    'secuencia',
                    'detalle',
                    'saldo',
                    'monto',
                    {
                        'key': 'created_at',
                        'label': 'Fecha'
                    },
                ],
            itemRow: {},
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
        borrar(id) {
            this.$inertia.delete(`recibo/${id}`, {
                onBefore: () => confirm('Esta seguro?'),
            })
        },
        getSucursal($id) {
            return this.sucursales[$id];
        },
    }
}
</script>

