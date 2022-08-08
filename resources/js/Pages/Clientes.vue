<template>
    <generic-table
        :items="clientes"
        :errors="errors"
        :basePath="'cliente'"
        :fields="fields"
        :title="'Clientes'"

        :titleForm="'Cliente'"
        :form="form"
        :urlPost="'/admin/cliente'"

    >
    </generic-table>
</template>

<script>
import Authenticated from '@/Layouts/Authenticated.vue'
import GenericTable from '@/Layouts/components/genericTable.vue';

export default {
    layout: Authenticated,
    props: {
        clientes: Array,
        sucursales: Array,
        errors: Object,
    },
    components: {
        GenericTable
    },
    data() {
        return {
            fields:
                [
                     {
                        text: 'Nombre',
                        value: 'nombreResponsable'
                    },
                     {
                        text: 'Correo',
                        value: 'correo'
                    },
                     {
                        text: 'Telefono',
                        value: 'telefono'
                    },
                     {
                        text: 'Sucursal',
                        value: 'nombreSucursal'
                    },
                    {
                        text: 'Acciones',
                        value: 'Acciones'
                    },
                ],
            form: {
                nombreCompleto: {
                    label: 'nombreCompleto',
                    value: "",
                    type: "text",
                    state: null,
                    stateText: null
                }, nombreNegocio: {
                    label: 'nombreNegocio',
                    value: "",
                    type: "text",
                    state: null,
                    stateText: null
                }, nombreResponsable: {
                    label: 'nombreResponsable',
                    value: "",
                    type: "text",
                    state: null,
                    stateText: null
                }, correo: {
                    label: 'correo',
                    value: "",
                    type: "email",
                    state: null,
                    stateText: null
                }, telefono: {
                    label: 'telefono',
                    value: "",
                    type: "text",
                    state: null,
                    stateText: null
                }, direccion: {
                    label: 'direccion',
                    value: "",
                    type: "text",
                    state: null,
                    stateText: null
                }, nitCi: {
                    label: 'nitCi',
                    value: "",
                    type: "text",
                    state: null,
                    stateText: null
                }, codigo: {
                    label: 'codigo',
                    value: "",
                    type: "text",
                    state: null,
                    stateText: null
                }, sucursal: {
                    label: 'sucursal',
                    value: "",
                    type: "select",
                    state: null,
                    stateText: null,
                    options: this.sucursales
                }
            },
        }
    },

}
</script>

