<template>
    <div class="content-w">
        <div class="content-box">
            <div class="row">
                <div class="col">
                    <div class="row">
                        <div class="col-sm-12">
                            <h4 class="header-title m-t-0 m-b-20">{{ titulo }}</h4>
                        </div>
                    </div>
                    <div class="row m-b-20 m-t-10">
                        <div class="col">
                            <b-button v-b-modal="'userModal'" @click="loadModal()">{{ boton1 }}</b-button>
                            <FormUser :isNew="isNew" id="userModal" :itemRow="itemRow" :sucursales="sucursales"
                                      :roles="roles"></FormUser>
                            <FormUserE id="userModalE" :itemRow="itemRow"></FormUserE>
                        </div>
                    </div>

                    <div class="row m-b-20">
                        <div class="col">
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
                                        <b-button v-b-modal="'userModal'" @click="loadModal(false,row)">
                                            {{ boton2 }}
                                        </b-button>
                                        <b-button v-b-modal="'userModal'" @click="loadModal(false,row)">
                                            {{ boton4 }}
                                        </b-button>
                                        <b-button class="btn-danger" @click="borrar(row.item.id)">
                                            {{ boton3 }}
                                        </b-button>
                                    </div>
                                </template>
                            </b-table>
                        </div>
                    </div>
                </div>
            </div>
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
            boton4: "Extras",
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
                if (this.roles[key].value == id) {
                    rol = this.roles[key].text;
                }
            }
            return rol;
        },
    }
}
</script>

