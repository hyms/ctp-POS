<template>
    <div class="row">
        <div class="col-12">
            <div class="row mb-2">
                <div class="col">
                    <b-button v-b-modal="'reciboModal'" @click="loadModal()" variant="primary">
                        {{ boton1 }}
                    </b-button>
                    <Form
                        id="reciboModal"
                        :tipo="tipo"
                        :item-row="itemRow"
                    ></Form>
                </div>
            </div>
            <FormSearch :report="report"></FormSearch>
            <b-card no-body>
                <b-card-header>
                    <strong>{{ titulo + ((tipo) ? ' de Egreso' : ' de Ingreso') }}</strong>
                </b-card-header>
                <b-card-body>
                    <b-table
                        striped
                        hover
                        :items="recibos"
                        :fields="fields"
                        show-empty
                        small
                        :current-page="currentPage"
                        :per-page="perPage"
                    >
                        <template #empty="scope">
                            <p>{{ textoVacio }}</p>
                        </template>
                        <template v-slot:cell(created_at)="data">
                            {{ data.value | moment("DD/MM/YYYY HH:mm") }}
                        </template>
                        <template v-slot:cell(Acciones)="row">
                            <div class="row-actions">
                                <a class="btn btn-primary btn-sm" :href="'/reciboPdf/'+row.item.id" target="_blank">Imprimir</a>
                            </div>
                        </template>
                    </b-table>
                    <b-col>
                        <b-pagination
                            v-model="currentPage"
                            :total-rows="totalRows"
                            :per-page="perPage"
                            align="center"
                            class="my-0"
                            v-if="totalRows>perPage"
                        ></b-pagination>
                    </b-col>
                </b-card-body>
            </b-card>
        </div>
    </div>
</template>

<script>
import Authenticated from '@/Layouts/Authenticated'
import Form from './form'
import FormSearch from './formSearch'
import moment from 'moment';

export default {
    layout: Authenticated,
    props: {
        recibos: Array,
        errors: Object,
        tipo: Number,
        report: Object,
    },
    components: {
        Form,
        FormSearch
    },
    data() {
        return {
            isNew: true,
            boton1: "Nuevo",
            boton3: "imprimir",
            titulo: 'Recibos',
            textoVacio: 'No existen recibos',
            fields:
                [
                    'secuencia',
                    'detalle',
                    'saldo',
                    'monto',
                    {
                        'key': 'created_at',
                        'label': 'Fecha'
                    },
                    'Acciones'
                ],
            itemRow: {},
            totalRows: 1,
            currentPage: 1,
            perPage: 20,
        }
    },
    methods: {
        loadModal(isNew = true, item = null) {
            this.isNew = isNew;
            this.itemRow = {};
            if (!isNew) {
                this.itemRow = item.item;
            }
        },
        borrar(id) {
            this.$inertia.delete(`recibo/${id}`, {
                onBefore: () => confirm('Esta seguro?'),
            })
        },
        getSucursal($id) {
            return this.sucursales[$id];
        },
        viewModify(date) {
            const today = moment();
            date = moment(date);
            return moment(today).isSame(date, 'day');
        },
    },
    mounted() {
        // Set the initial number of items
        this.totalRows = this.recibos.length;
    },
}
</script>

