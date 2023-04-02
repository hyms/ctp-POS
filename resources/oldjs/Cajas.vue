<template>
    <generic-table
        :items="cajas"
        :errors="errors"
        :basePath="'caja'"
        :fields="fields"
        :title="'Cajas'"

        :titleForm="'Caja'"
        :form="form"
        :urlPost="'/admin/caja'"

    >
    </generic-table>
</template>

<script>
import Authenticated from '@/Layouts/Authenticated.vue'
import GenericTable from '@/../../oldjs/components/genericTable.vue';

export default {
    layout: Authenticated,
    props: {
        cajas: Array,
        cajasOptions: Array,
        sucursales: Array,
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
                        text: 'Monto',
                        value: 'monto'
                    },
                    {
                        text: 'Habiitado',
                        value: 'enableView'
                    },
                    {
                        text: 'Sucursal',
                        value: 'nombreSucursal',
                    },
                    {
                        text: 'Acciones',
                        value: 'Acciones'
                    },
                ],
            form: {
                nombre: {
                    label: 'nombre',
                    value: "",
                    type: "text",
                    state: null,
                    stateText: null
                },
                descripcion: {
                    label: 'descripcion',
                    value: "",
                    type: "textarea",
                    state: null,
                    stateText: null
                },
                sucursal: {
                    label: 'sucursal',
                    value: "",
                    type: "select",
                    state: null,
                    stateText: null,
                    options: this.sucursales
                },
                dependeDe: {
                    label: 'Depende de',
                    value: "",
                    type: "select",
                    state: null,
                    stateText: null,
                    options: this.cajasOptions,
                    isPadre: 1
                },
                enable: {
                    label: 'enable',
                    value: "",
                    type: "boolean",
                    state: null,
                    stateText: null
                },
                monto: {
                    label: '',
                    value: "",
                    type: "hidden",
                    state: null,
                    stateText: null
                }
            },
        }
    },
}
</script>

