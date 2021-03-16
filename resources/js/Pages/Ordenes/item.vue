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

        <vue-html2pdf
            :show-layout="false"
            :float-layout="true"
            :enable-download="false"
            :preview-modal="true"
            :paginate-elements-by-height="1400"
            filename="hee hee"
            :pdf-quality="2"
            :manual-pagination="false"
            pdf-format="a4"
            pdf-orientation="landscape"
            pdf-content-width="800px"

            ref="html2Pdf"
        >
            <item-pdf slot="pdf-content" :item="item" :productos="productos" :id="id"/>
        </vue-html2pdf>

        <template #modal-footer="{ ok, cancel }">
            <!-- Emulate built in modal footer ok and cancel button actions -->
            <b-button size="sm" variant="primary" @click="ok()">
                OK
            </b-button>
            <b-button size="sm" variant="danger" @click="generateReport()">
                Imprimir
            </b-button>
        </template>
    </b-modal>
</template>

<script>
import itemPdf from './pdf';
import VueHtml2pdf from 'vue-html2pdf'

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
        generateReport() {
            this.$refs.html2Pdf.generatePdf()
        }
    },
    components: {
        VueHtml2pdf,
        itemPdf
    }
}
</script>
