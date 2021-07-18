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
                        <b-col>
                            <b-button type="submit">Buscar</b-button>
                        </b-col>
                    </b-row>
                </b-form>
            </b-card>
            <b-card no-body class="mb-1">
                <b-card-header header-tag="header" class="p-1" role="tab">
                    <b-button block v-b-toggle.accordion-1 variant="secondary">INGRESOS DIARIOS</b-button>
                </b-card-header>
                <b-collapse id="accordion-1" visible accordion="my-accordion" role="tabpanel">
                    <b-card-body class="table-responsive">
                        <b-table
                            striped
                            hover
                            :items="data['table']['ingreso']"
                            :fields="data['fields']"
                            show-empty
                            small
                        >
                            <template v-slot:cell(created_at)="data">
                                {{ data.value | moment("DD/MM/YYYY HH:mm") }}
                            </template>
                            <template #empty="scope">
                                <p>No existen Datos</p>
                            </template>
                        </b-table>
                    </b-card-body>
                </b-collapse>
            </b-card>
            <b-card no-body class="mb-1">
                <b-card-header header-tag="header" class="p-1" role="tab">
                    <b-button block v-b-toggle.accordion-2 variant="secondary">EGRESOS DIARIOS</b-button>
                </b-card-header>
                <b-collapse id="accordion-2" visible accordion="my-accordion" role="tabpanel">
                    <b-card-body class="table-responsive">
                        <b-table
                            striped
                            hover
                            :items="data['table']['egreso']"
                            :fields="data['fields']"
                            show-empty
                            small
                        >
                            <template v-slot:cell(created_at)="data">
                                {{ data.value | moment("DD/MM/YYYY HH:mm") }}
                            </template>
                            <template #empty="scope">
                                <p>No existen Datos</p>
                            </template>
                        </b-table>
                    </b-card-body>
                </b-collapse>
            </b-card>
        </div>
    </div>
</template>

<script>
import Layout from '@/Shared/Layout'
import Menu from "@/Shared/menu/menuReportes";

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
        }
    },
    mounted() {
        for(const key in this.form) {
            if (this.request[key] !== undefined && this.request[key] !== '') {
                this.form[key].value = this.request[key];
            }
        }
    }
}
</script>
