<template>
    <div>
        <b-modal
            :id="id"
            :title="titulo1"
            @show="reset"
            @hidden="reset"
            @ok="handleOk">
            <form @submit.stop.prevent="enviar">
                <b-alert dismissible :show="errors.length">
                    {{ errors }}
                </b-alert>
                <template v-for="(item,key) in form">
                    <b-form-group
                        :label="item.label"
                        :label-for="key"
                        :state="item.state"
                        :invalid-feedback="item.stateText"
                        v-if="['text','password','date','textarea','email','select'].includes(item.type)"
                    >
                        <b-input
                            :type="item.type"
                            :placeholder="item.label"
                            v-model="item.value"
                            :id="key"
                            :state="item.state"
                            v-if="['text','password','date','email'].includes(item.type)"
                        ></b-input>
                        <b-form-select
                            v-if="item.type==='select'"
                            v-model="item.value"
                            :options="options"
                        >
                            <template #first>
                                <b-form-select-option :value="null">Seleccione una opcion</b-form-select-option>
                            </template>
                        </b-form-select>
                    </b-form-group>
                    <b-checkbox
                        v-if="item.type==='bool'"
                        v-model="item.value"
                        :id="key"
                        :state="item.state"
                    >{{ item.label }}
                    </b-checkbox>
                </template>
            </form>
        </b-modal>
    </div>
</template>

<script>
import axios from "axios";

export default {
    name: "Producto",
    props: {
        id: String,
        itemRow: Object,
    },
    data() {
        return {
            boton1: "Nuevo",
            boton2: "Modificar",
            titulo1: "Datos Extras",
            form: {
                tipoImpresora: {
                    label: 'Tipo de Impresora',
                    value: "",
                    type: "select",
                    state: null,
                    stateText: null
                },impresora: {
                    label: 'Nombre de la Impresora',
                    value: "",
                    type: "text",
                    state: null,
                    stateText: null
                }
            },
            idForm: null,
            errors: Array,
            options:[]
        }
    },
    methods: {
        reset() {
            this.limpiar();

            if ('id' in this.itemRow) {
                this.idForm = this.itemRow['id'];
            }
            Object.keys(this.form).forEach(key => {
                this.form[key].value = this.itemRow[key];
            })
        },
        limpiar() {
            Object.keys(this.form).forEach(key => {
                this.form[key].state = null;
                this.form[key].stateText = null;
            })
            this.errors = [];
        },
        handleOk(bvModalEvt) {
            // Prevent modal from closing
            bvModalEvt.preventDefault();
            this.enviar();
        },
        enviar() {
            this.limpiar();
            let user = new FormData();
            if (this.idForm) {
                user.append('id', this.idForm);
            }
            Object.keys(this.form).forEach(key => {
                if(this.form[key].value !=null) {
                    if (['enable'].includes(key)) {
                        user.append(key, this.form[key].value ? '1' : '0');
                    } else {
                        user.append(key, this.form[key].value);
                    }
                }
            })
            /* this.$inertia.post('/admin/producto',producto, {
                 onSuccess: (page) => {
                     console.log(page);
                 },
                 onError: (errors) => {
                     console.log(errors);
                 }
             });*/
            axios.post('/admin/user', user, {headers: {'Content-Type': 'multipart/form-data'}})
                .then(({data}) => {
                    if (data["status"] === 0) {
                        location.href = data["path"];
                    }
                    Object.keys(this.form).forEach(key => {
                        if (key in data.errors) {
                            this.form[key].state = false;
                            this.form[key].stateText = data.errors[key][0];
                        } else {
                            this.form[key].state = true;
                            this.form[key].stateText = "";
                        }
                    })
                })
                .catch(error => {
                    // handle error
                    this.errors = error
                    console.log(error);
                })
        }
    },
}
</script>
