<template>
    <b-card title="REGISTRO DIARIO">
        <b-card-text class="text-right">{{ fecha }}
        </b-card-text>
        <b-table-simple  class="table table-hover table-small table-responsive text-small">
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
                    <td>CAJA CHICA GASTOS</td>
                    <td></td>
                    <td>{{ cajas }}</td>
                    <td>{{ saldo + ventas + deudas + recibos[1] - recibos[0] - cajas }}</td>
                </b-tr>
                <b-tr v-if="arqueo.monto">
                    <td>{{arqueo.correlativo}}</td>
                    <td>{{arqueo.obseraciones}}</td>
                    <td></td>
                    <td>{{arqueo.monto}}</td>
                    <td>{{saldo + ventas + deudas + recibos[1] - recibos[0] - cajas -arqueo.monto}}</td>
                </b-tr>
                <b-tr>
                    <td colspan="4" class="text-right"><strong>Total Saldo</strong></td>
                    <td>{{getTotal()}}</td>
                </b-tr>
            </b-tbody>
        </b-table-simple>
    </b-card>
</template>

<script>
import Layout from '@/Shared/Layout'

export default {
    layout: Layout,
    props: {
        saldo: Number,
        arqueo: Object,
        caja: Object,
        fecha: Date,
        ventas: Number,
        deudas: Number,
        recibos: Array,
        cajas: Object,
        dia: Number,
    },
    data() {
        return {
            total: 0
        }
    },
    methods: {
        getTotal() {
            if (this.arqueo.monto)
                return this.saldo + this.ventas + this.deudas + this.recibos[1] - this.recibos[0] - this.cajas - this.arqueo.monto
            return this.saldo + this.ventas + this.deudas + this.recibos[1] - this.recibos[0] - this.cajas;
        },
    }
}
</script>
<style>
.text-small {
    font-size: 0.8em;
}
</style>
