<template>
    <v-row>
        <v-col>
            <formStock
                :dialog="dialog"
                :productos="productos"
                :ingreso="typeInventario === 2"
                :productos-sell="productosSell()"
                @close="close"
            ></formStock>
            <v-row>
                <v-col>
                    <v-dialog max-width="400px" scrollable>
                        <template v-slot:activator="{ on, attrs }">
                            <v-btn
                                color="secondary"
                                elevation="1"
                                class="mr-2 my-1 d-inline"
                                v-bind="attrs"
                                v-on="on"
                                left
                            >
                                Saldos
                                <v-icon right
                                    >mdi-clipboard-list-outline</v-icon
                                >
                            </v-btn>
                        </template>
                        <v-card>
                            <v-card-text>
                                <v-simple-table>
                                    <template v-slot:default>
                                        <thead>
                                            <tr>
                                                <th class="text-left">
                                                    Producto
                                                </th>
                                                <th class="text-left">
                                                    Cantidad
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr
                                                v-for="item in productos"
                                                :key="item.name"
                                            >
                                                <td>
                                                    {{ item.formato }} ({{
                                                        item.dimension
                                                    }})
                                                </td>
                                                <td>{{ item.cantidad }}</td>
                                            </tr>
                                        </tbody>
                                    </template>
                                </v-simple-table>
                            </v-card-text>
                        </v-card>
                    </v-dialog>
                    <v-spacer class="d-inline"></v-spacer>
                    <formSearch
                        :report="report"
                        :productosSelect="productosSelect"
                    ></formSearch>
                </v-col>
            </v-row>
            <v-row>
                <template v-for="item in inventario">
                    <v-col>
                        <v-card>
                            <v-card-title>
                                {{ item.title }}
                                <v-spacer></v-spacer>
                                <v-btn
                                    outlined
                                    color="primary"
                                    small
                                    @click="loadForm(item.typeInventario)"
                                >
                                    Nuevo
                                    <v-icon right> mdi-plus-thick</v-icon>
                                </v-btn>
                            </v-card-title>
                            <v-divider></v-divider>
                            <v-data-table
                                :items="item.data"
                                :headers="fields"
                                :no-data-text="emptyText"
                                mobile-breakpoint="540"
                            >
                            </v-data-table>
                        </v-card>
                    </v-col>
                </template>
            </v-row>
        </v-col>
    </v-row>
</template>

<script>
import Authenticated from "@/Layouts/Authenticated.vue";
import genericTable from "@/../components/genericTable.vue";
import formSearch from "./formSearch.vue";
import formStock from "./form.vue";

export default {
    layout: Authenticated,
    props: {
        productos: Array,
        productosSelect: Array,
        inventario: Array,
        fields: Array,
        stocks: Array,
        report: Object,
        errors: Object,
    },
    data() {
        return {
            dialog: false,
            dialogFilter: false,
            alert: false,
            sending: false,
            sendingF: false,
            emptyText: "No existen datos a mostrar",
            editedIndex: -1,
            editedItem: {},
            typeInventario: 0,
        };
    },
    components: {
        genericTable,
        formSearch,
        formStock,
    },
    methods: {
        openDialogFilter(typeInventario) {
            this.dialogFilter = true;
            this.typeInventario = typeInventario;
        },
        close() {
            this.dialog = false;
            this.dialogFilter = false;
        },
        searchItems() {
            let form = {};
            form["typeInventario"] = this.typeInventario;
            for (let key in this.formFiltro) {
                form[key] = this.formFiltro[key].value;
            }
            this.sendingF = true;
            this.$inertia.get(this.$page.url, form, {
                onFinish() {
                    this.sendingF = false;
                },
            });
        },
        loadForm(typeInventario) {
            this.typeInventario = typeInventario;
            this.dialog = true;
        },
        productosSell() {
            let sell = [];
            for (let key in this.productos) {
                sell[key] = {
                    id: this.productos[key].id,
                    cantidad: 0,
                    costo: this.productos[key].precioUnidad,
                    producto: this.productos[key].producto,
                };
            }
            return sell;
        },
    },
};
</script>
