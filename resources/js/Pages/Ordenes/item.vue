<template>
    <b-modal
        :id="id"
        title="Comprobante">
        <h4 class="text-right">{{ item.correlativo }}</h4>
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
                <td v-if="isVenta">{{ itemOrden.costo }}</td>
                <td v-if="isVenta">{{ itemOrden.total }}</td>
            </tr>
            </tbody>
        </table>
        <div>
            <p><strong>Observaciones:</strong><br>
                {{ item.observaciones }}
            </p>
        </div>
<!--        <div>
sector de form venta
</div>-->
        <template #modal-footer="{ ok, cancel }">
            <!-- Emulate built in modal footer ok and cancel button actions -->
            <b-button size="sm" variant="primary" @click="ok()">
                Cerrar
            </b-button>
<!--            <b-button size="sm" variant="danger" @click="imprimirPos()">-->
<!--                Imprimir-->
<!--            </b-button>-->
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
    props: {
        productos: Array,
        item: Object,
        id: String,
        isVenta:Boolean
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
            let producto = new FormData();
            producto.append('id', this.item.id);
            axios.post('/venta/orden', producto, {headers: {'Content-Type': 'multipart/form-data'}})
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

    },
    components: {
    }
}
</script>
