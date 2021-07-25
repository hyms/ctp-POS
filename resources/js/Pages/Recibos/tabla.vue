<template>
    <div class="content-w">
        <div class="content-box">
            <Menu :active="active"></Menu>
            <div class="tab-content">
                <div class="row m-b-20">
                    <b-button v-b-modal="'reciboModal'" @click="loadModal()">
                        {{ boton1 }}
                    </b-button>
                    <Form
                        id="reciboModal"
                        :tipo="tipo"
                        :item-row="itemRow"
                    ></Form>
                    <b-card class="table-responsive m-t-20">
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
                                    <a class="btn btn-secondary btn-sm" :href="'/reciboPdf/'+row.item.id" target="_blank">Imprimir</a>
                                </div>
                            </template>
                        </b-table>
                    </b-card>
                </div>
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
            </div>
        </div>
    </div>
</template>

<script>
import Layout from '@/Shared/Layout'
import Form from './form'
import Menu from './menuRegistro';
import moment from 'moment';

export default {
    layout: Layout,
    props: {
        recibos: Array,
        errors: Object,
        active: Number,
        tipo: Number,
    },
    components: {
        Form,
        Menu
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

