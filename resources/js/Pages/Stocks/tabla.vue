<template>
    <div class="content-w">
        <div class="content-box">
            <Menu :active="0"></Menu>
            <Form :isNew="isNew" id="stockModal" :itemRow="itemRow"></Form>
            <div class="row">
                <div class="col">
                    <b-card no-body>
                        <b-tabs pills card vertical>
                            <template v-for="(sucursal,key) in sucursales">
                            <b-tab :title="sucursal.nombre" :active="(key===0)">
                                <b-card-text>
                                    <table class="table table-striped table-hover text-center">
                                        <thead>
                                        <tr>
                                            <th scope="col">Productos</th>
                                            <th scope="col">Cantidad</th>
                                            <th scope="col">Acciones</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <template v-for="(producto,key) in productos">
                                            <tr>
                                                <td>{{ producto.formato }}({{ producto.dimension }})</td>
                                                <td>{{ getCantidad(sucursal.id, producto.id) }}</td>
                                                <td>
                                                    <b-button v-b-modal="'stockModal'" @click="loadModal(sucursal.id, producto.id,true)">
                                                        Añadir
                                                    </b-button>
                                                    <b-button v-b-modal="'stockModal'" class="btn-danger" @click="loadModal(sucursal.id, producto.id,false)">
                                                        Quitar
                                                    </b-button>
                                                </td>
                                            </tr>
                                        </template>
                                        </tbody>
                                    </table>
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
import Form from './form'
import Menu from '@/Shared/menu/menuProductos';

export default {
    layout: Layout,
    props: {
        productos: Array,
        sucursales: Array,
        stocks:Object,
        errors: Object,
    },
    components: {
        Form,
        Menu,
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
       },
        loadModal(sucursal, producto,isUp) {
            let cantidad = this.stocks[sucursal][producto];
            this.isNew = isUp;
            this.itemRow = {
                sucursal:sucursal,
                producto:producto,
                cantidad:((cantidad===null)?0:cantidad)
            };
        },
        getCantidad(sucursal, producto) {
            let cantidad = this.stocks[sucursal][producto];
            if (cantidad != null)
                return cantidad;
            return "-";
        }
    }
}
</script>

