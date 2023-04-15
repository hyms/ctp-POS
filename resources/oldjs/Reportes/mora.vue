<template>
    <v-row>
        <v-col>
            <v-card>
                <v-card-title>
                    <v-row>
                        <template v-for="(item, key) in form">
                            <v-col cols="4">
                                <v-text-field
                                    v-if="
                                        [
                                            'text',
                                            'password',
                                            'date',
                                            'email',
                                        ].includes(item.type)
                                    "
                                    :id="key"
                                    v-model="item.value"
                                    outlined
                                    dense
                                    clearable
                                    hide-details="auto"
                                    :type="item.type"
                                    :label="item.label"
                                ></v-text-field>
                                <v-select
                                    v-if="item.type === 'select'"
                                    :id="key"
                                    v-model="item.value"
                                    item-text="text"
                                    item-value="value"
                                    outlined
                                    dense
                                    clearable
                                    hide-details="auto"
                                    :items="item.options"
                                    :label="item.label"
                                ></v-select>
                            </v-col>
                        </template>
                        <v-col>
                            <v-row>
                                <v-col>
                                    <v-btn
                                        left
                                        color="primary"
                                        @click="sended"
                                        :loading="sending"
                                        :disabled="sending"
                                    >
                                        Consultar
                                        <v-icon right> mdi-poll </v-icon>
                                    </v-btn>
                                </v-col>
                                <v-spacer></v-spacer>
                                <v-col align="right">
                                    <v-btn right elevation="1" color="secondary"
                                        ><h3>Total: {{ total }}</h3></v-btn
                                    >
                                </v-col>
                            </v-row>
                        </v-col>
                    </v-row>
                </v-card-title>
                <v-divider></v-divider>
                <v-card-text>
                    <v-dialog
                        id="cliente"
                        v-model="dialogOrdenes"
                        max-width="600"
                    >
                        <v-card>
                            <v-card-title class="text-h5">
                                {{ "Cliente: " + item.nombre }}
                            </v-card-title>
                            <v-card-text>
                                <div class="table-responsive">
                                    <v-data-table
                                        :headers="item.fields"
                                        :items="item.ordenes"
                                        :single-expand="true"
                                        :expanded.sync="expanded"
                                        item-key="codigoServicio"
                                        show-expand
                                        class="elevation-1"
                                    >
                                        <template
                                            v-slot:expanded-item="{
                                                headers,
                                                item,
                                            }"
                                        >
                                            <td
                                                :colspan="headers.length"
                                                class="m-0 p-0"
                                            >
                                                <v-card class="mt-3 mb-3">
                                                    <div
                                                        class="table-responsive"
                                                    >
                                                        <v-simple-table
                                                            class="table"
                                                        >
                                                            <thead>
                                                                <tr>
                                                                    <th
                                                                        scope="col"
                                                                    >
                                                                        #
                                                                    </th>
                                                                    <th
                                                                        scope="col"
                                                                    >
                                                                        Productos
                                                                    </th>
                                                                    <th
                                                                        scope="col"
                                                                    >
                                                                        Cant.
                                                                    </th>
                                                                    <th
                                                                        scope="col"
                                                                    >
                                                                        Precio
                                                                    </th>
                                                                    <th
                                                                        scope="col"
                                                                    >
                                                                        Total
                                                                    </th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <tr
                                                                    v-for="(
                                                                        itemOrden,
                                                                        key
                                                                    ) in item.detallesOrden"
                                                                >
                                                                    <td>
                                                                        {{
                                                                            key +
                                                                            1
                                                                        }}
                                                                    </td>
                                                                    <td>
                                                                        {{
                                                                            getProduct(
                                                                                itemOrden.stock
                                                                            )
                                                                        }}
                                                                    </td>
                                                                    <td>
                                                                        {{
                                                                            itemOrden.cantidad
                                                                        }}
                                                                    </td>
                                                                    <td>
                                                                        {{
                                                                            itemOrden.costo
                                                                        }}
                                                                    </td>
                                                                    <td>
                                                                        {{
                                                                            itemOrden.costo *
                                                                            itemOrden.cantidad
                                                                        }}
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                            <tfoot>
                                                                <tr>
                                                                    <td
                                                                        colspan="4"
                                                                        class="text-right"
                                                                    >
                                                                        <strong
                                                                            >Total</strong
                                                                        >
                                                                    </td>
                                                                    <td>
                                                                        {{
                                                                            getTotal(
                                                                                item.detallesOrden
                                                                            )
                                                                        }}
                                                                    </td>
                                                                </tr>
                                                            </tfoot>
                                                        </v-simple-table>
                                                    </div>
                                                </v-card>
                                            </td>
                                        </template>
                                    </v-data-table>
                                    <!--                            <b-table
                                striped
                                hover
                                :items="item.ordenes"
                                :fields="item.fields"
                                show-empty
                                small>
                                <template #empty="scope">
                                    <p>No existen deudas</p>
                                </template>
                                <template v-slot:cell(created_at)="data">
                                    {{ data.value | moment("DD/MM/YYYY HH:mm") }}
                                </template>

                                <template #cell(detalle)="row">
                                    <b-button size="sm" @click="row.toggleDetails" class="mr-2">
                                        {{ row.detailsShowing ? 'Ocultar' : 'Mostrar' }} Orden
                                    </b-button>
                                </template>

                                <template #row-details="row">
                                    <b-card>
                                        <div class="table-responsive">
                                            <table class="table">
                                                <thead>
                                                <tr>
                                                    <th scope="col">#</th>
                                                    <th scope="col">Productos</th>
                                                    <th scope="col">Cant.</th>
                                                    <th scope="col">Precio</th>
                                                    <th scope="col">Total</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr v-for="(itemOrden,key) in row.item.detallesOrden">
                                                    <td>{{ key + 1 }}</td>
                                                    <td>{{ getProduct(itemOrden.stock) }}</td>
                                                    <td>{{ itemOrden.cantidad }}</td>
                                                    <td>{{ itemOrden.costo }}</td>
                                                    <td>{{ itemOrden.costo * itemOrden.cantidad }}</td>
                                                </tr>
                                                </tbody>
                                                <tfoot>
                                                <tr>
                                                    <td colspan="4" class="text-right"><strong>Total</strong></td>
                                                    <td>{{ getTotal(row.item.detallesOrden) }}</td>
                                                </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </b-card>
                                </template>
                            </b-table>-->
                                </div>
                            </v-card-text>
                        </v-card>
                    </v-dialog>

                    <template v-if="data['table'].length > 0">
                        <v-simple-table fixed-header height="60vh">
                            <template v-slot:default>
                                <thead>
                                    <tr>
                                        <template
                                            v-for="field in data['fields']"
                                        >
                                            <th>
                                                <span class="text-uppercase">{{
                                                    field
                                                }}</span>
                                            </th>
                                        </template>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template
                                        v-for="(item, key) in data['table']"
                                    >
                                        <tr>
                                            <template
                                                v-for="field in data['fields']"
                                            >
                                                <td>
                                                    <template
                                                        v-if="field === 'desde'"
                                                        >{{
                                                            getDesde(item)
                                                        }}</template
                                                    >
                                                    <template
                                                        v-else-if="
                                                            field === 'hasta'
                                                        "
                                                        >{{
                                                            getHasta(item)
                                                        }}</template
                                                    >
                                                    <template
                                                        v-else-if="
                                                            field === 'cantidad'
                                                        "
                                                        >{{ getCantidad(item) }}
                                                    </template>
                                                    <template
                                                        v-else-if="
                                                            field === 'nombre'
                                                        "
                                                        ><v-btn
                                                            @click="
                                                                loadModal(item)
                                                            "
                                                            text
                                                            color="primary"
                                                            >{{
                                                                item[field]
                                                            }}</v-btn
                                                        ></template
                                                    >
                                                    <template v-else>{{
                                                        item[field]
                                                    }}</template>
                                                </td>
                                            </template>
                                        </tr>
                                    </template>
                                </tbody>
                            </template>
                        </v-simple-table>
                    </template>
                </v-card-text>
            </v-card>
        </v-col>
    </v-row>
    <!--    <div class="content-w">
                <div class="tab-content">

                    <b-card v-if="data['table'].length>0">
                        <template #header>
                            <h5 class="mb-0">Resultados</h5>
                        </template>
                        <b-modal id="cliente"
                                 :title="'Cliente: '+ item.nombre" size="lg">
                            <div class="table-responsive">
                                <b-table
                                    striped
                                    hover
                                    :items="item.ordenes"
                                    :fields="item.fields"
                                    show-empty
                                    small>
                                    <template #empty="scope">
                                        <p>No existen deudas</p>
                                    </template>
                                    <template v-slot:cell(created_at)="data">
                                        {{ data.value | moment("DD/MM/YYYY HH:mm") }}
                                    </template>

                                    <template #cell(detalle)="row">
                                        <b-button size="sm" @click="row.toggleDetails" class="mr-2">
                                            {{ row.detailsShowing ? 'Ocultar' : 'Mostrar' }} Orden
                                        </b-button>
                                    </template>

                                    <template #row-details="row">
                                        <b-card>
                                            <div class="table-responsive">
                                                <table class="table">
                                                    <thead>
                                                    <tr>
                                                        <th scope="col">#</th>
                                                        <th scope="col">Productos</th>
                                                        <th scope="col">Cant.</th>
                                                        <th scope="col">Precio</th>
                                                        <th scope="col">Total</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <tr v-for="(itemOrden,key) in row.item.detallesOrden">
                                                        <td>{{ key + 1 }}</td>
                                                        <td>{{ getProduct(itemOrden.stock) }}</td>
                                                        <td>{{ itemOrden.cantidad }}</td>
                                                        <td>{{ itemOrden.costo }}</td>
                                                        <td>{{ itemOrden.costo * itemOrden.cantidad }}</td>
                                                    </tr>
                                                    </tbody>
                                                    <tfoot>
                                                    <tr>
                                                        <td colspan="4" class="text-right"><strong>Total</strong></td>
                                                        <td>{{ getTotal(row.item.detallesOrden) }}</td>
                                                    </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        </b-card>
                                    </template>
                                </b-table>
                            </div>
                        </b-modal>
                        <div class="table-responsive">
                            <b-table
                                striped
                                hover
                                :items="data['table']"
                                :fields="data['fields']"
                                show-empty
                                small
                            >
                                <template #cell(#)="data">
                                    {{ data.index + 1 }}
                                </template>
                                <template #empty="scope">
                                    <p>No existen clientes</p>
                                </template>
                                <template v-slot:cell(nombre)="row">
                                    <b-button variant="link" v-b-modal="'cliente'" @click="loadModal(row.item)">
                                        {{ row.item.nombre }}
                                    </b-button>
                                </template>
                                <template v-slot:cell(desde)="row">
                                    {{ getDesde(row.item) }}
                                </template>
                                <template v-slot:cell(hasta)="row">
                                    {{ getHasta(row.item) }}
                                </template>
                                <template v-slot:cell(cantidad)="row">
                                    {{ getCantidad(row.item) }}
                                </template>
                            </b-table>
                        </div>
                    </b-card>
                </div>
            </div>
        </div>-->
</template>

<script>
import Authenticated from "@/Layouts/Authenticated.vue";
import moment from "moment";

export default {
    layout: Authenticated,
    props: {
        sucursales: Array,
        productos: Array,
        request: Object | Array,
        clientes: Array,
        total: Number,
        errors: Object,
        data: Object,
    },
    components: {},
    data() {
        return {
            form: {
                sucursal: {
                    label: "Sucursal",
                    value: "",
                    type: "select",
                    state: null,
                    stateText: null,
                    options: this.sucursales,
                },
                fechaI: {
                    label: "Fecha Inicio",
                    value: "",
                    type: "date",
                    state: null,
                    stateText: null,
                },
                fechaF: {
                    label: "Fecha Fin",
                    value: "",
                    type: "date",
                    state: null,
                    stateText: null,
                },
            },
            item: {},
            sending: false,
            dialogOrdenes: false,
            expanded: [],
        };
    },
    methods: {
        sended() {
            let form = {};
            for (let key in this.form) {
                form[key] = this.form[key].value;
            }
            this.$inertia.get("/admin/reportes/mora", form);
        },
        loadModal(item) {
            this.item = item;
            this.dialogOrdenes = true;
        },
        getTotal(detalle) {
            let total = 0;
            if (detalle) {
                for (let value of detalle) {
                    if (value) {
                        total += value.costo * value.cantidad;
                    }
                }
            }
            return total;
        },
        getProduct(id) {
            let item = {};
            for (let value of this.productos) {
                if (value.id == id) {
                    item = value;
                    break;
                }
            }
            if (item) {
                return item.formato + " (" + item.dimension + ")";
            }
            return "";
        },
        getDesde(item) {
            let fecha = moment();
            for (let data of item.ordenes) {
                if (moment(data.created_at).isBefore(fecha)) {
                    fecha = moment(data.created_at);
                }
            }
            return fecha.format("DD/MM/YYYY HH:mm");
        },
        getHasta(item) {
            let fecha = moment(0);
            for (let data of item.ordenes) {
                if (moment(fecha).isBefore(data.created_at)) {
                    fecha = moment(data.created_at);
                }
            }
            return fecha.format("DD/MM/YYYY HH:mm");
        },
        getCantidad(item) {
            return item.ordenes.length;
        },
    },
    created() {
        for (let key in this.request) {
            this.form[key].value = this.request[key];
        }
        for (let key in this.errors) {
            this.form[key].state = false;
        }
    },
};
</script>
