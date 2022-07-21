<template>
    <div class="row">
        <div class="col-12">
            <div class="row mb-2">
                <div class="col">
                    <b-button v-b-modal="'clienteModal'" variant="primary" @click="loadModal()">{{
                        boton1
                        }}
                    </b-button>
                    <FormProducto :isNew="isNew" id="clienteModal" :itemRow="itemRow"
                                  :sucursales="sucursales"></FormProducto>
                </div>
            </div>

            <b-card no-body>
                <b-card-header><strong>{{ titulo }}</strong></b-card-header>
                <b-card-body>
                    <b-col lg="6" class="my-1">
                        <b-form-group
                            label="Buscar"
                            label-for="filter-input"
                            label-cols-sm="3"
                            label-align-sm="right"
                            label-size="sm"
                        >
                            <b-input-group size="sm">
                                <b-form-input
                                    id="filter-input"
                                    v-model="filter"
                                    type="search"
                                    placeholder="Buscar"
                                ></b-form-input>
                            </b-input-group>
                        </b-form-group>
                    </b-col>
                    <b-table
                        striped
                        hover
                        responsive
                        :items="clientes"
                        :fields="fields"
                        show-empty
                        small
                        :current-page="currentPage"
                        :per-page="perPage"
                        :filter="filter"
                        @filtered="onFiltered"
                    >
                        <template #empty="scope">
                            <p>{{ textoVacio }}</p>
                        </template>
                        <template v-slot:cell(sucursal)="data">
                            {{ getSucursal(data.value) }}
                        </template>

                        <template v-slot:cell(Acciones)="row">
                            <div class="row-actions">
                                <b-button size="sm" v-b-modal="'clienteModal'" variant="primary"
                                          @click="loadModal(false,row)">
                                    {{ boton2 }}
                                </b-button>
                                <b-button size="sm" class="btn-danger" @click="borrar(row.item.id)">{{
                                    boton3
                                    }}
                                </b-button>
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
import FormProducto from './form'

export default {
    layout: Authenticated,
    props: {
        clientes: Array,
        sucursales: Object,
        errors: Object,
    },
    components: {
        FormProducto
    },
    data() {
        return {
            isNew: true,
            boton1: "Nuevo",
            boton2: "Modificar",
            boton3: "Borrar",
            titulo: 'Clientes',
            textoVacio: 'No existen Clientes',
            fields:
                [
                    'nombreCompleto',
                    'nombreResponsable',
                    'correo',
                    'telefono',
                    'sucursal',
                    'Acciones'
                ],
            itemRow: {},
            totalRows: 1,
            currentPage: 1,
            perPage: 20,
            filter: '',
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
            this.$inertia.delete(`cliente/${id}`, {
                onBefore: () => confirm('Esta seguro?'),
            })
        },
        getSucursal($id) {
            return this.sucursales[$id];
        },
        onFiltered(filteredItems) {
            // Trigger pagination to update the number of buttons/pages due to filtering
            this.totalRows = filteredItems.length
            this.currentPage = 1
        }
    },
    mounted() {
        // Set the initial number of items
        this.totalRows = this.clientes.length;
    },
}
</script>

