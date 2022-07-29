<template>
    <generic-table
        :items="productos"
        :errors="errors"
        :basePath="'producto'"
        :fields="fields"
        :title="'Productos'"

        :titleForm="'Producto'"
        :form="form"
        :urlPost="'/admin/producto'"

    >
    </generic-table>
</template>

<script>
import Authenticated from '@/Layouts/Authenticated'
import GenericTable from '@/Layouts/components/genericTable';

export default {
    layout: Authenticated,
    props: {
        productos: Array,
        tipoProducto: Array,
        errors: Object,
    },
    components: {
        GenericTable,
    },
    data() {
        return {
            fields:
                [
                    {
                        text: 'Codigo',
                        value: 'codigo'
                    },
                    {
                        text: 'Nombre',
                        value: 'formato',
                    },
                    {
                        text: 'Detalle',
                        value: 'dimension',
                    },
                    {
                        text: 'Tipo Producto',
                        value: 'productoTipoView',
                    },
                    {
                        text: 'Acciones',
                        value: 'Acciones'
                    },
                ],
            form: {
                codigo: {
                    label: 'Codigo',
                    value: "",
                    type: "text",
                    state: null,
                    stateText: null
                },
                formato: {
                    label: 'Nombre',
                    value: "",
                    type: "text",
                    state: null,
                    stateText: null
                },
                dimension: {
                    label: 'Detalle',
                    value: "",
                    type: "text",
                    state: null,
                    stateText: null
                },
                productoTipo: {
                    label: 'Tipo Producto',
                    value: "",
                    type: "group-check",
                    state: null,
                    stateText: null,
                    options: this.tipoProducto
                }
            },
        }
    },
}
</script>

