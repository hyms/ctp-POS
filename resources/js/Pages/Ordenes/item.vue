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
                        <b-input type="text" size="sm" v-model="itemOrden.costo" v-if="[1,5].includes(item.estado)"></b-input>
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
                <template v-if="[1,5].includes(item.estado)">
                    <tr>
                        <td colspan="4" class="text-right"><strong>Cancelado</strong></td>
                        <td>
                            <b-input type="text" size="sm" v-model="item.montoVenta" v-if="[1,5].includes(item.estado)"></b-input>
                            <template v-else>{{ item.montoVenta }}</template>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4" class="text-right"><strong>Cambio</strong></td>
                        <td>{{ getCambio() }}</td>
                    </tr>
                </template>
                </tfoot>
            </table>
        </div>
        <div v-if="item.estado==2">
            <form>
                <b-row>
                    <b-col>
                        <b-form-group
                            label="Cancelado"
                            label-for="cancelado"
                        >
                            <b-input
                                type="text"
                                placeholder="Cancelado"
                                v-model="item.montoVenta"
                                id="cancelado"
                                name="cancelado"
                                disabled
                            ></b-input>
                        </b-form-group>
                    </b-col>
                    <b-col>
                        <b-form-group
                            label="A Cuenta"
                            label-for="cuenta"
                        >
                            <b-input
                                type="text"
                                placeholder="Cuenta"
                                v-model="monto"
                                id="cuenta"
                                name="cuenta"
                            ></b-input>
                        </b-form-group>
                    </b-col>
                    <b-col>
                        <b-form-group
                            label="Saldo"
                            label-for="saldo"
                        >
                            <b-input
                                type="text"
                                placeholder="Saldo"
                                :value="getSaldo()"
                                id="saldo"
                                name="saldo"
                                disabled
                            ></b-input>
                        </b-form-group>
                    </b-col>
                </b-row>
            </form>
        </div>
        <div>
            <p><strong>Observaciones:</strong><br>
                {{ item.observaciones }}
            </p>
        </div>
        <template #modal-footer="{ ok, cancel }">
            <loading-button :loading="sending" :variant="'warning'" :size="'sm'" v-if="![0,2,5].includes(item.estado)"
                            @click.native="quemado(item.id)" :text="'Quemado'" :textLoad="'Quemado...'">Quemado
            </loading-button>
            <b-button size="sm" variant="danger" @click="cancel()">
                Cerrar
            </b-button>
            <a
                class="btn btn-secondary btn-sm"
                :href="'/ordenPdf/'+item.id"
                target="_blank">Imprimir</a>
            <loading-button :loading="sending" :variant="'dark'" :size="'sm'" v-if="isVenta && [1,5].includes(item.estado)"
                            @click.native="guardarVenta()" :text="'Guardar'" :textLoad="'Guardando'">Guardar
            </loading-button>
            <loading-button :loading="sending" :variant="'dark'" :size="'sm'" v-if="isVenta && item.estado==2"
                            @click.native="guardarDeuda()" :text="'Pagar'" :textLoad="'Pagando'">Pagar
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
        guardar(url) {
            this.sending = true;
            this.reajusteOrden();
            let producto = new FormData();
            producto.append('item', JSON.stringify(this.item));
            if (this.monto) {
                producto.append('monto', this.monto);
            }
            axios.post(url, producto, {headers: {'Content-Type': 'multipart/form-data'}})
                .then(({data}) => {
                    if (data["status"] == 0) {
                        this.$bvModal.hide(this.id)
                        this.$inertia.get(data["path"])
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
                }).finally(() => {
                this.sending = false;
            })
        },
        guardarVenta() {
            this.guardar('/ordenVenta');
        },
        guardarDeuda() {
            this.guardar('/ordenDeuda');
        },
        quemado(id) {
            this.sending = true;
            this.$inertia.post('/orden/quemado', {id: id}, {
                onBefore: () => confirm('Esta seguro?'),
                onSuccess: () => {
                    this.$bvModal.hide(this.id);
                    this.sending = false;
                },
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
        getSaldo() {
            return (this.total - this.item.montoVenta - this.monto);
        },
        reajusteOrden() {
            if (this.item.montoVenta > this.total) {
                this.item.montoVenta = this.total;
            }
        }
    },
}
</script>
