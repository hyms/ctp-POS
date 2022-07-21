<template>
    <standar-table
        :items="productos"
        :errors="errors"
        :basePath="'producto'"
        :fields="fields"
        :title="'Productos'"

        :titleForm="'Producto'"
        :form="form"
        :urlPost="'/admin/producto'"

    >
    </standar-table>
</template>

<script>
import Authenticated from '@/Layouts/Authenticated'
import Menu from '@/Shared/menu/menuProductos';
import StandarTable from '@/Shared/standarTable';

export default {
    layout: Authenticated,
    props: {
        productos: Array,
        tipoProducto: Object,
        errors: Object,
    },
    components: {
        StandarTable,
        Menu
    },
    data() {
        return {
            fields:
                [
                    'codigo',
                    {
                        key: 'formato',
                        label: 'Nombre'
                    },
                    {
                        key: 'dimension',
                        label: 'detalle'
                    },
                    {
                        key: 'productoTipoView',
                        label: 'Tipo Producto'
                    },
                    'Acciones'
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

