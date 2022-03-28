<template>
    <standar-table
        :items="cajas"
        :errors="errors"
        :basePath="'caja'"
        :fields="fields"
        :title="'Cajas'"

        :titleForm="'Caja'"
        :form="form"
        :urlPost="'/admin/caja'"

    >
    </standar-table>
</template>

<script>
import Layout from '@/Shared/Layout'
import StandarTable from '@/Shared/standarTable';

export default {
    layout: Layout,
    props: {
        cajas: Array,
        sucursales: Object,
        errors: Object,
    },
    components: {
        StandarTable,
    },
    data() {
        return {
            fields:
                [
                    'nombre',
                    'monto',
                    'enable',
                    {
                        key: 'nombreSucursal',
                        label: 'Sucursal'
                    },
                    'Acciones'
                ],
            form: {
                nombre: {
                    label: 'nombre',
                    value: "",
                    type: "text",
                    state: null,
                    stateText: null
                }, descripcion: {
                    label: 'descripcion',
                    value: "",
                    type: "textarea",
                    state: null,
                    stateText: null
                }, sucursal: {
                    label: 'sucursal',
                    value: "",
                    type: "select",
                    state: null,
                    stateText: null,
                    options: this.sucursales
                }, dependeDe: {
                    label: 'Depende de',
                    value: "",
                    type: "select",
                    state: null,
                    stateText: null,
                    options: this.cajas,
                    isPadre: 1
                }, enable: {
                    label: 'enable',
                    value: "",
                    type: "boolean",
                    state: null,
                    stateText: null
                }, monto: {
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

