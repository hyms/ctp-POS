<template>
    <v-dialog v-model="dialog" max-width="500px" scrollable persistent>
        <v-card>
            <v-card-title>{{ titulo1 + ((tipo)?' de Egreso':' de Ingreso') }}</v-card-title>
            <v-card-text>
                <form>
                    <v-alert
                        dismissible
                        class="mb-4"
                        v-if="Object.keys(errorsData).length > 0"
                        v-model="alert"
                        color="red"
                        outlined
                        text
                    >
                        <ul class="text-sm">
                            <li v-for="(error, key) in errorsData" :key="key">{{ key }}: {{ error }}</li>
                        </ul>
                    </v-alert>
                    <template v-for="(item,key) in form">
                        <template v-if="['text','password','date','textarea','select','search'].includes(item.type)">
                            <v-text-field
                                v-model="item.value"
                                :id="key"
                                v-if="['text','password','date'].includes(item.type)"
                                outlined
                                dense
                                hide-details="auto"
                                :type="item.type"
                                :label="item.label"
                                :error="item.state"
                                :error-messages="item.stateText"
                                class="my-2"
                            ></v-text-field>
                            <v-textarea
                                v-if="item.type==='textarea'"
                                :id="key"
                                v-model="item.value"
                                rows="2"
                                outlined
                                dense
                                hide-details="auto"
                                :label="item.label"
                                :error="item.state"
                                :error-messages="item.stateText"
                                class="my-2"
                            ></v-textarea>
                        </template>
                    </template>
                </form>
            </v-card-text>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn small color="error" class="ma-1" @click="$emit('close')">
                    Cancelar
                </v-btn>
                <v-btn small color="primary" class="ma-1" @click="sended"
                       :loading="sending" :disabled="sending">
                    Guardar
                </v-btn>
            </v-card-actions>
            <!--        <b-modal
                        <template #modal-footer="{ ok, cancel }">
                            <b-button variant="danger" @click="cancel()" size="sm">
                                Cancel
                            </b-button>
                            <loading-button :loading="sending" variant="primary" size="sm"
                                            @click.native="ok()" :text="'Guardar'" :textLoad="'Guardando'">Guardar
                            </loading-button>
                        </template>
                    </b-modal>-->
        </v-card>
    </v-dialog>
</template>

<script>
import axios from "axios";

export default {
    name: "recibo",
    props: {
        tipo: Number,
        dialog:Boolean,
    },
    data() {
        return {
            sending: false,
            titulo1: "Nuevo Recibo",
            titulo2: "Modificar Recibo",
            form: {
                nombre: {
                    label: 'nombre',
                    value: "",
                    type: "text",
                    state: null,
                    stateText: null
                }, ciNit: {
                    label: 'ciNit',
                    value: "",
                    type: "text",
                    state: null,
                    stateText: null
                }, detalle: {
                    label: 'detalle',
                    value: "",
                    type: "textarea",
                    state: null,
                    stateText: null
                }, monto: {
                    label: 'Monto',
                    value: "",
                    type: "text",
                    state: null,
                    stateText: null
                }
            },
            errorsData: Array
        }
    },
    methods: {
        removeState() {
            for (let key in this.form) {
                this.form[key].state = null;
                this.form[key].stateText = null;
            }
            this.errorsData = [];
        },
        removeValues() {
            for (let key in this.form) {
                this.form[key].value = "";
            }
            this.client = Object.assign({}, {});
            this.clients = [];
        },
        setErrors(data) {
            this.errorsData = data.errors;
            for (let key in this.form) {
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
            for (let key in this.form) {
                formData.append(key, this.form[key].value);
            }
            formData.append('tipo', this.tipo);
            return formData;
        },
        sended() {
            this.sending = true;
            this.removeState();
            axios.post('/recibo', this.loadFormData(), {headers: {'Content-Type': 'multipart/form-data'}})
                .then(({data}) => {
                    if (data["status"] === 0) {
                        this.removeValues()
                        this.removeState()
                        this.$emit("close")
                        this.$inertia.get(data["path"])
                    } else {
                        this.setErrors(data)
                    }
                })
                .catch(error => {
                    // handle error
                    this.errors = error
                    console.log(error);
                }).finally(() => {
                this.sending = false;
            })
        }
    },
}
</script>
