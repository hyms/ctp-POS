<template>
    <v-menu
        v-model="menu"
        :close-on-content-click="false"
        :nudge-width="500"
        offset-y>
        <template v-slot:activator="{ on, attrs }">
            <v-btn
                color="primary"
                outlined
                small
                elevation="1"
                class="mr-2 my-1"
                v-bind="attrs"
                v-on="on"
            >
                Filtros
                <v-icon right>
                    mdi-magnify
                </v-icon>
            </v-btn>
        </template>

        <v-card>
            <v-card-text>
                <form>
                    <v-row>
                        <template v-for="(item,key) in form">
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
                    </v-row>
                </form>
            </v-card-text>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn
                    outlined
                    small
                    color="error"
                    @click="menu = false"
                >
                    Cancelar
                </v-btn>
                <v-btn
                    outlined
                    small
                    color="primary"
                    @click="search"
                >
                    Buscar
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-menu>
</template>

<script>
export default {
    data() {
        return {
            form: {
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
                orden: {
                    label: 'Orden',
                    value: "",
                    type: "text",
                    state: null,
                    stateText: null
                },
                responsable: {
                    label: 'Cliente',
                    value: "",
                    type: "text",
                    state: null,
                    stateText: null
                },
                tipoOrden: {
                    label: 'Tipo de orden',
                    value: "",
                    type: "select",
                    state: null,
                    stateText: null,
                    options: this.tiposSelect,
                    isPadre: 1
                },
                estado: {
                    label: 'Estado',
                    value: "",
                    type: "select",
                    state: null,
                    stateText: null,
                    options: this.estados,
                    isPadre: 1
                },
            },
            menu: false
        }
    },
    props: {
        report: Object,
        estados: Array,
        tiposSelect: Array,
    },
    mounted() {
        for (let key in this.form) {
            this.form[key].value = this.report[key]??"";
        }
    },
    methods: {
        search() {
            let form = {}
            for (let key in this.form) {
                form[key] = this.form[key].value;
            }
            this.$inertia.get(this.$page.url, form);
        }
    }
}
</script>

