<template>
    <div class="row">
        <div class="col-12">
            <div class="row mb-2">
                <div class="col">
                    <b-button v-b-modal="'cajaModal'" variant="primary">{{ boton1 }}</b-button>
                    <Form :is-new="true" id="cajaModal" :credito="credito"></Form>
                </div>
            </div>
            <form-c-c :report="report"></form-c-c>
            <b-card no-body>
                <b-card-header>
                    <strong>Registro de Caja Chica</strong>
                </b-card-header>
                <b-card-body>
                    <div class="table-responsive">
                        <b-table
                            striped
                            hover
                            :items="registros"
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
                                    <b-button size="sm" class="btn-danger" @click="borrar(row.item.id)"
                                              v-if="viewModify(row.item.created_at)">
                                        {{ boton3 }}
                                    </b-button>
                                </div>
                            </template>
                        </b-table>
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
                </b-card-body>
            </b-card>
        </div>
    </div>
</template>

<script>
import Layout from '@/Shared/Layout'
import Form from './formMovimiento'
import FormCC from './formSearchCC'
import Menu from './menuRegistroCajas';
import moment from 'moment';

export default {
    layout: Layout,
    props: {
        registros: Array,
        credito: Boolean,
        report:Object
    },
    components: {
        Form,
        FormCC,
        Menu
    },
    data() {
        return {
            boton1: "Nuevo",
            boton3: "Borrar",
            titulo: 'Registros de Caja',
            textoVacio: 'No existen Registros',
            fields: [
                {
                    'key': 'observaciones',
                    'label': 'Detalle'
                },
                'monto',
                {
                    'key': 'created_at',
                    'label': 'Fecha'
                },
                'Acciones'
            ],
            totalRows: 1,
            currentPage: 1,
            perPage: 20,
        }
    },
    methods: {
        borrar(id) {
            this.$inertia.delete(`cajaMovimiento/${id}`, {
                onBefore: () => confirm('Esta seguro?'),
            })
        },
        viewModify(date) {
            const today = moment();
            date = moment(date);
            return moment(today).isSame(date, 'day');
        }
    },
    mounted() {
        // Set the initial number of items
        this.totalRows = this.registros.length
    },
}
</script>
