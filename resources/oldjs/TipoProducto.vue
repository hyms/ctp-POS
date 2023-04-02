<template>
    <generic-table
        :items="tipos"
        :errors="errors"
        :basePath="'tipoProductos'"
        :fields="fields"
        :title="'Tipo de Productos'"

        :titleForm="'Tipo de Producto'"
        :form="form"
        :urlPost="'/admin/tipoProductos'"
    >
    </generic-table>
</template>

<script>
import Authenticated from '@/Layouts/Authenticated.vue'
import GenericTable from '@/../../oldjs/components/genericTable.vue';

export default {
    layout: Authenticated,
    props: {
        tipos: Array,
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
                        text: 'Nombre',
                        value: 'nombre'
                    },
                    {
                        text: 'Codigo',
                        value: 'codigo'
                    },
                    {
                        text: 'Acciones',
                        value: 'Acciones'
                    },
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

