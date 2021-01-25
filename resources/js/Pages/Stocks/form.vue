<template>
    <div>
        <b-modal
            :id="id"
            :title="(isNew)?titulo1:titulo2"
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
                    >
                        <b-input
                            :type="item.type"
                            :placeholder="item.label"
                            v-model="item.value"
                            :id="key"
                            :state="item.state"
                        ></b-input>
                    </b-form-group>
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
        isNew:Boolean,
    },
    data() {
        return {

            titulo1: "Añadir",
            titulo2: "Quitar",
            form: {
                cantidad: {
                    label: 'Cantidad',
                    value: "",
                    type: "number",
                    state: null,
                    stateText: null
                },
            },
            errors: Array
        }
    },
    methods: {
        reset() {
            this.limpiar();
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
            let producto = new FormData();
            Object.keys(this.form).forEach(key => {
                producto.append(key, this.form[key].value);
            })
            axios.post('/admin/stock', producto, {headers: {'Content-Type': 'multipart/form-data'}})
                .then(({data}) => {
                    if (data["status"] == 0) {
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
