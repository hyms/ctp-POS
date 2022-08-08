<template>
    <v-dialog
        v-model="dialog" max-width="500px" scrollable persistent>
        <v-card>
            <v-card-title>
                {{ titulo + ' ' + (item.codigoServicio ?? item.correlativo) }}
            </v-card-title>
            <v-card-text>
                <div><strong>Cliente:</strong> {{ item.responsable }}</div>
                <div><strong>Telefono:</strong> {{ item.telefono }}</div>
                <br>
                <v-simple-table dense class="overflow-x-auto">
                    <template v-slot:default>
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Productos</th>
                            <th>Cant.</th>
                            <th v-if="isVenta">Precio</th>
                            <th v-if="isVenta">Total</th>
                        </tr>
                        </thead>
                        <tbody>
                        <template v-for="(itemOrden,key) in item.detallesOrden">
                            <tr>
                                <td>{{ key + 1 }}</td>
                                <td>{{ getProduct(itemOrden.stock) }}</td>
                                <td>{{ itemOrden.cantidad }}</td>
                                <td v-if="isVenta">
                                    <v-text-field
                                        type="text"
                                        v-model="itemOrden.costo"
                                        outlined
                                        dense
                                        single-line
                                        style="min-width: 50px; max-width: 100px"
                                        class="my-1 texto-small"
                                        hide-details="auto"
                                        v-if="[1,5].includes(item.estado)"></v-text-field>
                                    <template v-else>{{ parseFloat(itemOrden.costo).toFixed(2) }}</template>
                                </td>
                                <td v-if="isVenta">{{
                                        parseFloat(itemOrden.costo * itemOrden.cantidad).toFixed(2)
                                    }}
                                </td>
                            </tr>
                        </template>
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
                                    <v-text-field
                                        type="text"
                                        v-model="item.montoVenta"
                                        outlined
                                        dense
                                        single-line
                                        style="min-width: 50px; max-width: 100px"
                                        class="my-1 texto-small"
                                        hide-details="auto"
                                        v-if="[1,5].includes(item.estado)"></v-text-field>
                                    <template v-else>{{ item.montoVenta }}</template>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="4" class="text-right"><strong>Cambio</strong></td>
                                <td>{{ getCambio() }}</td>
                            </tr>
                        </template>
                        </tfoot>
                    </template>
                </v-simple-table>
                <div v-if="item.estado===2">
                    <form>
                        <v-row>
                            <v-col>
                                <v-text-field
                                    label="Cancelado"
                                    type="text"
                                    placeholder="Cancelado"
                                    v-model="item.montoVenta"
                                    id="cancelado"
                                    name="cancelado"
                                    disabled
                                    outlined
                                    dense
                                ></v-text-field>
                            </v-col>
                            <v-col>
                                <v-text-field
                                    label="A Cuenta"
                                    type="text"
                                    placeholder="A Cuenta"
                                    v-model="monto"
                                    id="cuenta"
                                    name="cuenta"
                                    outlined
                                    dense
                                ></v-text-field>
                            </v-col>
                            <v-col>
                                <v-text-field
                                    label="Saldo"
                                    type="text"
                                    placeholder="Saldo"
                                    :value="getSaldo()"
                                    id="saldo"
                                    name="saldo"
                                    disabled
                                    outlined
                                    dense
                                ></v-text-field>
                            </v-col>
                        </v-row>
                    </form>
                </div>
                <div>
                    <p><strong>Observaciones:</strong><br>
                        <span v-html="getObservaciones(item.observaciones)"></span>
                    </p>
                </div>
            </v-card-text>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn small color="error" class="ma-1" @click="$emit('close')">
                    Cancelar
                </v-btn>
                <v-btn small color="primary" class="ma-1"
                       outlined :href="getUrlPrint(item.id)" target="_blank">
                    Imprimir
                </v-btn>
                <v-btn small color="primary" class="ma-1"
                       v-if="isVenta && [1,5].includes(item.estado)"
                       @click="saveVenta()"
                       :loading="sending" :disabled="sending">
                    Guardar
                </v-btn>
                <v-btn small color="primary" class="ma-1"
                       v-if="isVenta && item.estado===2"
                       @click="saveDeuda()"
                       :loading="sending" :disabled="sending">
                    Pagar
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
import axios from "axios";

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
        isVenta: Boolean,
        dialog: Boolean
    },
    methods: {
        getProduct(id) {
            let item = {};
            for (let value of this.productos) {
                if (value.id === id) {
                    item = value;
                    break;
                }
            }
            if (item) {
                return item.formato + ' (' + item.dimension + ')';
            }
            return "";
        },
        seved(url) {
            this.sending = true;
            this.resyncOrden();
            let producto = new FormData();
            producto.append('item', JSON.stringify(this.item));
            if (this.monto) {
                producto.append('monto', this.monto);
            }
            axios.post(url, producto, {headers: {'Content-Type': 'multipart/form-data'}})
                .then(({data}) => {
                    if (data["status"] === 0) {
                        this.$emit("close")
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
        saveVenta() {
            this.seved('/ordenVenta');
        },
        saveDeuda() {
            this.seved('/ordenDeuda');
        },
        getTotal(detalle) {
            let total = 0;
            if (detalle) {
                for (let value of detalle) {
                    if (value) {
                        total += value.costo * value.cantidad;
                    }
                }
                this.total = parseFloat(total).toFixed(2);
            }
            return parseFloat(total).toFixed(2);
        },
        getCambio() {
            return parseFloat(this.item.montoVenta - this.total).toFixed(2);
        },
        getSaldo() {
            return (this.total - this.item.montoVenta - this.monto);
        },
        getObservaciones(item) {
            if (item)
                return item.replace(/\n/g, "<br/>");
            return "";
        },
        resyncOrden() {
            if (this.item.montoVenta > this.total) {
                this.item.montoVenta = this.total;
            }
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
