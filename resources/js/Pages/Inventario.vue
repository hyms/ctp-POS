<template>
    <v-row>
        <v-col>
            <v-dialog max-width="400px" scrollable>
                <template v-slot:activator="{ on, attrs }">
                    <v-btn
                        color="secondary"
                        elevation="1"
                        class="mb-3"
                        v-bind="attrs"
                        v-on="on"
                    >
                        Saldos
                        <v-icon right> mdi-clipboard-list-outline</v-icon>
                    </v-btn>
                </template>
                <v-card>
                    <v-card-title>
                        Ingresos
                    </v-card-title>

                    <v-card-text>
                        <v-simple-table>
                            <template v-slot:default>
                                <thead>
                                <tr>
                                    <th class="text-left">
                                        Producto
                                    </th>
                                    <th class="text-left">
                                        Cantidad
                                    </th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr
                                    v-for="item in productos"
                                    :key="item.name"
                                >
                                    <td>{{ item.formato }} ({{ item.dimension }})</td>
                                    <td>{{ item.cantidad }}</td>
                                </tr>
                                </tbody>
                            </template>
                        </v-simple-table>
                    </v-card-text>
                </v-card>
            </v-dialog>
            <v-row>
                <v-col>
                    <v-card>
                        <v-card-title>
                            Ingresos
                        </v-card-title>

                    </v-card>
                </v-col>
                <v-col>
                    <v-card>
                        <v-card-title>
                            Egresos
                        </v-card-title>

                    </v-card>
                </v-col>
            </v-row>
        </v-col>
    </v-row>
</template>

<script>
import Authenticated from "@/Layouts/Authenticated.vue";

export default {
    layout: Authenticated,
    props: {
        productos: Array,
        productosSelect: Object,
        movimientos: Array,
        fields: Array,
        stocks: Array,
        active: Number,
        report: Array,
        errors: Object,
    },
}
</script>
