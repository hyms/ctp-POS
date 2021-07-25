<template>
    <div class="content-w">
        <div class="content-box">
            <Menu :active="0"></Menu>
            <div class="tab-content">
                <b-card>
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
                            <b-col md="5">
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
                <h5>{{fechaI|moment('DD/MM/YYYY')}}-{{fechaF|moment('DD/MM/YYYY')}}</h5>
                <b-card v-for="(values,key) in totalOrdenes" :key="key">
                    <template #header>
                        <h5 class="mb-0">{{ values.name }}</h5>
                    </template>
                    <b-table-simple hover small caption-top responsive>
                        <b-thead head-variant="dark">
                            <b-tr>
                                <b-th></b-th>
                                <b-th v-for="(value,key2) in values.value" :key="key2">{{ value.name }}</b-th>
                            </b-tr>
                        </b-thead>
                        <b-tbody>
                            <b-tr v-for="(row,rowkey) in values.value[0].value" :key="rowkey">
                                <b-td>{{ row.name }}</b-td>
                                <b-td v-for="(value,key2) in values.value" :key="key2">{{ value.value[rowkey].value }}</b-td>
                            </b-tr>
                        </b-tbody>
                    </b-table-simple>
                </b-card>

            </div>
        </div>
    </div>
</template>

<script>
import Layout from '@/Shared/Layout'
import Menu from "./menuReportes";

export default {
    layout: Layout,
    props: {
        totalOrdenes: Array,
        fechaI: String,
        fechaF: String,
    },
    components: {
        Menu
    },
    data() {
        return {
            form: {
                fechaI: {
                    label: 'Desde',
                    value: "",
                    type: "date",
                    state: null,
                    stateText: null
                },
                fechaF: {
                    label: 'Hasta',
                    value: "",
                    type: "date",
                    state: null,
                    stateText: null
                },
            },
        }
    },
    methods: {
        enviar() {
            let form = {};
            for (const key in this.form) {
                form[key] = this.form[key].value;
            }
            this.$inertia.get('/admin/reportes/resumen', form)
        },
    },
}
</script>
