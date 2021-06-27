<template>
    <div class="content-w">
        <div class="content-box">
            <Menu :active="4" ></Menu>
            <div class="tab-content">
                <b-row>
                    <b-col md="8">
                        <b-card>
                            <template #header>
                                <h5 class="mb-0">Resumen Mora</h5>
                            </template>
                            <form @submit.prevent="enviar">
                                <b-row>
                                    <b-col md="4" sm="6">
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
                                    <b-col md="4" sm="6">
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
                            </form>
                        </b-card>
                    </b-col>
                    <b-col md="4">
                        <b-card>
                            <template #header>
                                <h5 class="mb-0">Total</h5>
                            </template>
                            <b-row>
                                <b-col>
                                    <h2>{{total}}</h2>
                                </b-col>
                            </b-row>
                        </b-card>
                    </b-col>
                </b-row>

                <b-card v-if="data['table'].length>0">
                    <template #header>
                        <h5 class="mb-0">Resultados</h5>
                    </template>
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
                            <template v-slot:cell(observaciones)="data">
                                <span v-html="data.value"></span>
                            </template>
                        </b-table>
                    </div>
                </b-card>
            </div>
        </div>
    </div>
</template>

<script>
import Layout from '@/Shared/Layout'
import Menu from "@/Shared/menu/menuReportes";

export default {
    layout: Layout,
    props: {
        sucursales: Object,
        request:Object,
        clientes:Array,
        total:Number,
        errors: Object,
        data:Object,
    },
    components: {
        Menu
    },
    data() {
        return {
            form: {
                sucursal: {
                    label: 'Sucursal',
                    value: "",
                    type: "select",
                    state: null,
                    stateText: null
                },
                fechaI: {
                    label: 'Fecha Inicio',
                    value: "",
                    type: "date",
                    state: null,
                    stateText: null
                },fechaF: {
                    label: 'Fecha Fin',
                    value: "",
                    type: "date",
                    state: null,
                    stateText: null
                },
            },
        }
    },
    methods: {
        enviar() {
            let form = {};
            Object.keys(this.form).forEach(key => {
                form[key] = this.form[key].value;
            })
            this.$inertia.get('/admin/reportes/mora', form)
        },
    },
    created() {
        Object.keys(this.request).forEach(key => {
            this.form[key].value = this.request[key];
        });
        Object.keys(this.errors).forEach(key => {
            this.form[key].state = false;
        })
    }
}
</script>

<style scoped>

</style>
