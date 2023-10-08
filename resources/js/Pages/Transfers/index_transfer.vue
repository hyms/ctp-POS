<script setup>
import {computed, ref} from "vue";
import Layout from "@/Layouts/Authenticated.vue";
import ExportBtn from "@/Components/buttons/ExportBtn.vue";
import DeleteDialog from "@/Components/buttons/DeleteDialog.vue";
import {router, usePage} from "@inertiajs/vue3";
import helper from "@/helpers";
import labels from "@/labels";

const currency = computed(() => usePage().props.currency);

const props = defineProps({
  warehouses: Array,
  transfers: Array,
  errors: Object,
});
const search = ref("");
const loading = ref(false);
const snackbar = ref(false);
const snackbarText = ref("");
const snackbarColor = ref("info");
const dialogDelete = ref(false);
const dialogDetail = ref(false);
const details = ref([]);
const transfer = ref({GrandTotal: ""});

const fields = ref([
  {title: labels.transfer.date, key: "date"},
  {title: labels.transfer.Ref, key: "Ref"},
  {title: labels.transfer.from_warehouse, key: "from_warehouse"},
  {title: labels.transfer.to_warehouse, key: "to_warehouse"},
  {title: labels.transfer.items, key: "items"},
  {title: labels.transfer.GrandTotal, key: "GrandTotal"},
  {title: labels.transfer.statut, key: "statut"},
  {title: labels.actions, key: "actions"},
]);
const jsonFields = ref({
  Fecha: "date",
  Codigo: "Ref",
  de_Agencia: "from_warehouse",
  a_Agencia: "to_warehouse",
  Items: "items",
  Total: "GrandTotal",
  Estado: "statut",
});

// //------ Reset Filter
// function Reset_Filter() {
//   this.search = "";
//   this.Filter_date = "";
//   this.Filter_status = "";
//   this.Filter_Ref = "";
//   this.Filter_From = "";
//   this.Filter_To = "";
//   this.Get_Transfers(this.serverParams.page);
// },

//----------------------------------- Get Details Transfer ------------------------------\\
function showDetails(id) {
  loading.value = true;
  dialogDetail.value = false;
  axios
      .get("/transfer/" + id)
      .then(response => {
        transfer.value = response.data.transfer;
        details.value = response.data.details;
        console.log(transfer.value)
        console.log(details.value)
        dialogDetail.value = true;
      })
      .catch(response => {
        dialogDetail.value = true;
      })
      .finally(() => {
        loading.value = false;
      });
}

//-------------------------------- Reset Form -------------------------------\\
function reset_Form() {
  transfer.value = {id: ""};
}

//---------------------------------- Delete Transfer ----------------------\\
function onCloseDelete() {
  reset_Form();
  dialogDelete.value = false;
}

function Delete_Item(item) {
  reset_Form();
  transfer.value = item;
  dialogDelete.value = true;
}

function Remove_Transfer(id) {
  snackbar.value = false;
  axios
      .delete("/transfers/" + transfer.value.id)
      .then(() => {
        snackbar.value = true;
        snackbarColor.value = "success";
        snackbarText.value = "Borrado exitoso";
        router.reload({
          preserveState: true,
          preserveScroll: true,
        });
        dialogDelete.value = false;
      })
      .catch((error) => {
        console.log(error);
        snackbar.value = true;
        snackbarColor.value = "error";
        snackbarText.value = error.response.data.message;
      })
      .finally(() => {
        setTimeout(() => {
          loading.value = false;
        }, 1000);
      });
}
</script>
<template>
  <layout :loading="loading"
          :snackbar-view="snackbar"
          :snackbar-text="snackbarText"
          :snackbar-color="snackbarColor">
    <!-- Modal Remove Adjustment -->
    <delete-dialog
        :model="dialogDelete"
        :on-save="Remove_Transfer"
        :on-close="onCloseDelete"
    ></delete-dialog>
    <!-- Show details -->
    <v-dialog v-model="dialogDetail" max-width="800px">
      <v-card>
        <v-toolbar
            title="Detalle de Transferencia"
            border
        ></v-toolbar>
        <v-card-text>
          <v-row>
            <v-col lg="5" cols="12" class="mt-2">
              <v-table hover>
                <tbody>
                <!-- date -->
                <tr>
                  <td>{{ labels.transfer.date }}</td>
                  <th>{{ transfer.date }}</th>
                </tr>
                <!-- Reference -->
                <tr>
                  <td>{{ labels.transfer.Ref }}</td>
                  <th>{{ transfer.Ref }}</th>
                </tr>
                <!-- From warehouse -->
                <tr>
                  <td>{{ labels.transfer.from_warehouse }}</td>
                  <th>{{ transfer.from_warehouse }}</th>
                </tr>
                <!-- To warehouse -->
                <tr>
                  <td>{{ labels.transfer.to_warehouse }}</td>
                  <th>{{ transfer.to_warehouse }}</th>
                </tr>
                <!-- Grand Total -->
                <tr>
                  <td>{{ labels.transfer.GrandTotal }}</td>
                  <th>{{ currency }}{{ helper.formatNumber(transfer.GrandTotal, 2) }}</th>
                </tr>
                <!-- Status -->
                <tr>
                  <td>{{ labels.transfer.statut }}</td>
                  <th>
                    <v-chip
                        :color="helper.statutTransferColor(transfer.statut)"
                        variant="tonal"
                        size="x-small"
                    >{{ helper.statutTransfer(transfer.statut) }}
                    </v-chip>
                  </th>
                </tr>
                </tbody>
              </v-table>
            </v-col>
            <v-col lg="7" cols="12" sm="12" class="mt-2">
              <v-table border>
                <thead>
                <tr>
                  <th scope="col">{{ labels.transfer_detail.product }}</th>
                  <th scope="col">{{ labels.transfer_detail.code }}</th>
                  <th scope="col">{{ labels.transfer_detail.qty }}</th>
                  <th scope="col">{{ labels.transfer_detail.sub_total }}</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="detail in details">
                  <td>{{ detail.name }}</td>
                  <td>{{ detail.code }}</td>
                  <td>{{ helper.formatNumber(detail.quantity, 2) }} {{ detail.unit }}</td>
                  <td>{{ currency }} {{ detail.total.toFixed(2) }}</td>
                </tr>
                </tbody>
              </v-table>
            </v-col>
          </v-row>
          <hr v-show="transfer.note">
          <v-row class="mt-3">
            <v-col cols="12">
              <p>{{ transfer.note }}</p>
            </v-col>
          </v-row>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn
              variant="elevated"
              color="primary"
              class="ma-1"
              @click="dialogDetail = false"
          >Aceptar
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
    <v-row align="center">
      <v-col>
        <v-text-field
            v-model="search"
            prepend-icon="mdi-magnify"
            hide-details
            label="Buscar"
            single-line
            variant="underlined"
        ></v-text-field>
      </v-col>
      <v-spacer></v-spacer>
      <v-col cols="auto" class="text-right">
        <ExportBtn
            :data="transfers"
            :fields="jsonFields"
            name-file="Transferencias"
        ></ExportBtn>
        <v-btn
            color="primary"
            class="ma-1"
            prepend-icon="mdi-account-plus"
            @click="router.visit('/transfers/create')"
        >
          Añadir
        </v-btn>
      </v-col>
    </v-row>
    <v-row>
      <v-col>
        <v-data-table
            :headers="fields"
            :items="transfers"
            :search="search"
            hover
            class="elevation-2"
            no-data-text="No existen datos a mostrar"
        >
          <template v-slot:item.statut="{ item }">
            <v-chip
                :color="helper.statutTransferColor(item.statut)"
                variant="tonal"
                size="x-small"
            >{{ helper.statutTransfer(item.statut) }}
            </v-chip>
          </template>
          <template v-slot:item.actions="{ item }">
            <v-btn
                class="ma-1"
                color="info"
                icon="mdi-eye"
                size="x-small"
                variant="outlined"
                @click="showDetails(item.id)"
            >
            </v-btn>
            <v-btn
                class="ma-1"
                color="primary"
                icon="mdi-pencil"
                size="x-small"
                variant="outlined"
                :disabled="helper.enableDay(item.updated_at)"
                @click="router.visit('/transfers/edit/' + item.id)"
            >
            </v-btn>
            <v-btn
                class="ma-1"
                color="error"
                icon="mdi-delete"
                size="x-small"
                variant="outlined"
                :disabled="helper.enableDay(item.updated_at)"
                @click="Delete_Item(item)"
            >
            </v-btn>
          </template>
        </v-data-table>
      </v-col>
    </v-row>

    <!--    &lt;!&ndash; multiple filters &ndash;&gt;-->
    <!--    <v-sidebar id="sidebar-right" :title="$t('Filter')" bg-variant="white" right shadow>-->
    <!--      <div class="px-3 py-2">-->
    <!--        <v-row>-->
    <!--          &lt;!&ndash; Reference  &ndash;&gt;-->
    <!--          <v-col md="12">-->
    <!--            <v-form-group :label="$t('Reference')">-->
    <!--              <v-form-input label="Reference" :placeholder="$t('Reference')" v-model="Filter_Ref"></v-form-input>-->
    <!--            </v-form-group>-->
    <!--          </v-col>-->

    <!--          &lt;!&ndash; From warehouse  &ndash;&gt;-->
    <!--          <v-col md="12">-->
    <!--            <v-form-group :label="$t('FromWarehouse')">-->
    <!--              <v-select-->
    <!--                :reduce="label => label.value"-->
    <!--                :placeholder="$t('Choose_Warehouse')"-->
    <!--                v-model="Filter_From"-->
    <!--                :options="warehouses.map(warehouses => ({label: warehouses.name, value: warehouses.id}))"-->
    <!--              />-->
    <!--            </v-form-group>-->
    <!--          </v-col>-->

    <!--          &lt;!&ndash; To warehouse  &ndash;&gt;-->
    <!--          <v-col md="12">-->
    <!--            <v-form-group :label="$t('ToWarehouse')">-->
    <!--              <v-select-->
    <!--                :reduce="label => label.value"-->
    <!--                :placeholder="$t('Choose_Warehouse')"-->
    <!--                v-model="Filter_To"-->
    <!--                :options="warehouses.map(warehouses => ({label: warehouses.name, value: warehouses.id}))"-->
    <!--              />-->
    <!--            </v-form-group>-->
    <!--          </v-col>-->

    <!--          &lt;!&ndash; Status  &ndash;&gt;-->
    <!--          <v-col md="12">-->
    <!--            <v-form-group :label="$t('Status')">-->
    <!--              <v-select-->
    <!--                v-model="Filter_status"-->
    <!--                :reduce="label => label.value"-->
    <!--                :placeholder="$t('Choose_Status')"-->
    <!--                :options="-->
    <!--                      [-->
    <!--                        {label: 'Completed', value: 'completed'},-->
    <!--                        {label: 'Sent', value: 'sent'},-->
    <!--                        {label: 'Pending', value: 'pending'},-->
    <!--                      ]"-->
    <!--              ></v-select>-->
    <!--            </v-form-group>-->
    <!--          </v-col>-->

  </layout>
</template>