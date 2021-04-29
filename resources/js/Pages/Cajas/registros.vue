<template>
    <div class="content-w">
        <div class="content-box">
            <Menu :active="credito?2:1"></Menu>
            <div class="tab-content">
                <div class="row m-b-20">
                    <div class="col">
                        <b-button v-b-modal="'cajaModal'" @click="loadModal()">{{ boton1 }}</b-button>
                        <Form :is-new="true" id="cajaModal" :credito="credito"></Form>
                    </div>
                </div>

                <div class="row m-b-20">
                    <b-card>
                        <div class="table-responsive">
                            <b-table
                                striped
                                hover
                                :items="registros"
                                :fields="fields"
                                show-empty
                                small
                            >
                                <template #empty="scope">
                                    <p>{{ textoVacio }}</p>
                                </template>
                                <template v-slot:cell(created_at)="data">
                                    {{ data.value | moment("DD/MM/YYYY HH:mm") }}
                                </template>
                                <template v-slot:cell(Acciones)="row">
                                    <div class="row-actions">
                                        <b-button size="sm" class="btn-danger" @click="borrar(row.item.id)">{{
                                                boton3
                                            }}
                                        </b-button>
                                    </div>
                                </template>
                            </b-table>
                        </div>
                    </b-card>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Layout from '@/Shared/Layout'
import Form from './formMovimiento'
import Menu from '@/Shared/menu/menuRegistroCajas';

export default {
    layout: Layout,
    props: {
        registros: Array,
        credito: Boolean,
    },
    components: {
        Form,
        Menu
    },
    data() {
        return {
            boton1: "Nuevo",
            boton3: "Borrar",
            titulo: 'Registros de Caja',
            textoVacio: 'No existen Registros',
            fields: [
                'observaciones',
                'monto',
                {
                    'key': 'created_at',
                    'label': 'Fecha'
                },
                'Acciones'
            ]
        }
    },
    methods: {
        loadModal() {

        },
        borrar(id) {
            this.$inertia.delete(`cajaMovimiento/${id}`, {
                onBefore: () => confirm('Esta seguro?'),
            })
        },
    },
    filters: {
        dtFormat(dt, format) {
            return moment.utc(dt).format(format)
        }
    }
}
</script>
