<script setup>
import {ref} from "vue";
import Layout from '@/Layouts/Authenticated.vue'
import {VDataTableServer} from 'vuetify/labs/VDataTable'

defineProps({
    users: Array,
    warehouses: Array,
    roles: Array,
    errors: Object,
});
const search = ref("");
const loading = ref(false);
const alertType = ref('info');
const alertMessage = ref('');
const alert = ref(false);
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

//------ Checked Status User
function isChecked(user) {
    loading.value = true;
    consol(!!!user.statut);
    axios.post("users_switch_activated/" + user.id, {
        statut: !!!user.statut,
        id: user.id
    }).then(({data}) => {
        consol(data);
        if (data.success) {
            alert.value = true;
            alertType.value = "success";
            if (!!!user.statut) {
                user.statut = 1;
                alertMessage.value = "Usuario activado";
            } else {
                user.statut = 0;
                alertMessage.value = "Usuario desactivado";
            }
        } else {

            user.statut = 1;
            alert.value = true;
            alertType.value = "warning";
            alertMessage.value = "algo salio mal";

        }
    }).catch((errors) => {
        consol(errors);
        user.statut = 1;
        alert.value = true;
        alertType.value = "warning";
        alertMessage.value = "algo salio mal";
    }).finally(() => {
        loading.value = false;
    });
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
        <v-alert
            closable
            class="mb-3"
            v-model="alert"
            :type="alertType"
            :text="alertMessage"
            variant="elevated"
        >
        </v-alert>
        <v-data-table-server
            :headers="fields"
            :items="users"
            :items-length="users.length"
            :search="search"
            density="compact"
            class="elevation-2"
            no-data-text="No existen datos a mostrar"
            :loading="loading"
            loading-text="Cargando... "
        >
            <template v-slot:item.statut="{ item }">
                <v-switch density="compact" hide-details :model-value="!!item.raw.statut" color="primary" @change="isChecked(item.raw)"></v-switch>
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

