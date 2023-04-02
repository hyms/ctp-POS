<script setup>
import {ref} from "vue";
import Layout from '@/Layouts/Authenticated.vue'
import GTable from '@/Layouts/genericTable.vue';
import {VDataTableServer} from 'vuetify/labs/VDataTable'

defineProps({
    users: Array,
    warehouses: Array,
    roles: Array,
    errors: Object,
});
const search = ref("");
const loading = ref(false);
const fields = ref([
    {
        title: 'Usuario',
        key: 'username'
    },
    {
        title: 'Apellido',
        key: 'lastname'
    },
    {
        title: 'Nombre',
        key: 'firtname'
    },
    {
        title: 'Telefono',
        key: 'phone'
    },
    {
        title: 'Estado',
        key: 'statut'
    },
    {
        title: 'Acciones',
        key: 'accions'
    },
]);
const form = ref({
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
        // options: this.warehouses
    }, role: {
        label: 'Rol',
        value: "",
        type: "select",
        state: null,
        stateText: null,
        // options: this.roles
    }, enable: {
        label: 'Habilitado',
        value: "",
        type: "bool",
        state: null,
        stateText: null
    }
});

function consol(item) {
    console.log(item)
}
</script>

<template>
    <Layout>
        <v-text-field
            v-model="search"
            append-icon="mdi-magnify"
            label="Buscar"
            density="compact"
            variant="solo"
            single-line
            hide-details
            class="mb-3"
        ></v-text-field>

        <v-data-table-server
            :headers="fields"
            :items="users"
            :search="search"
            density="compact"
            class="elevation-2"
            no-data-text="No existen datos a mostrar"
            :loading="loading"
            loading-text="Cargando... "
        >
            <template v-slot:item.statut="{ item }">
                <v-switch :model-value="!!item.raw.statut" color="primary" @click="consol(!!item.raw.statut)"></v-switch>
            </template>
            <template v-slot:item.accions="{ item }">
                <div class="row-actions">
                    <v-btn
                        size="x-small"
                        color="primary"
                        variant="outlined"
                        class="ma-1"
                        @click="editItem(item.raw)"
                        icon="mdi-pencil"
                    >
                    </v-btn>
                </div>
            </template>
            <template v-slot:column.name="{ column }">
                {{ column.title.toUpperCase() }}
            </template>
        </v-data-table-server>
    </Layout>
</template>

