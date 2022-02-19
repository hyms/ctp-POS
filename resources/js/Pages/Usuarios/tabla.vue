<template>
    <div class="row">
        <div class="col-12">
            <div class="row mb-2">
                <div class="col">
                    <b-button v-b-modal="'userModal'" @click="loadModal()" variant="primary">{{ boton1 }}</b-button>
                    <FormUser :isNew="isNew" id="userModal" :itemRow="itemRow" :sucursales="sucursales"
                              :roles="roles"></FormUser>
                </div>
            </div>
            <b-card no-body>
                <b-card-header>
                    <strong>{{ titulo }}</strong>
                </b-card-header>
                <b-card-body>
                    <b-table
                        striped
                        hover
                        responsive
                        :items="userss"
                        :fields="fields"
                        show-empty
                        small
                    >
                        <template v-slot:cell(sucursal)="data">
                            {{ getSucursal(data.value) }}
                        </template>
                        <template v-slot:cell(role)="data">
                            {{ getRoles(data.value) }}
                        </template>
                        <template v-slot:cell(enable)="data">
                            {{ (data.value === 1) ? "Si" : "No" }}
                        </template>
                        <template v-slot:cell(Acciones)="row">
                            <div class="row-actions">
                                <b-button v-b-modal="'userModal'" @click="loadModal(false,row)" variant="primary">
                                    {{ boton2 }}
                                </b-button>
                                <b-button class="btn-danger" @click="borrar(row.item.id)">
                                    {{ boton3 }}
                                </b-button>
                            </div>
                        </template>
                    </b-table>
                </b-card-body>
            </b-card>
        </div>
    </div>
</template>

<script>
import Layout from '@/Shared/Layout'
import FormUser from './form'

export default {
    layout: Layout,
    props: {
        userss: Array,
        sucursales: Object,
        roles: Array,
        errors: Object,
    },
    components: {
        FormUser,
    },
    data() {
        return {
            isNew: true,
            boton1: "Nuevo",
            boton2: "Modificar",
            boton3: "Borrar",
            titulo: 'Usuarios',
            textoVacio: 'No existen Usuarios',
            idModal: 'userModal',
            fields:
                [
                    'username',
                    'enable',
                    'sucursal',
                    'role',
                    'apellido',
                    'nombre',
                    'ultimoAcceso',
                    'Acciones'
                ],
            itemRow: {}
        }
    },
    methods: {
        loadModal(isNew = true, item = null) {
            this.isNew = isNew;
            this.itemRow = {};
            if (!isNew) {
                this.itemRow = item.item;
            }
        },
        borrar(id) {
            this.$inertia.delete(`user/${id}`, {
                onBefore: () => confirm('Esta seguro?'),
            })
        },
        getSucursal(id) {
            return this.sucursales[id];
        },
        getRoles(id) {
            let rol = '';
            for (let key in this.roles) {
                if (this.roles[key].value === id) {
                    rol = this.roles[key].text;
                }
            }
            return rol;
        },
    }
}
</script>

