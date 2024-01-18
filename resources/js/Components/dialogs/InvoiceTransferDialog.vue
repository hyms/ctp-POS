<script setup>
import { helpers, labels } from "@/helpers";

const props = defineProps({
    modelValue: Boolean,
    invoice_pos: Object,
    loading: { type: Boolean, default: false },
});
const emit = defineEmits(["update:modelValue"]);

function updateValue(value) {
    emit("update:modelValue", value);
}
</script>
<template>
    <!-- Modal Show Invoice POS-->
    <v-dialog
        :model-value="modelValue"
        max-width="350"
        @update:modelValue="updateValue"
    >
        <v-card :loading="loading">
            <v-card-text>
                <div id="invoice-POS">
                    <div class="info">
                        <h2 class="text-center font-weight-bold">
                            {{ invoice_pos.transfer.Ref }}
                        </h2>
                        <h4 class="text-center font-weight-bold">
                            {{ invoice_pos.setting?.CompanyName }}
                        </h4>
                        <v-col cols="12">
                            <p>
                                <span
                                    >Fecha : {{ invoice_pos.transfer.date }}
                                    <br
                                /></span>
                                <span
                                    >Usuario :
                                    <span class="font-weight-bold total">{{
                                        invoice_pos.transfer.user_name
                                    }}</span>
                                    <br
                                /></span>
                                <span
                                    >De :
                                    {{ invoice_pos.transfer.from_warehouse }}
                                    <br
                                /></span>
                                <span
                                    >A :
                                    {{ invoice_pos.transfer.to_warehouse }}
                                    <br
                                /></span>
                            </p>
                        </v-col>
                    </div>
                    <v-table hover>
                        <tbody>
                            <tr v-for="detail_invoice in invoice_pos.details">
                                <td>
                                    <span class="font-weight-bold total">{{
                                        detail_invoice.name
                                    }}</span>
                                </td>
                                <td
                                    style="
                                        text-align: right;
                                        vertical-align: bottom;
                                    "
                                >
                                    <span>
                                        {{
                                            helpers.formatNumber(
                                                detail_invoice.quantity,
                                                0
                                            )
                                        }}
                                        {{ detail_invoice.unit_sale }}
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </v-table>
                    <table class="change mt-2" style="font-size: 12px">
                        <thead>
                            <tr class="total">
                                <th>Notas</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <div
                                        style="text-wrap: pretty"
                                        v-html="
                                            invoice_pos.transfer.notes.replace(
                                                /(?:\r\n|\r|\n)/g,
                                                '<br/>'
                                            )
                                        "
                                    ></div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </v-card-text>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn
                    prepend-icon="fas fa-file-pdf"
                    @click="
                        helpers.print_pdf(
                            'invoice-POS',
                            invoice_pos.transfer.ref
                        )
                    "
                    color="primary"
                    variant="outlined"
                >
                    {{ labels.print }} PDF
                </v-btn>
                <v-btn
                    prepend-icon="fas fa-print"
                    @click="helpers.print_pos('invoice-POS')"
                    color="primary"
                    variant="outlined"
                >
                    {{ labels.print }}
                </v-btn>
                <v-spacer></v-spacer>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>
