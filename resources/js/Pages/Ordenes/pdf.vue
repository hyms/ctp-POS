<template>
    <section slot="pdf-content">
        <div class="row" style="color: #000000">
<!--            <div style="width:593px; position: relative; float: left;">-->
                <div class="col">
                <div class="col">
                    <div class="row">
                        <div class="col-8">Correlativo</div>
                        <div class="col-4"><h4 class="text-right">{{ item.correlativo }}</h4></div>
                    </div>

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
            </div>
<!--            </div>-->
            </div>
        </div>
    </section>
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

    },
    components: {}
}

</script>
