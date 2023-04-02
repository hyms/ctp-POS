<template>
    <v-row>
        <v-col v-for="(sucursal,key) in stocks" :key="key">
            <v-card>
                <v-card-title>
                    {{ sucursal.sucursalView }}
                </v-card-title>
                <v-data-table
                    :items="sucursal.productos"
                    :headers="fields"
                    :no-data-text="emptyText"
                    mobile-breakpoint="540"
                    item-class="text-center"
                    hide-default-footer
                    :items-per-page="-1"
                >
                    <template v-slot:item.cantidad="{item}">
                        <v-edit-dialog
                            :return-value.sync="item.cantidad"
                            @save="saveAmount(item.stockId,item.cantidad)"
                            save-text="Guardar"
                            cancel-text="Cancelar"
                            large
                        >
                            <v-btn text color="primary">
                                {{ (item.cantidad ?? '-') }}
                            </v-btn>
                            <template v-slot:input>
                                <v-text-field
                                    v-model="item.cantidad"
                                    label="Cantidad"
                                    single-line
                                    outlined
                                    dense
                                ></v-text-field>
                            </template>
                        </v-edit-dialog>
                    </template>
                    <template v-slot:item.precio="{item}">
                        <v-edit-dialog
                            :return-value.sync="item.precio"
                            @save="savePrice(item.stockId,item.precio)"
                            save-text="Guardar"
                            cancel-text="Cancelar"
                            large
                        >
                            <v-btn text color="primary">
                                {{ (item.precio ?? '-') }}
                            </v-btn>
                            <template v-slot:input>
                                <v-text-field
                                    v-model="item.precio"
                                    label="Precio"
                                    single-line
                                    outlined
                                    dense
                                ></v-text-field>
                            </template>
                        </v-edit-dialog>
                    </template>
                    <template v-slot:item.estado="{item}">
                        <v-switch
                            :input-value="item.estado"
                            @click="saveEnable(item.stockId)"
                        ></v-switch>
                    </template>
                </v-data-table>
            </v-card>
        </v-col>
    </v-row>
</template>

<script>
import Authenticated from '@/Layouts/Authenticated.vue'
import axios from "axios";

export default {
    layout: Authenticated,
    props: {
        productos: Array,
        sucursales: Array,
        stocks: Array,
        errors: Object,
    },
    data() {
        return {
            titulo: 'Productos',
            emptyText: 'No existen Productos',
            itemRow: {},
            fields: [
                {
                    text: 'Producto',
                    value: 'productoView'
                },
                {
                    text: 'Cantidad',
                    value: 'cantidad'
                },
                {
                    text: 'Precio',
                    value: 'precio'
                },
                {
                    text: 'Estado',
                    value: 'estado'
                },
            ]
        }
    },
    methods: {
        saveEnable(id) {
            let stock = new FormData();
            stock.append('id', id)
            this.saved( '/admin/stockEnable',stock)
        },
        savePrice(id, newPrice) {
            let stock = new FormData();
            stock.append('id', id);
            stock.append('precioUnidad', newPrice);
            this.saved( '/admin/stockPrice',stock)
        },
        saveAmount(id, newAmount) {
            let stock = new FormData();
            stock.append('id', id);
            stock.append('cantidad', newAmount);
            this.saved( '/admin/stockAmount',stock)
        },
        saved(url,formdata){
            axios.post(url, formdata, {headers: {'Content-Type': 'multipart/form-data'}})
                .then(({data}) => {
                })
                .catch(error => {
                    // handle error
                    this.errors = error
                    console.log(error);
                }).finally(() => {
                this.$inertia.reload();
            })
        }
    }
}
</script>

