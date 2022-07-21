<template>
    <standar-table
        :items="userss"
        :errors="errors"
        :basePath="'user'"
        :fields="fields"
        :title="'Usuarios'"

        :titleForm="'Usuario'"
        :form="form"
        :urlPost="'/admin/user'"

    >
    </standar-table>
</template>

<script>
import Authenticated from '@/Layouts/Authenticated'
import StandarTable from '@/Shared/standarTable';

export default {
    layout: Authenticated,
    props: {
        userss: Array,
        sucursales: Object,
        roles: Array,
        errors: Object,
    },
    components: {
        StandarTable,
    },
    data() {
        return {
            fields:
                [
                    'username',
                    'enable',
                    {
                        key: 'nombreSucursal',
                        label: 'sucursal'
                    },
                    {
                        key: 'nombreRol',
                        label: 'role'
                    },
                    'apellido',
                    'nombre',
                    'ultimoAcceso',
                    'Acciones'
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

