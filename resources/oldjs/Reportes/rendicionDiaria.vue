<template>
    <v-row>
        <v-col>
            <v-card>
                <v-card-title>
                    <v-row>
                        <template v-for="(item, key) in form">
                            <template v-if="item.active">
                                <v-col cols="6">
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
                        </template>
                        <v-col>
                            <v-row>
                                <v-col>
                                    <v-btn
                                        left
                                        small
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
                                <v-col>
                                    <v-btn elevation="1" color="secondary"
                                        ><h3>
                                            Total:
                                            {{ totalIngreso - totalEgreso }}
                                        </h3></v-btn
                                    >
                                </v-col>
                            </v-row>
                        </v-col>
                    </v-row>
                </v-card-title>
                <v-divider></v-divider>
                <v-card-text>
                    <v-row>
                        <template v-for="(dataTable, key) in data['table']">
                            <v-col cols="6">
                                <v-card outlined color="primary">
                                    <v-card-title>
                                        <h4 class="white--text text-uppercase">
                                            {{ key }}
                                        </h4>
                                        <v-spacer></v-spacer>
                                        <v-btn color="secondary"
                                            ><h3>
                                                Total:
                                                {{
                                                    key === "egreso"
                                                        ? totalEgreso
                                                        : totalIngreso
                                                }}
                                            </h3></v-btn
                                        >
                                    </v-card-title>
                                    <v-divider></v-divider>
                                    <v-simple-table
                                        dense
                                        fixed-header
                                        height="60vh"
                                    >
                                        <template v-slot:default>
                                            <thead>
                                                <tr>
                                                    <template
                                                        v-for="field in data[
                                                            'fields'
                                                        ]"
                                                    >
                                                        <th>
                                                            <span
                                                                class="text-uppercase"
                                                                >{{
                                                                    field
                                                                }}</span
                                                            >
                                                        </th>
                                                    </template>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <template
                                                    v-for="(
                                                        item, key
                                                    ) in dataTable"
                                                >
                                                    <tr>
                                                        <template
                                                            v-for="field in data[
                                                                'fields'
                                                            ]"
                                                        >
                                                            <td>
                                                                <span
                                                                    v-if="
                                                                        field !==
                                                                        'fecha'
                                                                    "
                                                                    >{{
                                                                        field ===
                                                                        "#"
                                                                            ? key +
                                                                              1
                                                                            : item[
                                                                                  field
                                                                              ]
                                                                    }}</span
                                                                >
                                                                <span v-else>
                                                                    {{
                                                                        item[
                                                                            field
                                                                        ]
                                                                            | moment(
                                                                                "DD/MM/YYYY HH:mm"
                                                                            )
                                                                    }}</span
                                                                >
                                                            </td>
                                                        </template>
                                                    </tr>
                                                </template>
                                            </tbody>
                                        </template>
                                    </v-simple-table>
                                </v-card>
                            </v-col>
                        </template>
                    </v-row>
                </v-card-text>
            </v-card>
            <!--            <b-row>
                            <b-col sm="6">
                                <b-card no-body>
                                    <b-card-header header-tag="div">
                                    <span class="h4">
                                   INGRESOS DIARIOS <span class="badge badge-primary text-bold">TOTAL {{ totalIngreso }} Bs</span>
                                    </span>
                                    </b-card-header>
                                    <b-card-body class="table-responsive">
                                        <b-table
                                            striped
                                            hover
                                            :items="data['table']['ingreso']"
                                            :fields="data['fields']"
                                            show-empty
                                            small
                                            sticky-header
                                        >
                                            <template v-slot:cell(created_at)="data">
                                                {{ data.value | moment("DD/MM/YYYY HH:mm") }}
                                            </template>
                                            <template #empty="scope">
                                                <p>No existen Datos</p>
                                            </template>
                                        </b-table>
                                    </b-card-body>
                                </b-card>
                            </b-col>
                            <b-col sm="6">
                                <b-card no-body>
                                    <b-card-header header-tag="header">
                                        <span class="h4">EGRESOS DIARIOS <span
                                            class="badge badge-primary text-bold">TOTAL {{ totalEgreso }} Bs</span></span>
                                    </b-card-header>
                                    <b-card-body class="table-responsive">
                                        <b-table
                                            striped
                                            hover
                                            :items="data['table']['egreso']"
                                            :fields="data['fields']"
                                            show-empty
                                            small
                                            sticky-header
                                        >
                                            <template v-slot:cell(created_at)="data">
                                                {{ data.value | moment("DD/MM/YYYY HH:mm") }}
                                            </template>
                                            <template #empty="scope">
                                                <p>No existen Datos</p>
                                            </template>
                                        </b-table>
                                    </b-card-body>
                                </b-card>
                            </b-col>
                        </b-row>-->
        </v-col>
    </v-row>
</template>

<script>
import Authenticated from "@/Layouts/Authenticated.vue";

export default {
    layout: Authenticated,
    props: {
        admin: {
            type: Boolean,
            default: false,
        },
        sucursales: Array,
        request: Object,
        data: Object,
    },
    data() {
        return {
            form: {
                fechaI: {
                    label: "Fecha Inicio",
                    value: "",
                    type: "date",
                    state: null,
                    stateText: null,
                    active: true,
                },
                fechaF: {
                    label: "Fecha Final",
                    value: "",
                    type: "date",
                    state: null,
                    stateText: null,
                    active: this.admin,
                },
                sucursal: {
                    label: "Sucursal",
                    value: "",
                    type: "select",
                    state: null,
                    stateText: null,
                    active: this.admin,
                },
            },
            totalIngreso: 0,
            totalEgreso: 0,
            sending: false,
        };
    },
    methods: {
        sended() {
            this.sending = true;
            let form = {};
            form["fechaI"] = this.form.fechaI.value;
            if (this.admin) {
                form["fechaF"] = this.form.fechaF.value;
                form["sucursal"] = this.form.sucursal.value;
            }
            this.$inertia.get(window.location.pathname, form);
            this.sending = false;
        },
        getTotal(table) {
            let total = 0.0;
            for (const value of table) {
                total += parseFloat(value.monto);
            }
            return total;
        },
    },
    mounted() {
        for (const key in this.form) {
            if (this.request[key] !== undefined && this.request[key] !== "") {
                this.form[key].value = this.request[key];
            }
        }
        this.totalEgreso = this.getTotal(this.data["table"]["egreso"]);
        this.totalIngreso = this.getTotal(this.data["table"]["ingreso"]);
    },
};
</script>
