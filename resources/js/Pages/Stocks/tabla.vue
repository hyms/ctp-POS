<template>
    <div class="row">
        <div class="col-12">
            <Form :isNew="isNew" id="stockModal" :itemRow="itemRow"></Form>
            <Form2 :isNew="isNew" id="stockModal2" :itemRow="itemRow"></Form2>
            <b-tabs pills vertical>
                <template v-for="(sucursal,key) in sucursales">
                    <b-tab :title="sucursal.nombre" :active="(key===0)">
                        <b-card>
                            <b-card-text>
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover text-center">
                                        <thead>
                                        <tr>
                                            <th scope="col">Producto</th>
                                            <th scope="col">Cantidad</th>
                                            <th scope="col">Precio</th>
                                            <th scope="col">Estado</th>
                                            <th scope="col">Acciones</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <template v-for="(producto,key) in productos">
                                            <tr>
                                                <td>{{ producto.formato }}({{ producto.dimension }})</td>
                                                <td>{{ getCantidad(sucursal.id, producto.id) }}</td>
                                                <td>
                                                    <b-button variant="link" v-b-modal="'stockModal2'"
                                                              @click="loadModal(sucursal.id, producto.id,true)">
                                                        {{ getPrecio(sucursal.id, producto.id) }}
                                                    </b-button>
                                                </td>
                                                <td>
                                                    <loading-button :loading="sending" variant="link"
                                                                    @click.native="saveEnable(getId(sucursal.id, producto.id))"
                                                                    :text="getEnable(sucursal.id, producto.id)"
                                                                    :textLoad="''">
                                                        {{ getEnable(sucursal.id, producto.id) }}
                                                    </loading-button>
                                                </td>
                                                <td>

                                                    <b-button v-b-modal="'stockModal'" variant="primary"
                                                              @click="loadModal(sucursal.id, producto.id,true)">
                                                        Añadir
                                                    </b-button>
                                                    <b-button v-b-modal="'stockModal'" variant="danger"
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
                        </b-card>
                    </b-tab>
                </template>
            </b-tabs>
        </div>
    </div>
</template>

<script>
import Layout from '@/Shared/Layout'
import Form from './form'
import Form2 from './formPrecio'
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
        Form2,
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
                id: this.getId(sucursal,producto),
                sucursal: sucursal,
                producto: producto,
                cantidad: ((stock === null) ? 0 : stock['cantidad']),
                precioUnidad: ((stock === null) ? 0 : stock['precioUnidad'])
            };
            console.log(this.itemRow);
        },
        saveEnable(id) {
            this.sending = true;
            let stock = new FormData();
            stock.append('id', id)
            axios.post('/admin/stockEnable', stock, {headers: {'Content-Type': 'multipart/form-data'}})
                .then(({data}) => {
                    if (data["status"] === 0) {
                        this.$inertia.reload();
                    }
                    for (let key in this.form) {
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
            let texto = 'Deshabilitado';
            if (this.stocks[sucursal][producto] != null) {
                const enable = this.stocks[sucursal][producto]['enable'];
                if (enable != null && enable === 1) {
                    texto = 'Habilitado';
                }
            }
            return texto;
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

