<template>
    <div class="content-w">
        <div class="content-box">
            <div class="row">
                <div class="col-sm-12">
                    <h4 class="header-title m-t-0 m-b-20">Registro Diario</h4>
                </div>
            </div>
            <b-card :title="fecha|moment('DD/MM/YYYY')">

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
                                <td>{{ saldo + ventas + deudas + cajas[0] + recibos[1] - recibos[0] - cajas[1] }}</td>
                            </b-tr>
                            <b-tr v-if="arqueo.monto">
                                <td>{{ arqueo.correlativo }}</td>
                                <td>{{ arqueo.obseraciones }}</td>
                                <td></td>
                                <td>{{ arqueo.monto }}</td>
                                <td>{{ saldo + ventas + deudas + recibos[1] - recibos[0] - cajas - arqueo.monto }}</td>
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
</template>

<script>
import Layout from '@/Shared/Layout'

export default {
    layout: Layout,
    props: {
        saldo: Number,
        arqueo: Array,
        caja: Object,
        fecha: String,
        ventas: Number,
        deudas: Number,
        recibos: Array,
        cajas: Array,
        dia: String,
    },
    data() {
        return {
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
    }
}
</script>
<style>
.text-small {
    font-size: 0.9em;
}
</style>
