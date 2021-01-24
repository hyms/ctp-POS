<template>
    <table class="table table-striped table-hover">
        <thead>
        <tr>
            <th scope="col">Productos</th>
            <th scope="col">Cantidad</th>
            <th scope="col">Acciones</th>
        </tr>
        </thead>
        <tbody>
        <template v-for="(value,key) in productos">
        <tr>
            <td>{{value.formato}}({{value.dimension}})</td>
            <td>Mark</td>
            <td>Otto</td>
        </tr>
        </template>
        </tbody>
    </table>
</template>

<script>

export default {
    props: {
        productos: Array,
        sucursal: Array,
        sucursalPadre: Array,
    },
    components: {
    },
    data() {
        return {
            itemRow: {}
        }
    },
    methods: {
        loadModal(isNew = true, item = null) {
            this.isNew = isNew;
            this.itemRow = {};
            if (!isNew) {
                this.itemRow = item.item;
            }
        },
    }
}
</script>

