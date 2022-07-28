<template>
    <div class="row">
        <div class="col-12">
            <v-dialog v-model="dialogDelete" max-width="250px">
                <v-card>
                    <v-card-title class="text-h5">{{ deleteText }}</v-card-title>
                    <v-card-text class="text-h6">
                        {{ sureText }}
                    </v-card-text>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn small color="error" @click="closeDelete">Cancel</v-btn>
                        <v-btn small color="primary" @click="deleteItemConfirm">OK</v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>
            <div class="row mb-1">
                <div class="col">
                    <v-dialog v-model="dialog" max-width="500px" persistent>
                        <template v-slot:activator="{ on, attrs }">
                            <v-btn
                                color="primary"
                                dark
                                v-bind="attrs"
                                v-on="on"
                            >
                                {{ newText }}
                            </v-btn>
                        </template>
                        <v-card>
                            <v-card-title>
                                <span class="text-h5">{{ formTitle }}</span>
                            </v-card-title>

                            <v-card-text>
                                <v-container>
                                    <v-row>
                                        <v-col>
                                            <form @submit.stop.prevent="sendForm">
                                                <v-alert dismissible type="error" col v-model="alert">
                                                    <ul>
                                                        <li v-for="(value,key) in errors">
                                                            {{ value }}
                                                        </li>
                                                    </ul>
                                                </v-alert>
                                                <template v-for="(item,key) in form">
                                                    <v-text-field
                                                        v-if="['text','password','date','email'].includes(item.type)"
                                                        :id="key"
                                                        v-model="item.value"
                                                        outlined
                                                        dense
                                                        hide-details="auto"
                                                        :type="item.type"
                                                        :label="item.label"
                                                        :error="item.state"
                                                        :error-messages="item.stateText"
                                                        class="mb-2"
                                                    ></v-text-field>
                                                    <v-textarea
                                                        v-if="item.type==='textarea'"
                                                        :id="key"
                                                        v-model="item.value"
                                                        auto-grow
                                                        outlined
                                                        dense
                                                        hide-details="auto"
                                                        :label="item.label"
                                                        :error="item.state"
                                                        :error-messages="item.stateText"
                                                        class="mb-2"
                                                    ></v-textarea>
                                                    <v-select
                                                        v-if="item.type==='select'"
                                                        :id="key"
                                                        v-model="item.value"
                                                        item-text="text"
                                                        item-value="value"
                                                        outlined
                                                        dense
                                                        hide-details="auto"
                                                        :items="getOptions(item.options,item.isPadre===1,editedIndex)"
                                                        :label="item.label"
                                                        :error="item.state"
                                                        :error-messages="item.stateText"
                                                        class="mb-2"
                                                    ></v-select>
                                                    <v-checkbox
                                                        v-if="item.type==='bool'"
                                                        v-model="item.value"
                                                        :id="key"
                                                        hide-details="auto"
                                                        :error-messages="item.stateText"
                                                        :label="item.label"
                                                    ></v-checkbox>
                                                    <template v-if="item.type==='group-check'">
                                                        <template v-for="(valueOption,keyOption) in item.options">
                                                            <v-checkbox
                                                                v-model="item.value"
                                                                :label="valueOption['nombre']"
                                                                :value="valueOption['id']"
                                                            ></v-checkbox>
                                                        </template>
                                                    </template>
                                                </template>
                                            </form>
                                        </v-col>
                                    </v-row>
                                </v-container>
                            </v-card-text>

                            <v-card-actions>
                                <v-spacer></v-spacer>
                                <v-btn small color="error" @click="close">
                                    Cancelar
                                </v-btn>
                                <v-btn small color="primary" @click="save"
                                       :loading="sending" :disabled="sending">
                                    Guardar
                                </v-btn>
                            </v-card-actions>
                        </v-card>
                    </v-dialog>
                </div>
            </div>
            <v-card>
                <v-card-title>
                    <strong>{{ title }}</strong>
                </v-card-title>
                <v-card-text>
                    <v-data-table
                        :items="items"
                        :headers="fields"
                        no-data-text="emptyText"
                        mobile-breakpoint="540"
                    >
                        <template v-slot:item.Acciones="{ item }">
                            <div class="row-actions">
                                <v-btn
                                    small
                                    class="mr-1"
                                    color="primary"
                                    @click="editItem(item)"
                                >
                                    <v-icon>
                                        mdi-pencil
                                    </v-icon>
                                </v-btn>
                                <v-btn
                                    color="error"
                                    small
                                    @click="deleteItem(item)"
                                >
                                    <v-icon>
                                        mdi-delete
                                    </v-icon>
                                </v-btn>
                            </div>
                        </template>
                        <template v-slot:item.central="{ item }">
                            {{ (item.central === 1) ? "Si" : "No" }}
                        </template>
                        <template v-slot:item.enable="{ item }">
                            {{ (item.enable === 1) ? "Si" : "No" }}
                        </template>
                    </v-data-table>
                </v-card-text>
            </v-card>
        </div>
    </div>
</template>

<script>
import axios from "axios";

export default {
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
    computed: {
        formTitle() {
            return `${this.editedIndex === -1 ? this.newText : this.modifyText} ${this.titleForm}`;
        },
    },
    data() {
        return {
            dialog: false,
            dialogDelete: false,
            alert: false,
            //btn's
            newText: "Nuevo",
            modifyText: "Modificar",
            deleteText: "Eliminar",
            //table
            emptyText: 'No existen datos a mostrar',
            sureText: 'Esta seguro?',
            editedIndex: -1,
            editedItem: {},
            //form
            sending: false,

        }
    },
    methods: {
        editItem(item) {
            this.editedIndex = item.id
            this.editedItem = Object.assign({}, item)
            this.setValues();
            this.dialog = true
        },

        deleteItem(item) {
            this.editedIndex = item.id
            this.dialogDelete = true
        },

        deleteItemConfirm() {
            this.$inertia.delete(`${this.basePath}/${this.editedIndex}`)
            this.closeDelete()
        },

        close() {
            this.dialog = false;
            this.alert = false;
            this.removeState();
            this.removeValues();
            this.$nextTick(() => {
                this.editedIndex = -1
                this.editedItem = Object.assign({}, {})
            })
        },

        closeDelete() {
            this.dialogDelete = false
            this.$nextTick(() => {
                this.editedItem = Object.assign({}, {})
                this.editedIndex = -1
            })
        },

        getOptions(options, isPadre, id) {
            if (!isPadre) {
                return options;
            }
            let newOptions = [];
            for (let key in options) {
                if (id !== options[key].value) {
                    newOptions.push(options[key]);
                }
            }
            return newOptions;
        },
        removeState() {
            for (let key in this.form) {
                this.form[key].state = null;
                this.form[key].stateText = null;
            }

        },
        removeValues() {
            for (let key in this.form) {
                this.form[key].value = "";
            }
        },
        setValues() {
            for (let key in this.form) {
                this.form[key].value = ['enable', 'central'].includes(key)
                    ? (this.editedItem[key] === 1)
                    : this.editedItem[key];
            }
        },
        setErrors(data) {
            for (let key in this.form) {
                this.errors = data.errors;
                if (key in data.errors) {
                    this.alert = true;
                    this.form[key].state = false;
                    this.form[key].stateText = data.errors[key][0];
                } else {
                    this.form[key].state = true;
                    this.form[key].stateText = "";
                }
            }
        },
        loadFormData() {
            let formData = new FormData();
            if (this.editedIndex > 0) {
                formData.append('id', this.editedIndex);
            }
            for (let key in this.form) {
                if (this.form[key].value != null) {
                    if (['enable', 'central'].includes(key)) {
                        formData.append(key, this.form[key].value ? '1' : '0');
                    } else {
                        formData.append(key, this.form[key].value);
                    }
                }
            }
            return formData;
        },
        save() {
            this.sending = true;
            this.removeState();
            axios.post(this.urlPost, this.loadFormData(), {headers: {'Content-Type': 'multipart/form-data'}})
                .then(({data}) => {
                    if (data["status"] === 0) {
                        this.close();
                        this.$inertia.reload();
                    }
                    this.setErrors(data);
                })
                .catch(error => {
                    // handle error
                    this.errors = error;
                    console.log(error);
                }).finally(() => {
                this.sending = false;
            })

        },
    },
    watch: {
        dialog(val) {
            val || this.close()
        },
        dialogDelete(val) {
            val || this.closeDelete()
        },
    },
}
</script>
