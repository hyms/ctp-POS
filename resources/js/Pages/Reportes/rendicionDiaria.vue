<template>
    <div class="content-w">
        <div class="content-box">
            <Menu :active="6" v-if="admin"></Menu>
            <div class="row" v-else>
                <div class="col-sm-12">
                    <h4 class="header-title m-t-0 m-b-20">Rendición Diaria</h4>
                </div>
            </div>
            <b-card>
                <b-form @submit.prevent="enviar">
                    <b-row>
                        <b-col md="4" sm="6" v-if="admin && Object.keys(sucursales).length>0">
                            <b-form-group
                                :label="form.sucursal.label"
                                label-for="sucursal"
                                :state="form.sucursal.state"
                            >
                                <b-form-select
                                    :placeholder="form.sucursal.label"
                                    v-model="form.sucursal.value"
                                    :options="sucursales"
                                    id="sucursal"
                                    :state="form.sucursal.state"
                                >
                                    <template #first>
                                        <b-form-select-option value="">-- Seleccione una sucursal --
                                        </b-form-select-option>
                                    </template>
                                </b-form-select>
                            </b-form-group>
                        </b-col>
                        <b-col md="4" sm="6">
                            <b-form-group
                                :label="form.fechaI.label"
                                label-for="fechaI"
                                :state="form.fechaI.state"
                            >
                                <b-input
                                    :type="form.fechaI.type"
                                    :placeholder="form.fechaI.label"
                                    v-model="form.fechaI.value"
                                    id="fechaI"
                                    :state="form.fechaI.state"
                                ></b-input>
                            </b-form-group>
                        </b-col>
                        <b-col md="4" sm="6" v-if="admin">
                            <b-form-group
                                :label="form.fechaF.label"
                                label-for="fechaF"
                                :state="form.fechaF.state"
                            >
                                <b-input
                                    :type="form.fechaF.type"
                                    :placeholder="form.fechaF.label"
                                    v-model="form.fechaF.value"
                                    id="fechaF"
                                    :state="form.fechaF.state"
                                ></b-input>
                            </b-form-group>
                        </b-col>
                    </b-row>
                    <b-row>
                        <b-col>
                            <b-button type="submit" size="sm" variant="primary">Buscar</b-button>
                        </b-col>
                    </b-row>

                </b-form>
            </b-card>
            <b-row class="mt-2">
                <b-col>
                    <b-card>
                    <span class="h4">Rendicion Total <span class="badge badge-primary text-bold">{{ totalIngreso - totalEgreso }} Bs</span> </span>
                    </b-card>
                </b-col>
            </b-row>
            <b-row>
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
            </b-row>

        </div>
    </div>
</template>

<script>
import Layout from '@/Shared/Layout'
import Menu from "./menuReportes";

export default {
    layout: Layout,
    name: "rendicionDiaria",
    components: {
        Menu
    },
    props: {
        admin: Boolean,
        sucursales: Object,
        request: Object,
        data: Object
    },
    data() {
        return {
            form: {
                fechaI: {
                    label: 'Fecha Inicio',
                    value: "",
                    type: "date",
                    state: null,
                    stateText: null
                },
                fechaF: {
                    label: 'Fecha Final',
                    value: "",
                    type: "date",
                    state: null,
                    stateText: null
                },
                sucursal: {
                    label: 'Sucursal',
                    value: "",
                    type: "select",
                    state: null,
                    stateText: null
                },
            },
            totalIngreso: 0,
            totalEgreso: 0,
        }
    },
    methods: {
        enviar() {
            let form = {};
            form['fechaI'] = this.form.fechaI.value;
            if (this.admin) {
                form['fechaF'] = this.form.fechaF.value;
                form['sucursal'] = this.form.sucursal.value;
            }
            this.$inertia.get(window.location.pathname, form);
        },
        getTotal(table) {
            let total = 0.0;
            for (const value of table) {
                total += parseFloat(value.monto);
            }
            return total;
        }
    },
    mounted() {
        for (const key in this.form) {
            if (this.request[key] !== undefined && this.request[key] !== '') {
                this.form[key].value = this.request[key];
            }
        }
        this.totalEgreso = this.getTotal(this.data['table']['egreso']);
        this.totalIngreso = this.getTotal(this.data['table']['ingreso']);
    },

}
</script>
