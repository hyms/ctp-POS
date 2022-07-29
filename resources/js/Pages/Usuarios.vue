<template>
    <generic-table
        :items="users"
        :errors="errors"
        :basePath="'user'"
        :fields="fields"
        :title="'Usuarios'"

        :titleForm="'Usuario'"
        :form="form"
        :urlPost="'/admin/user'"

    >
    </generic-table>
</template>

<script>
import Authenticated from '@/Layouts/Authenticated'
import GenericTable from '@/Layouts/components/genericTable';

export default {
    layout: Authenticated,
    props: {
        users: Array,
        sucursales: Array,
        roles: Array,
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
                        text: 'usuario',
                        value: 'username'
                    },
                    {
                        text: 'habilitado',
                        value: 'enableView'
                    },
                    {
                        text: 'sucursal',
                        value: 'nombreSucursal'
                    },
                    {
                        text: 'role',
                        value: 'nombreRol'
                    },
                    {
                        text: 'apellido',
                        value: 'apellido'
                    },
                    {
                        text: 'nombre',
                        value: 'nombre'
                    },
                    {
                        text: 'ultimo Acceso',
                        value: 'ultimoAcceso'
                    },
                    {
                        text: 'Acciones',
                        value: 'Acciones'
                    },
                ],
            form: {
                username: {
                    label: 'Usuario',
                    value: "",
                    type: "text",
                    state: null,
                    stateText: null
                }, password: {
                    label: 'Contraseña',
                    value: "",
                    type: "password",
                    state: null,
                    stateText: null
                }, apellido: {
                    label: 'Apellido',
                    value: "",
                    type: "text",
                    state: null,
                    stateText: null
                }, nombre: {
                    label: 'Nombre',
                    value: "",
                    type: "text",
                    state: null,
                    stateText: null
                }, ci: {
                    label: 'CI',
                    value: "",
                    type: "text",
                    state: null,
                    stateText: null
                }, telefono: {
                    label: 'Telefono',
                    value: "",
                    type: "text",
                    state: null,
                    stateText: null
                }, email: {
                    label: 'Correo',
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
                }, role: {
                    label: 'Rol',
                    value: "",
                    type: "select",
                    state: null,
                    stateText: null,
                    options: this.roles
                }, enable: {
                    label: 'Habilitado',
                    value: "",
                    type: "bool",
                    state: null,
                    stateText: null
                }

            },
        }
    },
}
</script>

