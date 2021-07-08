<template>
    <div class="content-w">
        <div class="content-box">
            <Menu :active="6" v-if="admin"></Menu>
            <div class="row" v-else>
                <div class="col-sm-12">
                    <h4 class="header-title m-t-0 m-b-20">Rendición Diaria</h4>
                </div>
            </div>
            <b-card >
                <b-form @submit.prevent="enviar">
                    <b-row>
                        <b-col md="5">
                            <b-form-group
                                :label="form.fechaI.label"
                                label-for="fechaI"
                                :state="form.fechaI.state"
                                label-cols-sm="3"
                            >
                                <b-input
                                    :type="form.fechaI.type"
                                    :placeholder="form.fechaI.label"
                                    v-model="form.fechaI.value"
                                    id="fechaI"
                                    :state="form.fechaI.state"
                                ></b-input>
                            </b-form-group>
                        </b-col>
                        <b-col md="5" v-if="admin">
                            <b-form-group
                                :label="form.fechaF.label"
                                label-for="fechaF"
                                :state="form.fechaF.state"
                                label-cols-sm="3"
                            >
                                <b-input
                                    :type="form.fechaF.type"
                                    :placeholder="form.fechaF.label"
                                    v-model="form.fechaF.value"
                                    id="fechaF"
                                    :state="form.fechaF.state"
                                ></b-input>
                            </b-form-group>
                        </b-col>
                        <b-col>
                            <b-button type="submit">Buscar</b-button>
                        </b-col>
                    </b-row>
                </b-form>
            </b-card>
            <b-card>
                <b-table
                    striped
                    hover
                    :items="data['table']['ingreso']"
                    :fields="data['fields']"
                    show-empty
                    small
                >
                    <template v-slot:cell(created_at)="data">
                        {{ data.value | moment("DD/MM/YYYY HH:mm") }}
                    </template>
                    <template #empty="scope">
                        <p>No existen Datos</p>
                    </template>
                </b-table>
                <b-table
                    striped
                    hover
                    :items="data['table']['egreso']"
                    :fields="data['fields']"
                    show-empty
                    small
                >
                    <template v-slot:cell(created_at)="data">
                        {{ data.value | moment("DD/MM/YYYY HH:mm") }}
                    </template>
                    <template #empty="scope">
                        <p>No existen Datos</p>
                    </template>
                </b-table>
            </b-card>
        </div>
    </div>
</template>

<script>
import Layout from '@/Shared/Layout'
import Menu from "@/Shared/menu/menuReportes";

export default {
    layout: Layout,
    name: "rendicionDiaria",
    components: {
        Menu
    },
    props: {
        admin: Boolean,
        sucursales:Object,
        request:Object,
        data:Array
    },
    data(){
        return {
            form: {
                fechaI: {
                    label: 'Fecha Inicio',
                    value: "",
                    type: "date",
                    state: null,
                    stateText: null
                },
                fechaF: {
                    label: 'Fecha Final',
                    value: "",
                    type: "date",
                    state: null,
                    stateText: null
                },
            },
        }
    },
    methods:{
        enviar(){
            let form = {};
            form['fechaI']=this.form.fechaI.value;
            if(this.admin){
                form['fechaF']=this.form.fechaF.value;
                // form.append('fechaI',this.form.fechaI.value);
            }
            this.$inertia.get(window.location.pathname,form);
        }
    }
}
</script>
