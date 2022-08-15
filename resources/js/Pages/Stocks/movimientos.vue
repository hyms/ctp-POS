<template>
    <div class="content-w">
        <div class="content-box">
            <Menu :active="2"></Menu>
            <div class="tab-content">
                <div class="m-b-20">
                    <div class="table-responsive">
                        <b-table
                            striped
                            hover
                            :items="movimientos"
                            :fields="fields"
                            show-empty
                            small
                            :current-page="currentPage"
                            :per-page="perPage"
                        >
                            <template #empty="scope">
                                <p>{{ textoVacio }}</p>
                            </template>
                            <template v-slot:cell(soSucursal)="data">
                                {{ getSucursal(data.value) }}
                            </template>
                            <template v-slot:cell(sdSucursal)="data">
                                {{ getSucursal(data.value) }}
                            </template>
                            +
                            <template v-slot:cell(producto)="data">
                                {{ getProducto(data.value) }}
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
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Authenticated from '@/Layouts/Authenticated'
import Menu from '@/Shared/menu/menuProductos';

export default {
    layout: Authenticated,
    props: {
        movimientos: Array,
        productos: Array,
        sucursales: Array,
        errors: Object,
    },
    components: {
        Menu
    },
    data() {
        return {
            isNew: true,
            titulo: 'Movimientos',
            textoVacio: 'No existen Movimientos',
            fields:
                [
                    'producto',
                    {
                        label: 'Origen',
                        key: 'soSucursal'
                    },
                    {
                        label: 'Destino',
                        key: 'sdSucursal'
                    },
                    'cantidad',
                    'nombre',
                    'apellido',
                    'observaciones',
                    {
                        label: 'Fecha',
                        key: 'updated_at'
                    }
                ],
            itemRow: {},
            totalRows: 1,
            currentPage: 1,
            perPage: 20,
        }
    },
    methods: {
        getSucursal(value) {
            let nombre = '';
            for (let key in this.sucursales) {
                if (value === this.sucursales[key].id) {
                    nombre = this.sucursales[key].nombre;
                }
            }
            return nombre;
        },
        getProducto(value) {
            let nombre = '';
            for (let key in this.productos) {
                if (value === this.productos[key].id) {
                    nombre = this.productos[key].formato + ' (' + this.productos[key].dimension + ')';
                }
            }
            return nombre;
        }
    },
    mounted() {
        // Set the initial number of items
        this.totalRows = this.movimientos.length
    },
}
</script>

