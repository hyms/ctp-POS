<template>
    <v-row>
        <v-col>
            <Form :tipo="tipo" :dialog="dialog" @close="close"></Form>
            <v-card>
                <v-card-title>
                    <v-btn
                        @click="loadModal()"
                        color="primary"
                        small
                        elevation="1"
                        class="mr-2 my-1"
                    >
                        Recibo
                        <v-icon right> mdi-file-document-plus </v-icon>
                    </v-btn>
                    <v-spacer></v-spacer>
                    <FormSearch :report="report"></FormSearch>
                </v-card-title>
                <v-data-table
                    :items="recibos"
                    :headers="fields"
                    :no-data-text="emptyText"
                    mobile-breakpoint="540"
                >
                    <template v-slot:item.created_at="{ item }">
                        {{ item.created_at | moment("DD/MM/YYYY HH:mm") }}
                    </template>
                    <template v-slot:item.Acciones="{ item }">
                        <v-btn
                            small
                            color="primary"
                            class="ma-1"
                            :href="'/reciboPdf/' + item.id"
                            target="_blank"
                        >
                            <v-icon> mdi-printer </v-icon>
                        </v-btn>
                    </template>
                </v-data-table>
            </v-card>
        </v-col>
    </v-row>
</template>

<script>
import Authenticated from "@/Layouts/Authenticated.vue";
import Form from "./form.vue";
import FormSearch from "./formSearch.vue";
import moment from "moment";

export default {
    layout: Authenticated,
    props: {
        recibos: Array,
        errors: Object,
        tipo: Number,
        report: Object,
    },
    components: {
        Form,
        FormSearch,
    },
    data() {
        return {
            isNew: true,
            textoVacio: "No existen recibos",
            fields: [
                { value: "secuencia", text: "Secuencia" },
                { value: "nombre", text: "Cliente" },
                { value: "detalle", text: "Detalle" },
                { value: "saldo", text: "Saldo" },
                { value: "monto", text: "Monto" },
                { value: "created_at", text: "Fecha" },
                { value: "Acciones", text: "Acciones" },
            ],
            editedItem: {},
            dialog: false,
            emptyText: "No existen Ordenes",
        };
    },
    methods: {
        loadModal() {
            this.dialog = true;
        },
        getSucursal($id) {
            return this.sucursales[$id];
        },
        close() {
            this.dialog = false;
        },
    },
};
</script>
