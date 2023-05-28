<script setup>
import helper from "@/helpers";

const props = defineProps({
  model: Boolean,
  invoice_pos: Object,
});
</script>
<template>
  <!-- Modal Show Invoice POS-->
  <v-dialog :model-value="model" max-width="350">
    <v-card>
      <v-card-text>
        <div id="invoice-POS">
          <div class="info">
            <h2 class="text-center font-weight-bold">{{ invoice_pos.sale.Ref }}</h2>
            <h4 class="text-center font-weight-bold">{{ invoice_pos.setting?.CompanyName }}</h4>
            <v-col cols="12">
              <p>
                <span>Fecha : {{ invoice_pos.sale.date }} <br></span>
                <span v-if="invoice_pos.pos_settings.show_address">Direccion : {{
                    invoice_pos.setting.CompanyAdress ?? ''
                  }} <br></span>
                <span v-if="invoice_pos.pos_settings.show_email">Correo : {{ invoice_pos.setting?.email }} <br></span>
                <span v-if="invoice_pos.pos_settings.show_phone">Telefono : {{ invoice_pos.setting?.CompanyPhone }} <br></span>
                <span v-if="invoice_pos.pos_settings.show_customer">Cliente : {{ invoice_pos.sale.client_name }} <br></span>
                <span>Agencia : {{ invoice_pos.sale.warehouse }} <br></span>
              </p>
            </v-col>
          </div>
          <v-table density="compact" hover>
            <tbody>
            <tr v-for="detail_invoice in invoice_pos.details">
              <td colspan="3">
                {{ detail_invoice.name }}
                <br>
                <span>{{ helper.formatNumber(detail_invoice.quantity, 2) }} {{
                    detail_invoice.unit_sale
                  }} x {{ helper.formatNumber(detail_invoice.total / detail_invoice.quantity, 2) }}</span>
              </td>
              <td style="text-align:right;vertical-align:bottom">{{ helper.formatNumber(detail_invoice.total, 2) }}
              </td>
            </tr>

            <tr style="margin-top:10px" v-show="invoice_pos.pos_settings.show_discount">
              <td colspan="3" class="total">Impuesto</td>
              <td style="text-align:right;" class="total">{{ invoice_pos.symbol }}
                {{ helper.formatNumber(invoice_pos.sale.taxe, 2) }}
                ({{ helper.formatNumber(invoice_pos.sale.tax_rate, 2) }}
                %)
              </td>
            </tr>

            <tr style="margin-top:10px" v-show="invoice_pos.pos_settings.show_discount">
              <td colspan="3" class="total">Descuento</td>
              <td style="text-align:right;" class="total">{{ invoice_pos.symbol }}
                {{ helper.formatNumber(invoice_pos.sale.discount, 2) }}
              </td>
            </tr>

            <tr style="margin-top:10px">
              <td colspan="3" class="total">Total</td>
              <td style="text-align:right;" class="total">{{ invoice_pos.symbol }}
                {{ helper.formatNumber(invoice_pos.sale.GrandTotal, 2) }}
              </td>
            </tr>

            <tr v-show="invoice_pos.sale.paid_amount < invoice_pos.sale.GrandTotal">
              <td colspan="3" class="total">Pagado</td>
              <td
                  style="text-align:right;"
                  class="total"
              >{{ invoice_pos.symbol }} {{ helper.formatNumber(invoice_pos.sale.paid_amount, 2) }}
              </td>
            </tr>

            <tr v-show="invoice_pos.sale.paid_amount < invoice_pos.sale.GrandTotal">
              <td colspan="3" class="total">Deuda</td>
              <td
                  style="text-align:right;"
                  class="total"
              >{{ invoice_pos.symbol }}
                {{ parseFloat(invoice_pos.sale.GrandTotal - invoice_pos.sale.paid_amount).toFixed(2) }}
              </td>
            </tr>
            </tbody>
          </v-table>
          <v-table
              class="change mt-3"
              style=" font-size: 12px;"
              v-show="invoice_pos.sale.paid_amount > 0"
              density="compact"
              hover
          >
            <thead>
            <tr style="background: #eee; ">
              <th style="text-align: left;" colspan="1">Pagado Por:</th>
              <th style="text-align: center;" colspan="2">Monto:</th>
              <th style="text-align: right;" colspan="1">Saldo:</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="payment_pos in invoice_pos.payments">
              <td style="text-align: left;" colspan="1">{{
                  helper.getReglamentPayment(payment_pos.Reglement)[0].title
                }}
              </td>
              <td
                  style="text-align: center;"
                  colspan="2"
              >{{ helper.formatNumber(payment_pos.montant, 2) }}
              </td>
              <td
                  style="text-align: right;"
                  colspan="1"
              >{{ helper.formatNumber(payment_pos.change, 2) }}
              </td>
            </tr>
            </tbody>
          </v-table>
        </div>
      </v-card-text>
      <v-card-actions>
        <v-spacer></v-spacer>
        <v-btn prepend-icon="mdi-printer" @click="helper.print_pos('invoice-POS')" color="primary" variant="outlined">
          Imprimir
        </v-btn>
        <v-spacer></v-spacer>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>
