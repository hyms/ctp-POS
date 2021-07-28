<template>
    <b-modal
        :id="id"
        :title="titulo + ' '+(item.codigoServicio?item.codigoServicio:item.correlativo)">
        <div><strong>Cliente:</strong> {{ item.responsable }}</div>
        <div><strong>Telefono:</strong> {{ item.telefono }}</div>
        <br>
        <div class="table-responsive">
            <table class="table">
                <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Productos</th>
                    <th scope="col">Cant.</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="(itemOrden,key) in item.detallesOrden">
                    <td>{{ key + 1 }}</td>
                    <td>{{ getProduct(itemOrden.stock) }}</td>
                    <td><b-form-spinbutton id="demo-sb" v-model="itemOrden.cantidad" min="0"
                                           max="100" size="sm" inline></b-form-spinbutton></td>
                </tr>
                </tbody>
            </table>
        </div>
        <div>
            <p><strong>Observaciones:</strong><br>
                <b-textarea size="sm" v-model="item.observaciones">
                </b-textarea>
            </p>
        </div>
        <template #modal-footer="{ ok, cancel }">
            <b-button size="sm" variant="danger" @click="cancel()">
                Cerrar
            </b-button>
            <a class="btn btn-secondary btn-sm" :href="getUrlPrint(item.id)" target="_blank">Imprimir</a>
            <loading-button :loading="sending" :variant="'dark'" :size="'sm'"
                             @click.native="guardar()" :text="'Guardar'" :textLoad="'Guardando'">Guardar
            </loading-button>
        </template>
    </b-modal>
</template>

<script>
import axios from "axios";
import LoadingButton from '@/Shared/LoadingButton'

export default {
    data() {
        return {
            titulo: "Orden",
            total: 0,
            monto: 0,
            sending: false,
        }
    },
    props: {
        productos: Array,
        item: Object,
        id: String,
        isVenta: Boolean
    },
    components: {
        LoadingButton
    },
    methods: {
        getProduct(id) {
            let item = {};
            for (let value of this.productos) {
                if (value.id == id) {
                    item = value;
                    break;
                }
            }
            if (item) {
                return item.formato + ' (' + item.dimension + ')';
            }
            return "";
        },
        guardar() {
            this.sending = true;
            let producto = new FormData();
            producto.append('item', JSON.stringify(this.item));
            axios.post('/reposicion', producto, {headers: {'Content-Type': 'multipart/form-data'}})
                .then(({data}) => {
                    if (data["status"] == 0) {
                        this.$bvModal.hide(this.id)
                        this.$inertia.get(data["path"])
                    } else {
                        for (let key in this.form) {
                            if (key in data.errors) {
                                this.form[key].state = false;
                                this.form[key].stateText = data.errors[key][0];
                            } else {
                                this.form[key].state = true;
                                this.form[key].stateText = "";
                            }
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
        },

        getObservaciones(item) {
            if (item)
                return item.replace(/\n/g, "<br/>");
            return "";
        },
        getUrlPrint(id) {
            if (this.isVenta) {
                return '/ordenPdfV/' + id
            }
            return '/ordenPdf/' + id;
        }
    },
}
</script>
