<template>
    <div class="row">
        <div class="col-12">
            <div class="row mb-2">
                <div class="col">
                    <b-button v-b-modal="idModal" @click="loadModal()" variant="primary">{{ newText }}</b-button>
                    <modal-form
                        :isNew="isNew"
                        :id="idModal"
                        :itemRow="itemRow"

                        :titleForm="titleForm"
                        :form="form"
                        :urlPost="urlPost"
                    ></modal-form>
                </div>
            </div>
            <b-card no-body>
                <b-card-header>
                    <strong>{{ title }}</strong>
                </b-card-header>
                <b-card-body>
                    <b-table
                        striped
                        hover
                        responsive
                        :items="items"
                        :fields="fields"
                        show-empty
                        small
                    >
                        <template #empty="scope">
                            <p>{{ emptyText }}</p>
                        </template>
                        <template v-slot:cell(central)="data">
                            {{ (data.value === 1) ? "Si" : "No" }}
                        </template>
                        <template v-slot:cell(enable)="data">
                            {{ (data.value === 1) ? "Si" : "No" }}
                        </template>
                        <template v-slot:cell(Acciones)="row">
                            <div class="row-actions">
                                <b-button v-b-modal="idModal" @click="loadModal(false,row)" variant="primary" size="sm">
                                    {{ modifyText }}
                                </b-button>
                                <b-button class="btn-danger" @click="borrar(row.item.id)" size="sm">
                                    {{ deleteText }}
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
import Authenticated from '../Layouts/Authenticated'
import ModalForm from './standarForm'

export default {
    name: "standarTable",
    layout: Authenticated,
    props: {
        errors: Object,
//table
        title: String,
        titleForm: String,
        basePath: String,
        fields: Array,
        items: Array,
        //form
        form: Object,
        urlPost: String
    },
    components: {
        ModalForm,
    },
    data() {
        return {
            isNew: true,
            newText: "Nuevo",
            modifyText: "Modificar",
            deleteText: "Borrar",
            emptyText: 'No existen datos a mostrar',
            sureText: 'Esta seguro?',
            idModal: 'formModal',
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
            this.$inertia.delete(`${this.basePath}/${id}`, {
                onBefore: () => confirm(this.sureText),
            })
        },
    }
}
</script>
