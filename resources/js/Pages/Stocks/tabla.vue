<template>
    <div class="content-w">
        <div class="content-box">
            <Menu :active="0"></Menu>
            <div class="row">
                <div class="col">
                    <b-card no-body>
                        <b-tabs pills card vertical>
                            <template v-for="(value,key) in sucursales">
                            <b-tab :title="value.nombre" :active="(key===0)">
                                <b-card-text>
                                    <TablaStock :productos="productos" :sucursal="value" :sucursalPadre="sucursalPadre(value)"></TablaStock>
                                </b-card-text>
                            </b-tab>
                            </template>
                        </b-tabs>
                    </b-card>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Layout from '@/Shared/Layout'
import FormProducto from './form'
import Menu from '@/Shared/menu/menuProductos';
import TablaStock from "./tablaStock";

export default {
    layout: Layout,
    props: {
        productos: Array,
        sucursales: Array,
        errors: Object,
    },
    components: {
        FormProducto,
        Menu,
        TablaStock
    },
    data() {
        return {
            isNew: true,
            boton1: "Nuevo",
            boton2: "Modificar",
            boton3: "Borrar",
            titulo: 'Productos',
            textoVacio: 'No existen Productos',
            idModal: 'productoModal',
            itemRow: {}
        }
    },
    methods: {
       sucursalPadre(item) {
           if (item.dependeDe != null) {

           }
           return {};
       }
    }
}
</script>

