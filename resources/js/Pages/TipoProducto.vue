<template>
    <standarTable
        :items="tipos"
        :errors="errors"
        :basePath="'tipoProductos'"
        :fields="fields"
        :title="'Tipo de Productos'"

        :titleForm="'Tipo de Producto'"
        :form="form"
        :urlPost="'/admin/tipoProductos'"
    >
    </standarTable>
</template>

<script>
import Authenticated from '@/Layouts/Authenticated'
import Menu from '@/Shared/menu/menuProductos';
import StandarTable from '@/Shared/standarTable';

export default {
    layout: Authenticated,
    props: {
        tipos: Array,
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
                    'nombre',
                    'codigo',
                    'Acciones'
                ],
            form: {
                nombre: {
                    label: 'Nombre',
                    value: "",
                    type: "text",
                    state: null,
                    stateText: null
                },
                codigo: {
                    label: 'Codigo',
                    value: "",
                    type: "text",
                    state: null,
                    stateText: null
                },
            },
        }
    },
}
</script>

