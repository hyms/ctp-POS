<template>
    <generic-table
        :items="sucursales"
        :errors="errors"
        :basePath="'sucursal'"
        :fields="fields"
        :title="'Sucursales'"

        :titleForm="'Sucursal'"
        :form="form"
        :urlPost="'/admin/sucursal'"

    >
    </generic-table>
</template>

<script>
import Authenticated from '@/Layouts/Authenticated.vue'
import genericTable from '@/Layouts/components/genericTable.vue';

export default {
    layout: Authenticated,
    props: {
        sucursales: Array,
        sucursalOptions: Array,
        errors: Object,
    },
    components: {
        genericTable
    },
    data() {
        return {
            fields:
                [
                    {
                        value: 'nombre',
                        text: 'Nombre',
                    },
                    {
                        value: 'telefono',
                        text: 'Telefono',
                    },
                    {
                        value: 'centralView',
                        text: 'Central',
                    },
                    {
                        value: 'enableView',
                        text: 'Habilitado',
                    },
                    {
                        value: 'Acciones',
                        text: 'Acciones',
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
                nombre: {
                    label: 'Nombre',
                    value: "",
                    type: "text",
                    state: null,
                    stateText: null
                },
                descripcion: {
                    label: 'Descripcion',
                    value: "",
                    type: "textarea",
                    state: null,
                    stateText: null
                },
                telefono: {
                    label: 'Telefono',
                    value: "",
                    type: "text",
                    state: null,
                    stateText: null
                },
                gmap: {
                    label: 'Coordenadas Gmap',
                    value: "",
                    type: "text",
                    state: null,
                    stateText: null
                },
                dependeDe: {
                    label: 'Depende de',
                    value: "",
                    type: "select",
                    state: null,
                    stateText: null,
                    options: this.sucursalOptions,
                    isPadre: 1
                },
                enable: {
                    label: 'Habilitado',
                    value: "",
                    type: "bool",
                    state: null,
                    stateText: null
                },
            },
        }
    },
}
</script>

