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
                                                           {{value}}
                                                       </li>
                                                    </ul>
                                                </v-alert>
                                                <template v-for="(item,key) in form">
                                                    <v-text-field
                                                        :type="item.type"
                                                        :label="item.label"
                                                        outlined
                                                        dense
                                                        v-model="item.value"
                                                        :error="item.state"
                                                        :id="key"
                                                        :hint="item.stateText"
                                                        v-if="['text','password','date','email'].includes(item.type)"
                                                    ></v-text-field>
                                                    <v-textarea
                                                        v-if="item.type==='textarea'"
                                                        v-model="item.value"
                                                        :id="key"
                                                        auto-grow
                                                        outlined
                                                        dense
                                                        :error="item.state"
                                                        :label="item.label"
                                                        :hint="item.stateText"
                                                    ></v-textarea>
                                                    <v-select
                                                        v-if="item.type==='select'"
                                                        v-model="item.value"
                                                        item-text="text"
                                                        item-value="value"
                                                        outlined
                                                        dense
                                                        :error="item.state"
                                                        :items="getOptions(item.options,item.isPadre===1,editedItem.id)"
                                                        :label="item.label"
                                                    ></v-select>
                                                    <v-checkbox
                                                        v-if="item.type==='bool'"
                                                        v-model="item.value"
                                                        :id="key"
                                                        :hint="item.stateText"
                                                        :label="item.label"
                                                    ></v-checkbox>
                                                    <!--                                                <b-form-checkbox-group
                                                                                                        v-if="item.type==='group-check'"
                                                                                                        id="checkbox-group-1"
                                                                                                        v-model="item.value"
                                                                                                        :options="item.options"
                                                                                                        value-field="id"
                                                                                                        text-field="nombre"
                                                                                                    ></b-form-checkbox-group>
                                                                                                    <template v-for="(valueOption,keyOption) in item.options">
                                                                                                        <v-checkbox
                                                                                                            v-model="item.value"
                                                                                                            label="John"
                                                                                                            :value="valueOption"
                                                                                                        ></v-checkbox>
                                                                                                    </template>-->
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
            return (this.editedIndex === -1 ? 'Nuevo ' : 'Modificar ') + this.titleForm;
        },
    },
    components: {},
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
            for (let value of options) {
                if (id !== value.value) {
                    newOptions.push(value);
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
                this.form[key].value = ['enable'].includes(key)
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
                    if (['enable'].includes(key)) {
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
