<template>
    <b-modal
        :id="id"
        :title="'Reposicion de '+titulo + ' '+(item.codigoServicio?item.codigoServicio:item.correlativo)">
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
                <template v-for="(product,key) in productos">
                    <b-tr>
                        <b-td>{{ product.formato }}</b-td>
                        <b-td>{{ product.dimension }}</b-td>
                        <b-td>
                            <b-form-spinbutton id="demo-sb" v-model="productosSell[key].cantidad" min="0"
                                               max="100" size="sm" inline></b-form-spinbutton>
                        </b-td>
                    </b-tr>
                </template>
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
            <template>
                <loading-button :loading="sending" :variant="'primary'" :size="'sm'"
                                @click.native="guardar()" :text="'Reponer'" :textLoad="'Guardando'" v-if="isNew">Reponer
                </loading-button>
                <a class="btn btn-outline-primary btn-sm" :href="getUrlPrint(item.id)" target="_blank" v-else>Imprimir</a>
            </template>
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
        isNew: Boolean,
        productosSell: Array,
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
            let items = [];
            for (let value of this.productosSell) {
                if (value.cantidad > 0) {
                    items = [...items, value];
                }
            }
            if (items.length > 0) {
                producto.append('productos', JSON.stringify(items));
            }
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
            return '/ordenPdf/' + id;
        }
    },
}
</script>
