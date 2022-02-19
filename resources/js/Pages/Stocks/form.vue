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
            <template #modal-footer="{ ok, cancel }">
                <b-button variant="danger" @click="cancel()">
                    Cancel
                </b-button>
                <loading-button :loading="sending" variant="primary"
                                @click.native="ok()" :text="'Guardar'" :textLoad="'Guardando'">Guardar
                </loading-button>
            </template>
        </b-modal>
    </div>
</template>

<script>
import axios from "axios";
import LoadingButton from '@/Shared/LoadingButton'

export default {
    name: "Producto",
    props: {
        id: String,
        itemRow: Object,
        isNew: Boolean,
    },
    components: {
        LoadingButton
    },
    data() {
        return {
            sending: false,
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
        },
        limpiar() {
            for (let key in this.form) {
                this.form[key].state = null;
                this.form[key].stateText = null;
            }
            this.errors = [];
        },
        handleOk(bvModalEvt) {
            // Prevent modal from closing
            bvModalEvt.preventDefault();
            this.enviar();
        },
        enviar() {
            this.sending = true;
            this.limpiar();
            let producto = new FormData();
            for (let key in this.form) {
                producto.append(key, this.form[key].value);
            }
            if (this.itemRow['sucursal']) {
                producto.append('sucursal', this.itemRow['sucursal']);
            }
            if (this.itemRow['producto']) {
                producto.append('producto', this.itemRow['producto']);
            }
            let url = '/admin/stockLess';
            if (this.isNew) {
                url = '/admin/stockMore';
            }
            axios.post(url, producto, {headers: {'Content-Type': 'multipart/form-data'}})
                .then(({data}) => {
                    if (data["status"] === 0) {
                        this.$bvModal.hide(this.id);
                        this.$inertia.reload();
                    }
                    for (let key in this.form) {
                        if (key in data.errors) {
                            this.form[key].state = false;
                            this.form[key].stateText = data.errors[key][0];
                        } else {
                            this.form[key].state = true;
                            this.form[key].stateText = "";
                        }
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
