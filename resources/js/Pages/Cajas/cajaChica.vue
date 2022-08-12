<template>
    <generic-table
        :items="registros"
        :errors="errors"
        :basePath="'caja'"
        :fields="fields"
        :title="'Caja Chica'"
        :isDelete="false"
        :is-modify="false"
        :titleForm="'Registro de Caja Chica'"
        :form="form"
        :urlPost="'/cajaDebito'"

    >
    </generic-table>
</template>

<script>
import Authenticated from '@/Layouts/Authenticated.vue'
import genericTable from "@/Layouts/components/genericTable.vue";

export default {
    layout: Authenticated,
    props: {
        registros: Array,
        credito: Boolean,
        report: Object,
        errors: Object
    },
    components: {
        genericTable
    },
    data() {
        return {
            fields: [
                {
                    text: 'Detalle',
                    value: 'observaciones'
                },
                {
                    text: 'Monto',
                    value: 'monto'
                },
                {
                    text: 'Fecha',
                    value: 'created_at'
                },
            ],
            form: {
                observaciones: {
                    label: 'Descripcion',
                    value: "",
                    type: "textarea",
                    state: null,
                    stateText: null
                },
                monto: {
                    label: 'monto',
                    value: "",
                    type: "text",
                    state: null,
                    stateText: null
                }
            },
        }
    },
}
</script>
