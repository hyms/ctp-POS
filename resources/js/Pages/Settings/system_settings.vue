<script setup>
import Layout from "@/Layouts/Authenticated.vue";
import { onMounted, ref } from "vue";
import { api, labelsNew, rules } from "@/helpers";
import Snackbar from "@/Components/snackbar.vue";

const props = defineProps({
    settings: Object,
    currencies: Object,
    clients: Object,
    warehouses: Object,
});

const loading = ref(false);
const snackbar = ref({
    view: false,
    color: "",
    text: "",
});
const form = ref(null);
const setting = ref({
    client_id: "",
    warehouse_id: "",
    // currency_id: "",
    email: "",
    logo: "",
    CompanyName: "",
    CompanyPhone: "",
    CompanyAdress: "",
    footer: "",
    // developed_by:"",
    // default_language:"",
    // sms_gateway:"",
    is_invoice_footer: "",
    invoice_footer: "",
});

//------------- Submit Validation Setting
async function Submit_Setting() {
    const validate = await form.value.validate();
    if (!validate) {
        snackbar.value.text = "Por favor llena correctamente el formulario";
        snackbar.value.color = "error";
        snackbar.value.view = true;
    } else {
        Update_Settings();
    }
}

//     //------------------------------ Event Upload Logo -------------------------------\\
//     async onFileSelected(e) {
//       const { valid } = await this.$refs.Logo.validate(e);
//
//       if (valid) {
//         this.setting.logo = e.target.files[0];
//       } else {
//         this.setting.logo = "";
//       }
//     },
//
//
//---------------------------------- Update Settings ----------------\\
function Update_Settings() {
    api.put({
        url: "/settings/" + setting.value.id,
        params: setting.value,
        loadingItem: loading,
        snackbar,
        onSuccess: (data) => {},
    });
}

//---------------------------------- Clear_Cache ----------------\\
function Clear_Cache() {
    api.get({
        url: "/clear_cache",
    });
}

onMounted(() => {
    setting.value = props.settings;
});
</script>
<template>
    <Layout>
        <snackbar
            :text="snackbar.text"
            v-model="snackbar.view"
            :color="snackbar.color"
        ></snackbar>

        <!-- System Settings -->
        <v-form @submit.prevent="Submit_Setting" ref="form">
            <v-row>
                <v-col cols="12">
                    <v-card>
                        <v-card-title
                            >{{ labelsNew.SystemSettings }}
                        </v-card-title>
                        <v-card-text>
                            <v-row>
                                <!-- Default Currency -->
                                <!--                      <v-col cols="12" lg="4" md="4">-->
                                <!--                          <v-select-->
                                <!--                              :label="labelsNew.DefaultCurrency"-->
                                <!--                            v-model="setting.currency_id"-->
                                <!--                            :reduce="label => label.value"-->
                                <!--                            :placeholder="$t('Choose_Currency')"-->
                                <!--                            :options="currencies.map(currencies => ({label: currencies.name, value: currencies.id}))"-->
                                <!--                          />-->
                                <!--                      </v-col>-->
                                <!---->
                                <!-- Email  -->
                                <v-col cols="12" lg="4" md="4">
                                    <v-text-field
                                        :label="
                                            labelsNew.DefaultEmail + ' ' + '*'
                                        "
                                        v-model="setting.email"
                                        hide-details="auto"
                                    ></v-text-field>
                                </v-col>
                                <!---->
                                <!-- - Logo -->
                                <!--                      <v-col cols="12" lg="4" md="4">-->
                                <!--                          <v-form-group-->
                                <!--                            slot-scope="{validate, valid, errors }"-->
                                <!--                            :label="$t('ChangeLogo')"-->
                                <!--                          >-->
                                <!--                            <input-->
                                <!--                              :state="errors[0] ? false : (valid ? true : null)"-->
                                <!--                              :class="{'is-invalid': !!errors.length}"-->
                                <!--                              @change="onFileSelected"-->
                                <!--                              label="Choose Logo"-->
                                <!--                              type="file"-->
                                <!--                            >-->
                                <!--                            <v-form-invalid-feedback id="Logo-feedback">{{ errors[0] }}</v-form-invalid-feedback>-->
                                <!--                          </v-form-group>-->
                                <!--                      </v-col>-->
                                <!---->
                                <!-- Company Name  -->
                                <v-col cols="12" lg="4" md="4">
                                    <v-text-field
                                        :label="
                                            labelsNew.CompanyName + ' ' + '*'
                                        "
                                        v-model="setting.CompanyName"
                                        :rules="rules.required"
                                        hide-details="auto"
                                    ></v-text-field>
                                </v-col>
                                <!---->
                                <!-- Company Phone  -->
                                <v-col cols="12" lg="4" md="4">
                                    <v-text-field
                                        :label="
                                            labelsNew.CompanyPhone + ' ' + '*'
                                        "
                                        v-model="setting.CompanyPhone"
                                        :rules="rules.required"
                                        hide-details="auto"
                                    ></v-text-field>
                                </v-col>
                                <!---->
                                <!-- developed by -->
                                <!--                                <v-col cols="12" lg="4" md="4">-->
                                <!--                        <v-text-field-->
                                <!--                            :label="labelsNew.developed_by + ' ' + '*'"-->
                                <!--                            v-model="setting.developed_by"-->
                                <!--                            :rules="rules.required"-->
                                <!--                        ></v-text-field>-->
                                <!--                                </v-col>-->

                                <!-- Footer -->
                                <!--                                <v-col cols="12" lg="4" md="4">-->
                                <!--                                    <v-text-field-->
                                <!--                                        :label="labelsNew.footer + ' ' + '*'"-->
                                <!--                                        v-model="setting.footer"-->
                                <!--                                        :rules="rules.required"-->
                                <!--                                        hide-details="auto"-->
                                <!--                                    ></v-text-field>-->
                                <!--                                </v-col>-->

                                <!-- Default Language -->
                                <!--                      <v-col cols="12" lg="4" md="4">-->
                                <!--                        <validation-provider name="DefaultLanguage" :rules="{ required: true}">-->
                                <!--                        <v-form-group slot-scope="{ valid, errors }" :label="$t('DefaultLanguage') + ' ' + '*'">-->
                                <!--                          <v-select-->
                                <!--                            :class="{'is-invalid': !!errors.length}"-->
                                <!--                            :state="errors[0] ? false : (valid ? true : null)"-->
                                <!--                            v-model="setting.default_language"-->
                                <!--                            :reduce="label => label.value"-->
                                <!--                            :placeholder="$t('DefaultLanguage')"-->
                                <!--                                  :options="-->
                                <!--                                  [-->
                                <!--                                  {label: 'English', value: 'en'},-->
                                <!--                                  {label: 'French', value: 'fr'},-->
                                <!--                                  {label: 'Arabic', value: 'ar'},-->
                                <!--                                  {label: 'Turkish', value: 'tur'},-->
                                <!--                                  {label: 'Simplified Chinese', value: 'sm_ch'},-->
                                <!--                                  {label: 'Thaï', value: 'thai'},-->
                                <!--                                  {label: 'Hindi', value: 'hn'},-->
                                <!--                                  {label: 'German', value: 'de'},-->
                                <!--                                  {label: 'Spanish', value: 'es'},-->
                                <!--                                  {label: 'Italien', value: 'it'},-->
                                <!--                                  {label: 'Indonesian', value: 'Ind'},-->
                                <!--                                  {label: 'Traditional Chinese', value: 'tr_ch'},-->
                                <!--                                  {label: 'Russian', value: 'ru'},-->
                                <!--                                  {label: 'Vietnamese', value: 'vn'},-->
                                <!--                                  {label: 'Korean', value: 'kr'},-->
                                <!--                                  {label: 'Bangla', value: 'ba'},-->
                                <!--                                  {label: 'Portuguese', value: 'br'},-->
                                <!--                              ]"                     -->
                                <!--                      ></v-select>-->
                                <!--                        <v-form-invalid-feedback>{{ errors[0] }}</v-form-invalid-feedback>-->
                                <!--                         </v-form-group>-->
                                <!--                    </validation-provider>-->
                                <!--                  </v-col>-->

                                <!-- Default Customer -->
                                <!--                                <v-col cols="12" lg="4" md="4">-->
                                <!--                                    <v-autocomplete-->
                                <!--                                        :label="labelsNew.DefaultCustomer"-->
                                <!--                                        v-model="setting.client_id"-->
                                <!--                                        :items="helpers.toArraySelect(clients)"-->
                                <!--                                        hide-details="auto"-->
                                <!--                                    />-->
                                <!--                                </v-col>-->

                                <!-- Default Warehouse -->
                                <!--                                <v-col cols="12" lg="4" md="4">-->
                                <!--                                    <v-select-->
                                <!--                                        :label="labelsNew.DefaultWarehouse"-->
                                <!--                                        v-model="setting.client_id"-->
                                <!--                                        :items="-->
                                <!--                                            helpers.toArraySelect(warehouses)-->
                                <!--                                        "-->
                                <!--                                        hide-details="auto"-->
                                <!--                                    />-->
                                <!--                                </v-col>-->

                                <!--                   &lt;!&ndash; Default SMS Gateway &ndash;&gt;-->
                                <!--                  <v-col cols="12" lg="4" md="4">-->
                                <!--                    <v-form-group :label="$t('Default_SMS_Gateway')">-->
                                <!--                      <v-select-->
                                <!--                        v-model="setting.sms_gateway"-->
                                <!--                        :reduce="label => label.value"-->
                                <!--                        :placeholder="$t('Choose_SMS_Gateway')"-->
                                <!--                        :options="sms_gateway.map(sms_gateway => ({label: sms_gateway.title, value: sms_gateway.id}))"-->
                                <!--                      />-->
                                <!--                    </v-form-group>-->
                                <!--                  </v-col>-->

                                <!--                   &lt;!&ndash; Time_Zone &ndash;&gt;-->
                                <!--                  <v-col cols="12" lg="4" md="4">-->
                                <!--                    <v-form-group :label="$t('Time_Zone')">-->
                                <!--                     <v-select @input="Selected_Time_Zone"-->
                                <!--                          :placeholder="$t('Time_Zone')"-->
                                <!--                          v-model="setting.timezone" :reduce="label => label.value"-->
                                <!--                          :options="zones_array.map(zones_array => ({label: zones_array.label, value: zones_array.zone}))">-->
                                <!--                      </v-select>-->
                                <!--                    </v-form-group>-->
                                <!--                  </v-col>-->

                                <!-- Company Adress -->
                                <v-col cols="12">
                                    <v-textarea
                                        :label="labelsNew.Adress + ' ' + '*'"
                                        :rules="rules.required"
                                        v-model="setting.CompanyAdress"
                                    ></v-textarea>
                                </v-col>

                                <!--                                <v-col cols="12" md="2" sm="2">-->
                                <!--                                    <v-checkbox-->
                                <!--                                        v-model="setting.is_invoice_footer"-->
                                <!--                                        :label="labelsNew.invoice_footer"-->
                                <!--                                    ></v-checkbox>-->
                                <!--                                </v-col>-->

                                <!--                                <v-col-->
                                <!--                                    cols="12"-->
                                <!--                                    md="6"-->
                                <!--                                    sm="6"-->
                                <!--                                    v-if="setting.is_invoice_footer"-->
                                <!--                                >-->
                                <!--                                    <v-textarea-->
                                <!--                                        :label="-->
                                <!--                                            labelsNew.invoice_footer + ' ' + '*'-->
                                <!--                                        "-->
                                <!--                                        :rules="rules.required"-->
                                <!--                                        v-model="setting.invoice_footer"-->
                                <!--                                    ></v-textarea>-->
                                <!--                                </v-col>-->

                                <v-col cols="12">
                                    <v-btn
                                        color="primary"
                                        variant="flat"
                                        type="submit"
                                        :text="labelsNew.submit"
                                    ></v-btn>
                                </v-col>
                            </v-row>
                        </v-card-text>
                    </v-card>
                </v-col>
            </v-row>
        </v-form>

        <!--    &lt;!&ndash; Clear Cache &ndash;&gt;-->
        <!--      <v-form @submit.prevent="Clear_Cache" v-if="!isLoading">-->
        <!--        <v-row class="mt-5">-->
        <!--          <v-col lg="12" md="12" sm="12">-->
        <!--            <v-card no-body :header="$t('Clear_Cache')">-->
        <!--              <v-card-body>-->
        <!--                <v-row>-->

        <!--                  <v-col md="12">-->
        <!--                    <v-form-group>-->
        <!--                      <v-button variant="primary" @click="Clear_Cache()" >{{$t('Clear_Cache')}}</v-button>-->
        <!--                    </v-form-group>-->
        <!--                  </v-col>-->
        <!--                </v-row>-->
        <!--              </v-card-body>-->
        <!--            </v-card>-->
        <!--          </v-col>-->
        <!--        </v-row>-->
        <!--      </v-form>-->
        <!--  </div>-->
    </Layout>
</template>
