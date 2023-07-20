<script setup>
import {ref} from "vue";
import Layout from "@/Layouts/Authenticated.vue";
import {router} from "@inertiajs/vue3";
import helper from "@/helpers";
import labels from "@/labels";
import Snackbar from "@/Components/snackbar.vue";

const props = defineProps({
  user: Object,
  errors: Object,
});

const form = ref(null);
const username = ref("");
const loading = ref(false);
const snackbar = ref(false);
const snackbarText = ref("");
const snackbarColor = ref("info");


//------------- Submit Update Profile
async function Submit_Profile() {
  const validate = await form.value.validate();
  if (validate.valid) Update_Profile();
}

//------------------ Update Profile ----------------------\\
function Update_Profile() {
  loading.value = true;
  snackbar.value = false;

  axios
      .put("/update_user_profile/" + props.user.id, {
        firstname: props.user.firstname,
        lastname: props.user.lastname,
        email: props.user.email,
        NewPassword: props.user.NewPassword,
        phone: props.user.phone,
      })
      .then(({data}) => {
        snackbarColor.value = "success";
        snackbarText.value = labels.success_message;
        router.reload({
          preserveState: true,
          preserveScroll: true,
        });

      })
      .catch((error) => {
        console.log(error);
        snackbarColor.value = "error";
        snackbarText.value = error.response.data.message;
      })
      .finally(() => {
        setTimeout(() => {
          loading.value = false;
          snackbar.value = true;
        }, 1000);
      });
}
</script>
<template>
  <Layout>
    <snackbar
        v-model="snackbar"
        :snackbarColor="snackbarColor"
        :snackbarText="snackbarText"
    >
    </snackbar>

    <v-card :loading="loading">
      <v-form @submit.prevent="Submit_Profile" ref="form">
        <v-toolbar height="10"></v-toolbar>
        <v-card-text v-if="loading">
          <v-skeleton-loader type="paragraph"></v-skeleton-loader>
        </v-card-text>
        <v-card-text v-else>
          <!--  Profile -->
          <v-row>
            <!-- First name -->
            <v-col md="6" cols="12">
              <v-text-field
                  :label="labels.user.firstname + ' *'"
                  v-model="user.firstname"
                  :placeholder="labels.user.firstname"
                  :rules="helper.required
                                        .concat(helper.max(20))
                                        .concat(helper.min(4))"
                  hide-details="auto"
              >
              </v-text-field>
            </v-col>

            <!-- Last name -->
            <v-col md="6" cols="12">
              <v-text-field
                  :label="labels.user.lastname + ' *'"
                  v-model="user.lastname"
                  :placeholder="labels.user.lastname"
                  :rules="helper.required
                                        .concat(helper.max(20))
                                        .concat(helper.min(4))"
                  hide-details="auto"
              >
              </v-text-field>
            </v-col>

            <!-- Phone -->
            <v-col md="6" cols="12">
              <v-text-field
                  :label="labels.user.phone + ' *'"
                  v-model="user.phone"
                  :placeholder="labels.user.phone"
                  :rules="helper.required"
                  hide-details="auto"
              >
              </v-text-field>
            </v-col>

            <!-- Email -->
            <v-col md="6" cols="12">
              <v-text-field
                  :label="labels.user.email + ' *'"
                  v-model="user.email"
                  :placeholder="labels.user.email"
                  :rules="helper.required"
                  hide-details="auto"
                  type="mail"
              >
              </v-text-field>
            </v-col>

            <!-- New Password -->
            <v-col md="6" cols="12">
              <v-text-field
                  :label="labels.user.NewPassword + ' *'"
                  v-model="user.NewPassword"
                  :placeholder="labels.user.NewPassword"
                  :rules="helper.min(6).concat(helper.max(14))"
                  hide-details="auto"
                  type="password"
              >
              </v-text-field>
            </v-col>
          </v-row>
        </v-card-text>
        <v-card-actions>
          <v-row>
            <v-col cols="12" class="text-center">
              <v-btn
                  variant="elevated"
                  type="submit"
                  color="primary"
                  :loading="loading"
                  :disabled="loading"
              >{{ labels.submit }}
              </v-btn>
            </v-col>
          </v-row>
        </v-card-actions>
      </v-form>
    </v-card>
  </Layout>
</template>
