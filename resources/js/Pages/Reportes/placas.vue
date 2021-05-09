<template>
    <div class="content-w">
        <div class="content-box">
            <Menu :active="1"></Menu>
            <div class="tab-content">
                <b-card>
                    <template #header>
                        <h5 class="mb-0">Filtros</h5>
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
                                    :label="form.fecha.label"
                                    label-for="fecha"
                                    :state="form.fecha.state"
                                >
                                    <b-input
                                        :type="form.fecha.type"
                                        :placeholder="form.fecha.label"
                                        v-model="form.fecha.value"
                                        id="fecha"
                                        :state="form.fecha.state"
                                    ></b-input>
                                </b-form-group>
                            </b-col>
                            <b-col md="4" sm="6">
                                <b-form-group
                                    :label="form.tipoOrden.label"
                                    label-for="tipoOrden"
                                    :state="form.tipoOrden.state"
                                >
                                    <b-form-select
                                        :placeholder="form.tipoOrden.label"
                                        v-model="form.tipoOrden.value"
                                        :options="this.tipoOrden"
                                        id="tipoOrden"
                                        :state="form.tipoOrden.state"
                                    >
                                        <template #first>
                                            <b-form-select-option value="">-- Seleccione un tipo --
                                            </b-form-select-option>
                                        </template>
                                    </b-form-select>
                                </b-form-group>
                            </b-col>
                            <b-col>
                                <b-button type="submit">Buscar</b-button>
                            </b-col>
                        </b-row>
                    </form>
                </b-card>
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
                            <template #custom-foot="data">
                                <b-tr>
                                    <b-th colspan="4" class="text-right"><strong>Total</strong></b-th>
                                    <template v-for="(item,key) in data['fields']">
                                        <b-th v-if="(key>=4) && (key<=data['fields'].length-2)"> {{ getTotal(item) }}
                                        </b-th>
                                    </template>
                                    <b-th></b-th>
                                </b-tr>
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
        forms: Object,
        errors: Object,
        data: Object
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
                fecha: {
                    label: 'Fecha',
                    value: "",
                    type: "date",
                    state: null,
                    stateText: null
                },
                tipoOrden: {
                    label: 'TipoOrden',
                    value: "",
                    type: "select",
                    state: null,
                    stateText: null
                }
            },
            tipoOrden: {
                '0': "Orden de Trabajo",
                //'1': "Orden Interna",
                '2': "Reposicion",
            }

        }
    },
    methods: {
        enviar() {
            let form = {};
            Object.keys(this.form).forEach(key => {
                form[key] = this.form[key].value;
            })
            this.$inertia.get('/admin/reportes/placas', form)
        },
        getTotal(key) {
            let total = 0;
            Object.values(this.data['table']).forEach(value => {
                total += (value[key['key']]*1);
            })
            return total;
        }
    },
    created() {
        Object.keys(this.forms).forEach(key => {
            this.form[key].value = this.forms[key];
            // if (this.errors[key])) {
            //     this.form[key].state = false;
            // }

        });
        Object.keys(this.errors).forEach(key => {
            this.form[key].state = false;
        })
    }
}
</script>

<style scoped>

</style>
