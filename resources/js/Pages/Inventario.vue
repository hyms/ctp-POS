<template>
    <v-row>
        <v-col>
            <v-dialog max-width="400px" scrollable>
                <template v-slot:activator="{ on, attrs }">
                    <v-btn
                        color="secondary"
                        elevation="1"
                        class="mb-3"
                        v-bind="attrs"
                        v-on="on"
                    >
                        Saldos
                        <v-icon right>mdi-clipboard-list-outline</v-icon>
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
                                    <td>{{ item.formato }} ({{ item.dimension }})</td>
                                    <td>{{ item.cantidad }}</td>
                                </tr>
                                </tbody>
                            </template>
                        </v-simple-table>
                    </v-card-text>
                </v-card>
            </v-dialog>
            <v-dialog v-model="dialogFilter">
                <v-card>
                    <v-card-title>
                        <span class="text-h5">Filtros</span>
                    </v-card-title>

                    <v-card-text>
                        <template v-for="(item,key) in formFiltro">
                            <v-col cols="6">
                                <v-text-field
                                    v-if="['text','password','date','email'].includes(item.type)"
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
                                    v-if="item.type==='select'"
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
                    </v-card-text>

                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn small color="error" class="ma-1" @click="close">
                            Cancelar
                        </v-btn>
                        <v-btn small color="primary" class="ma-1" @click="searchItems"
                               :loading="sendingF" :disabled="sendingF">
                            Buscar
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>
            <v-row>
                <v-col v-for="item in intentario">
                    <v-card>
                        <v-card-title>
                            <div>
                                {{ item.title }}
                                <v-btn outlined color="primary">
                                    <v-icon right> mdi-plus-thick</v-icon>
                                </v-btn>
                            </div>
                            <v-spacer></v-spacer>
                            <v-btn
                                color="primary"
                                outlined
                                small
                                elevation="1"
                                class="mr-2 my-1"
                                @click="openDialogFilter(item.typeInventario)"
                            >
                                Filtros
                                <v-icon right>
                                    mdi-magnify
                                </v-icon>
                            </v-btn>
                        </v-card-title>
                        <v-data-table
                            :items="item.data"
                            :headers="fields"
                            :no-data-text="emptyText"
                            mobile-breakpoint="540"
                        >
                        </v-data-table>
                    </v-card>
                </v-col>
            </v-row>
        </v-col>
    </v-row>
</template>

<script>
import Authenticated from "@/Layouts/Authenticated.vue";
import genericTable from "@/Layouts/components/genericTable.vue";

export default {
    layout: Authenticated,
    props: {
        productos: Array,
        productosSelect: Object,
        intentario: Array,
        fields: Array,
        stocks: Array,
        report: Array,
        errors: Object,
    },
    data() {
        return {
            dialog: false,
            dialogFilter: false,
            alert: false,
            sending: false,
            sendingF: false,
            emptyText: 'No existen datos a mostrar',
            formFiltro: {
                fechaI: {
                    label: 'Fecha desde',
                    value: "",
                    type: "date",
                    state: null,
                    stateText: null
                },
                fechaF: {
                    label: 'Fecha hasta',
                    value: "",
                    type: "date",
                    state: null,
                    stateText: null
                },
                observaciones: {
                    label: 'Observaciones',
                    value: "",
                    type: "text",
                    state: null,
                    stateText: null
                },
                producto: {
                    label: 'Producto',
                    value: "",
                    type: "select",
                    state: null,
                    stateText: null,
                    options: this.productos,
                    isPadre: 1
                },
            },
            editedIndex: -1,
            editedItem: {},
            typeInventario: 0,
        }
    },
    components: {
        genericTable
    },
    methods: {
        openDialogFilter(typeInventario) {
            this.dialogFilter = true;
            this.typeInventario = typeInventario;
        },
        close() {
            this.dialog = false;
            this.dialogFilter = false;
            this.alert = false;
            this.removeState();
            this.removeValues();
            this.$nextTick(() => {
                this.editedIndex = -1
                this.editedItem = Object.assign({}, {})
            })
        },
        searchItems() {
            let form = {}
            form['typeInventario'] = this.typeInventario;
            for (let key in this.formFiltro) {
                form[key] = this.formFiltro[key].value;
            }
            this.sendingF = true;
            this.$inertia.get(this.$page.url, form, {
                onFinish() {
                    this.sendingF = false;
                }
            });
        }
    },
    mounted() {
        for (let key in this.formFiltro) {
            this.formFiltro[key].value = this.report[key] ?? "";
        }
    },
}
</script>
