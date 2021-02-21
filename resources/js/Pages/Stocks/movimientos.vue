<template>
    <div class="content-w">
        <div class="content-box">
            <Menu :active="2"></Menu>
            <div class="tab-content">
                    <div class="row m-b-20">
                        <div class="col">
                            <b-table
                                striped
                                hover
                                responsive
                                :items="movimientos"
                                :fields="fields"
                                show-empty
                                small
                            >
                                <template #empty="scope">
                                    <p>{{ textoVacio }}</p>
                                </template>
                                <template v-slot:cell(soSucursal)="data">
                                    {{ getSucursal(data.value) }}
                                </template>
                                <template v-slot:cell(sdSucursal)="data">
                                    {{ getSucursal(data.value) }}
                                </template>+
                                <template v-slot:cell(producto)="data">
                                    {{ getProducto(data.value) }}
                                </template>
                            </b-table>
                        </div>
                    </div>
            </div>
        </div>
    </div>
</template>

<script>
import Layout from '@/Shared/Layout'
import Menu from '@/Shared/menu/menuProductos';

export default {
    layout: Layout,
    props: {
        movimientos: Array,
        productos:Array,
        sucursales:Array,
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
                        label:'Origen',
                        key:'soSucursal'
                    },
                    {
                        label:'Destino',
                        key:'sdSucursal'
                    },
                    'cantidad',
                    'nombre',
                    'apellido',
                    'observaciones',
                    {
                        label:'Fecha',
                        key:'updated_at'
                    }
                ],
            itemRow: {}
        }
    },
    methods:{
        getSucursal(value)
        {
            let nombre = '';
            Object.keys(this.sucursales).forEach(
                key => {
                    if (value === this.sucursales[key].id) {
                        nombre = this.sucursales[key].nombre;
                    }
                }
            )
            return nombre;
        },
        getProducto(value)
        {
            let nombre = '';
            Object.keys(this.productos).forEach(
                key => {
                    if (value === this.productos[key].id) {
                        nombre = this.productos[key].formato + ' ('+this.productos[key].dimension+')';
                    }
                }
            )
            return nombre;
        }
    }
}
</script>

