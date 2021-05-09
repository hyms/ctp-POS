<template>
    <b-modal
        :id="id"
        :title="titulo + ' #'+item.correlativo">
        <div><strong>Cliente:</strong> {{ item.responsable }}</div>
        <div><strong>Telefono:</strong> {{ item.telefono }}</div>
        <br>
        <table class="table">
            <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Productos</th>
                <th scope="col">cantidad</th>
                <th scope="col" v-if="isVenta">Precio</th>
                <th scope="col" v-if="isVenta">Total</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="(itemOrden,key) in item.detallesOrden">
                <td>{{ key + 1 }}</td>
                <td>{{ getProduct(itemOrden.stock) }}</td>
                <td>{{ itemOrden.cantidad }}</td>
                <td v-if="isVenta">
                    <b-input type="text" size="sm" v-model="itemOrden.costo" v-if="item.estado==1"></b-input>
                    <template v-else>{{ itemOrden.costo }}</template>
                </td>
                <td v-if="isVenta">{{ itemOrden.costo * itemOrden.cantidad }}</td>
            </tr>
            </tbody>
            <tfoot v-if="isVenta">
            <tr>
                <td colspan="4" class="text-right"><strong>Total</strong></td>
                <td>{{ getTotal(item.detallesOrden) }}</td>
            </tr>
            <tr>
                <td colspan="4" class="text-right"><strong>Cancelado</strong></td>
                <td>
                    <b-input type="text" size="sm" v-model="item.montoVenta" v-if="item.estado==1"></b-input>
                    <template v-else>{{ item.montoVenta }}</template>
                </td>
            </tr>
            <tr>
                <td colspan="4" class="text-right"><strong>Cambio</strong></td>
                <td>{{ getCambio() }}</td>
            </tr>
            </tfoot>
        </table>
        <div>
            <p><strong>Observaciones:</strong><br>
                {{ item.observaciones }}
            </p>
        </div>
        <template #modal-footer="{ ok, cancel }">
            <b-button size="sm" variant="danger" @click="cancel()">
                Cerrar
            </b-button>
            <a
                class="btn btn-dark btn-sm"
                :href="'/ordenPdf/'+item.id"
                target="_blank"
                v-if="!isVenta">Imprimir</a>
            <b-button size="sm" variant="dark" @click="guardarVenta()" v-if="isVenta && item.estado==1">
                Guardar
            </b-button>
        </template>
    </b-modal>
</template>

<script>
import axios from "axios";

export default {
    data() {
        return {
            titulo: "Orden",
            total: 0
        }
    },
    props: {
        productos: Array,
        item: Object,
        id: String,
        isVenta: Boolean
    },
    methods: {
        getProduct(id) {
            let item = {};
            Object.values(this.productos).forEach((value) => {
                if (value.id == id) {
                    item = value;
                    return value;
                }
            })
            if (item) {
                return item.formato + ' (' + item.dimension + ')';
            }
            return "";
        },
        guardarVenta() {
            this.reajusteOrden();
            let producto = new FormData();
            producto.append('item', JSON.stringify(this.item));
            axios.post('/ordenVenta', producto, {headers: {'Content-Type': 'multipart/form-data'}})
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
        },
        getTotal(detalle) {
            if (detalle) {
                let total = 0;
                Object.values(detalle).forEach(value => {
                    if (value) {
                        total += value.costo * value.cantidad;
                    }
                })
                this.total = total;
                return total;
            }
            return 0;
        },
        getCambio() {
            return (this.item.montoVenta - this.total);
        },
        reajusteOrden() {
            if (this.item.montoVenta > this.total) {
                this.item.montoVenta = this.total;
            }
        }
    },
    components: {},
}
</script>
