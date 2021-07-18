<template>
    <div class="content-w">
        <div class="content-box">
            <Menu :active="0"></Menu>
            <Form :isNew="isNew" id="stockModal" :itemRow="itemRow"></Form>
            <div class="tab-content">
                <b-card no-body>
                    <b-tabs card pills>
                        <template v-for="(sucursal,key) in sucursales">
                            <b-tab :title="sucursal.nombre" :active="(key===0)">
                                <b-card-text>
                                    <div class="table-responsive">
                                        <table class="table table-striped table-hover text-center">
                                            <thead>
                                            <tr>
                                                <th scope="col">Productos</th>
                                                <th scope="col">Cantidad</th>
                                                <th scope="col">Precio</th>
                                                <th scope="col">Acciones</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <template v-for="(producto,key) in productos">
                                                <tr>
                                                    <td>{{ producto.formato }}({{ producto.dimension }})</td>
                                                    <td>{{ getCantidad(sucursal.id, producto.id) }}</td>
                                                    <td>{{ getPrecio(sucursal.id, producto.id) }}</td>
                                                    <td>
                                                        <loading-button :loading="sending" variant="dark"
                                                                        @click.native="saveEnable(getId(sucursal.id, producto.id))"
                                                                        :text="getEnable(sucursal.id, producto.id)"
                                                                        :textLoad="''">
                                                            {{ getEnable(sucursal.id, producto.id) }}
                                                        </loading-button>
                                                        <b-button v-b-modal="'stockModal'"
                                                                  @click="loadModal(sucursal.id, producto.id,true)">
                                                            Añadir
                                                        </b-button>
                                                        <b-button v-b-modal="'stockModal'" class="btn-danger"
                                                                  @click="loadModal(sucursal.id, producto.id,false)">
                                                            Quitar
                                                        </b-button>
                                                    </td>
                                                </tr>
                                            </template>
                                            </tbody>
                                        </table>
                                    </div>
                                </b-card-text>
                            </b-tab>
                        </template>
                    </b-tabs>
                </b-card>
            </div>
        </div>
    </div>
</template>

<script>
import Layout from '@/Shared/Layout'
import Form from './form'
import Menu from '@/Shared/menu/menuProductos';
import LoadingButton from "@/Shared/LoadingButton";
import axios from "axios";

export default {
    layout: Layout,
    props: {
        productos: Array,
        sucursales: Array,
        stocks: Object,
        errors: Object,
    },
    components: {
        Form,
        Menu,
        LoadingButton,
    },
    data() {
        return {
            sending: false,
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
        loadModal(sucursal, producto, isUp) {
            let stock = this.stocks[sucursal][producto];
            this.isNew = isUp;
            this.itemRow = {
                sucursal: sucursal,
                producto: producto,
                cantidad: ((stock === null) ? 0 : stock['cantidad']),
                precioUnidad: ((stock === null) ? 0 : stock['precioUnidad'])
            };
        },
        saveEnable(id) {
            this.sending = true;
            let stock = new FormData();
            stock.append('id',id)
            axios.post('/admin/stockEnable', stock, {headers: {'Content-Type': 'multipart/form-data'}})
                .then(({data}) => {
                    if (data["status"] == 0) {
                        this.$inertia.reload();
                    }
                    for(let key in this.form){
                        if (key in data.errors) {
                            this.form[key].state = false;
                            this.form[key].stateText = data.errors[key][0];
                        } else {
                            this.form[key].state = true;
                            this.form[key].stateText = "";
                        }
                    }
                })
                .catch(error => {
                    // handle error
                    this.errors = error
                    console.log(error);
                }).finally(() => {
                this.sending = false;
            })
        },
        getCantidad(sucursal, producto) {
            if (this.stocks[sucursal][producto] != null) {
                let cantidad = this.stocks[sucursal][producto]['cantidad'];
                if (cantidad != null)
                    return cantidad;
            }
            return "-";
        },
        getPrecio(sucursal, producto) {
            if (this.stocks[sucursal][producto] != null) {
                let precio = this.stocks[sucursal][producto]['precioUnidad'];
                if (precio != null)
                    return precio;
            }
            return "-";
        },
        getEnable(sucursal, producto) {
            if (this.stocks[sucursal][producto] != null) {
                let enable = this.stocks[sucursal][producto]['enable'];
                if (enable != null) {
                    return (enable === 1) ? 'Habilitado' : 'Deshabilitado';
                }
            }
            return "-";
        },
        getId(sucursal, producto) {
            if (this.stocks[sucursal][producto] != null) {
                let id = this.stocks[sucursal][producto]['id'];
                if (id != null) {
                    return id;
                }
            }
            return "";
        }
    }
}
</script>

