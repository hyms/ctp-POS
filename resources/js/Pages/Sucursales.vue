<template>
    <standar-table
        :items="sucursales"
        :errors="errors"
        :basePath="'sucursal'"
        :fields="fields"
        :title="'Sucursales'"

        :titleForm="'Sucursal'"
        :form="form"
        :urlPost="'/admin/sucursal'"

    >
    </standar-table>
</template>

<script>
import Authenticated from '@/Layouts/Authenticated'
import StandarTable from '@/Shared/standarTable';

export default {
    layout: Authenticated,
    props: {
        sucursales: Array,
        errors: Object,
    },
    components: {
        StandarTable
    },
    data() {
        return {
            fields:
                [
                    'nombre',
                    'telefono',
                    'gmap',
                    'central',
                    {
                        key: 'enable',
                        label:
                            'Habilitado',
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
                    options: this.sucursales,
                    isPadre: 1
                },
                central: {
                    label: 'Central',
                    value: "",
                    type: "boolean",
                    state: null,
                    stateText: null
                },
                enable: {
                    label: 'Habilitado',
                    value: "",
                    type: "boolean",
                    state: null,
                    stateText: null
                },
            },
        }
    },
}
</script>

