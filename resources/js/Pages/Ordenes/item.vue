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
            </tr>
            </thead>
            <tbody>
            <tr v-for="(itemOrden,key) in item.detallesOrden">
                <td>{{ key + 1 }}</td>
                <td>{{ getProduct(itemOrden.stock) }}</td>
                <td>{{ itemOrden.cantidad }}</td>
            </tr>
            </tbody>
        </table>
        <div>
            <p><strong>Observaciones:</strong><br>
                {{ item.observaciones }}
            </p>
        </div>

        <template #modal-footer="{ ok, cancel }">
            <!-- Emulate built in modal footer ok and cancel button actions -->
            <b-button size="sm" variant="primary" @click="ok()">
                OK
            </b-button>
<!--            <b-button size="sm" variant="danger" @click="imprimirPos()">-->
<!--                Imprimir-->
<!--            </b-button>-->
            <a
                class="btn btn-dark btn-sm"
                :href="'/ordenPdf/'+item.id"
                target="_blank"
            >Imprimir</a>
        </template>
    </b-modal>
</template>

<script>
export default {
    props: {
        productos: Array,
        item: Object,
        id: String
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
        imprimirPos() {

        },

    },
    components: {
    }
}
</script>
