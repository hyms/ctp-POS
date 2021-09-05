<template>
    <div class="row">
        <div class="col-12">
            <b-card no-body>
                <b-card-header>
                    <strong>Saldos</strong>
                </b-card-header>
                <b-card-body>
                    <div class="table-responsive">
                        <b-table-simple class="table-hover table-small">
                            <b-thead>
                                <b-tr>
                                    <b-th>Formato</b-th>
                                    <b-th>Dimension</b-th>
                                    <b-th>Cant.</b-th>
                                </b-tr>
                            </b-thead>
                            <b-tbody>
                                <template v-for="(product,key) in productos">
                                    <b-tr>
                                        <b-td>{{ product.formato }}</b-td>
                                        <b-td>{{ product.dimension }}</b-td>
                                        <b-td>{{ product.cantidad }}</b-td>
                                    </b-tr>
                                </template>
                            </b-tbody>
                        </b-table-simple>
                    </div>
                </b-card-body>
            </b-card>
        </div>
    </div>
</template>

<script>
import Layout from '@/Shared/Layout'

export default {
    name: "Orden",
    layout: Layout,
    components: {},
    props: {
        id: String,
        productos: Array,
    },
    data() {
        return {
            sending: false,
            titulo1: "Nuevo Ingreso",
            titulo2: "Nuevo Egreso",
            form: {
                observaciones: {
                    label: 'Observaciones',
                    value: "",
                    type: "textarea",
                    state: null,
                    stateText: null
                }
            },
            idForm: null,
            errors: Array,
            options: [],
        }
    },
}
</script>
