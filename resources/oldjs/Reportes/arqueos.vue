<template>
<!--    <div class="content-w">
        <div class="content-box">
            <Menu :active="2"></Menu>
            <div class="tab-content">
                <b-card>
                    <b-form @submit.prevent="enviar">
                        <b-row>
                            <b-col md="5">
                                <b-form-group
                                    :label="form.fechaI.label"
                                    label-for="fechaI"
                                    :state="form.fechaI.state"
                                    label-cols-sm="3"
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
                            <b-col md="5">
                                <b-form-group
                                    :label="form.fechaF.label"
                                    label-for="fechaF"
                                    :state="form.fechaF.state"
                                    label-cols-sm="3"
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
                <b-card>
                    <b-card-header>
                        <b-card-title>
                            {{ fechaI|moment('DD/MM/YYYY') }}-{{ fechaF|moment('DD/MM/YYYY') }}
                        </b-card-title>
                    </b-card-header>
                    <div class="table-responsive">
                        <b-table-simple class="table table-hover text-small text-uppercase">
                            <b-thead>
                                <b-tr>
                                    <b-th>Comprobante</b-th>
                                    <b-th>Detalle</b-th>
                                    <b-th>Ingreso</b-th>
                                    <b-th>Egreso</b-th>
                                    <b-th>Saldo</b-th>
                                </b-tr>
                            </b-thead>
                            <b-tbody>
                                <b-tr>
                                    <td></td>
                                    <td>SALDO</td>
                                    <td>{{ saldo }}</td>
                                    <td></td>
                                    <td>{{ saldo }}</td>
                                </b-tr>
                                <b-tr>
                                    <td></td>
                                    <td>TOTAL DE INGRESOS</td>
                                    <td>{{ ventas + deudas }}</td>
                                    <td></td>
                                    <td>{{ saldo + ventas + deudas }}</td>
                                </b-tr>

                                <b-tr>
                                    <td></td>
                                    <td>Recibos de Ingreso</td>
                                    <td>{{ recibos[1] }}</td>
                                    <td></td>
                                    <td>{{ saldo + ventas + deudas + recibos[1] }}</td>
                                </b-tr>
                                <b-tr>
                                    <td></td>
                                    <td>Recibos de Engreso</td>
                                    <td></td>
                                    <td>{{ recibos[0] }}</td>
                                    <td>{{ saldo + ventas + deudas + recibos[1] - recibos[0] }}</td>
                                </b-tr>
                                <b-tr>
                                    <td></td>
                                    <td>Varios</td>
                                    <td>{{ cajas[0] }}</td>
                                    <td>{{ cajas[1] }}</td>
                                    <td>{{
                                            saldo + ventas + deudas + cajas[0] + recibos[1] - recibos[0] - cajas[1]
                                        }}
                                    </td>
                                </b-tr>
                                <b-tr v-if="arqueo.monto">
                                    <td>{{ arqueo.correlativo }}</td>
                                    <td>{{ arqueo.obseraciones }}</td>
                                    <td></td>
                                    <td>{{ arqueo.monto }}</td>
                                    <td>{{
                                            saldo + ventas + deudas + recibos[1] - recibos[0] - cajas - arqueo.monto
                                        }}
                                    </td>
                                </b-tr>
                                <b-tr>
                                    <td colspan="4" class="text-right"><strong>Total Saldo</strong></td>
                                    <td>{{ getTotal() }}</td>
                                </b-tr>
                            </b-tbody>
                        </b-table-simple>
                    </div>
                </b-card>
            </div>
        </div>
    </div>-->
</template>

<script>
import Authenticated from '@/Layouts/Authenticated.vue'
import Menu from "./menuReportes.vue";

export default {
    layout: Authenticated,
    props: {
        saldo: Number,
        arqueo: Array,
        caja: Object,
        fechaI: String,
        fechaF: String,
        ventas: Number,
        deudas: Number,
        recibos: Array,
        cajas: Array,
        dia: String,
    },
    components:{
        Menu
    },
    data() {
        return {
            form: {
                fechaI: {
                    label: 'Desde',
                    value: "",
                    type: "date",
                    state: null,
                    stateText: null
                },
                fechaF: {
                    label: 'Hasta',
                    value: "",
                    type: "date",
                    state: null,
                    stateText: null
                },
            },
            total: 0
        }
    },
    methods: {
        getTotal() {
            let monto = this.saldo + this.ventas + this.deudas + this.recibos[1] + this.cajas[0] - this.recibos[0] - this.cajas[1];
            if (this.arqueo.monto)
                return -this.arqueo.monto
            return monto;
        },
        enviar() {
            let form = {};
            for(const key in this.form){
                form[key] = this.form[key].value;
            }
            this.$inertia.get('/admin/reportes/arqueos', form)
        },
    }
}
</script>
<style>
.text-small {
    font-size: 0.9em;
}
</style>
