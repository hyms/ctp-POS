<script setup>
import { globals, helpers, labels } from "@/helpers";

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
                            {{ invoice_pos.sale.Ref }}
                        </h2>
                        <h4 class="text-center font-weight-bold">
                            {{ invoice_pos.setting?.CompanyName }}
                        </h4>
                        <v-col cols="12">
                            <p>
                                <span
                                    >Fecha : {{ invoice_pos.sale.date }} <br
                                /></span>
                                <span
                                    v-if="invoice_pos.pos_settings.show_address"
                                    >Direccion :
                                    {{
                                        invoice_pos.setting.CompanyAdress ?? ""
                                    }}
                                    <br
                                /></span>
                                <span v-if="invoice_pos.pos_settings.show_email"
                                    >Correo : {{ invoice_pos.setting?.email }}
                                    <br
                                /></span>
                                <span v-if="invoice_pos.pos_settings.show_phone"
                                    >Telefono :
                                    {{ invoice_pos.setting?.CompanyPhone }} <br
                                /></span>
                                <span
                                    v-if="
                                        invoice_pos.pos_settings.show_customer
                                    "
                                    >Cliente :
                                    <span class="font-weight-bold total">{{
                                        invoice_pos.sale.client_name
                                    }}</span>
                                    <br
                                /></span>
                                <span
                                    >Agencia : {{ invoice_pos.sale.warehouse }}
                                    <br
                                /></span>
                            </p>
                        </v-col>
                    </div>
                    <v-table hover>
                        <tbody>
                            <template
                                v-for="detail_invoice in invoice_pos.details"
                            >
                                <tr
                                    v-if="
                                        globals.userPermision(['invoice_price'])
                                    "
                                >
                                    <td colspan="3">
                                        <span class="font-weight-bold total">{{
                                            detail_invoice.name
                                        }}</span>
                                        <br />
                                        <span>
                                            {{
                                                helpers.formatNumber(
                                                    detail_invoice.quantity,
                                                    0
                                                )
                                            }}
                                            {{ detail_invoice.unit_sale }} x
                                            {{
                                                helpers.formatNumber(
                                                    detail_invoice.total /
                                                        detail_invoice.quantity,
                                                    2
                                                )
                                            }}
                                        </span>
                                    </td>
                                    <td
                                        style="
                                            text-align: right;
                                            vertical-align: bottom;
                                        "
                                    >
                                        {{
                                            helpers.formatNumber(
                                                detail_invoice.total,
                                                2
                                            )
                                        }}
                                    </td>
                                </tr>
                                <tr v-else>
                                    <td colspan="3">
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
                                        </span>
                                        {{ detail_invoice.unit_sale }}
                                    </td>
                                </tr>
                            </template>
                            <tr
                                style="margin-top: 10px"
                                v-if="
                                    invoice_pos.pos_settings.show_discount &&
                                    globals.userPermision(['invoice_price'])
                                "
                            >
                                <td colspan="3" class="total">Impuesto</td>
                                <td style="text-align: right" class="total">
                                    {{ invoice_pos.symbol }}
                                    {{
                                        helpers.formatNumber(
                                            invoice_pos.sale.taxe,
                                            2
                                        )
                                    }}
                                    ({{
                                        helpers.formatNumber(
                                            invoice_pos.sale.tax_rate,
                                            2
                                        )
                                    }}%)
                                </td>
                            </tr>

                            <tr
                                style="margin-top: 10px"
                                v-if="invoice_pos.pos_settings.show_discount"
                            >
                                <td colspan="3" class="total">Descuento</td>
                                <td style="text-align: right" class="total">
                                    {{ invoice_pos.symbol }}
                                    {{
                                        helpers.formatNumber(
                                            invoice_pos.sale.discount,
                                            2
                                        )
                                    }}
                                </td>
                            </tr>

                            <tr
                                style="margin-top: 10px"
                                v-if="globals.userPermision(['invoice_price'])"
                            >
                                <td colspan="3" class="total">Total</td>
                                <td style="text-align: right" class="total">
                                    {{ invoice_pos.symbol }}
                                    {{
                                        helpers.formatNumber(
                                            invoice_pos.sale.GrandTotal,
                                            2
                                        )
                                    }}
                                </td>
                            </tr>

                            <tr
                                v-if="
                                    invoice_pos.sale.paid_amount <
                                        invoice_pos.sale.GrandTotal &&
                                    globals.userPermision(['invoice_price'])
                                "
                            >
                                <td colspan="3" class="total">Pagado</td>
                                <td style="text-align: right" class="total">
                                    {{ invoice_pos.symbol }}
                                    {{
                                        helpers.formatNumber(
                                            invoice_pos.sale.paid_amount,
                                            2
                                        )
                                    }}
                                </td>
                            </tr>

                            <tr
                                v-if="
                                    invoice_pos.sale.paid_amount <
                                        invoice_pos.sale.GrandTotal &&
                                    globals.userPermision(['invoice_price'])
                                "
                            >
                                <td colspan="3" class="total">Deuda</td>
                                <td style="text-align: right" class="total">
                                    {{ invoice_pos.symbol }}
                                    {{
                                        parseFloat(
                                            invoice_pos.sale.GrandTotal -
                                                invoice_pos.sale.paid_amount
                                        ).toFixed(2)
                                    }}
                                </td>
                            </tr>
                        </tbody>
                    </v-table>
                    <v-table class="change mt-2" style="font-size: 12px">
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
                                            invoice_pos.sale.notes.replace(
                                                /(?:\r\n|\r|\n)/g,
                                                '<br/>'
                                            )
                                        "
                                    ></div>
                                </td>
                            </tr>
                        </tbody>
                    </v-table>
                </div>
            </v-card-text>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn
                    prepend-icon="fas fa-file-pdf"
                    @click="
                        helpers.print_pdf('invoice-POS', invoice_pos.sale.ref)
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
