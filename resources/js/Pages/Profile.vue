<script setup>
import { onMounted, ref } from "vue";
import Layout from "@/Layouts/Authenticated.vue";
import { api, labels, rules } from "@/helpers";
import FormView from "@/Components/dialogs/FormView.vue";
import Snackbar from "@/Components/snackbar.vue";

const props = defineProps({
    user: Object,
    errors: Object,
});

const form = ref(null);
const userForm = ref({
    firstname: "",
    lastname: "",
    email: "",
    NewPassword: "",
    phone: "",
});
const username = ref("");
const loading = ref(false);
const snackbar = ref({
    view: false,
    color: "",
    text: "",
});

//------------------ Update Profile ----------------------\\
function Update_Profile(valid) {
    if (valid) {
        api.put({
            url: "/update_user_profile/" + props.user.id,
            params: {
                firstname: userForm.value.firstname,
                lastname: userForm.value.lastname,
                email: userForm.value.email,
                NewPassword: userForm.value.NewPassword,
                phone: userForm.value.phone,
            },
            loadingItem: loading,
            snackbar,
        });
    }
}

onMounted(() => {
    userForm.value = props.user;
});
</script>
<template>
    <Layout>
        <snackbar
            v-model="snackbar.view"
            :color="snackbar.color"
            :text="snackbar.text"
        ></snackbar>
        <FormView :on-save="Update_Profile" :loading="loading">
            <!--  Profile -->
            <v-row>
                <!-- First name -->
                <v-col md="6" cols="12">
                    <v-text-field
                        :label="labels.user.firstname + ' *'"
                        v-model="userForm.firstname"
                        :placeholder="labels.user.firstname"
                        :rules="
                            rules.required
                                .concat(rules.max(20))
                                .concat(rules.min(4))
                        "
                        hide-details="auto"
                    >
                    </v-text-field>
                </v-col>

                <!-- Last name -->
                <v-col md="6" cols="12">
                    <v-text-field
                        :label="labels.user.lastname + ' *'"
                        v-model="userForm.lastname"
                        :placeholder="labels.user.lastname"
                        :rules="
                            rules.required
                                .concat(rules.max(20))
                                .concat(rules.min(4))
                        "
                        hide-details="auto"
                    >
                    </v-text-field>
                </v-col>

                <!-- Phone -->
                <v-col md="6" cols="12">
                    <v-text-field
                        :label="labels.user.phone + ' *'"
                        v-model="userForm.phone"
                        :placeholder="labels.user.phone"
                        :rules="rules.required"
                        hide-details="auto"
                    >
                    </v-text-field>
                </v-col>

                <!-- Email -->
                <v-col md="6" cols="12">
                    <v-text-field
                        :label="labels.user.email + ' *'"
                        v-model="userForm.email"
                        :placeholder="labels.user.email"
                        :rules="rules.required"
                        hide-details="auto"
                        type="mail"
                    >
                    </v-text-field>
                </v-col>

                <!-- New Password -->
                <v-col md="6" cols="12">
                    <v-text-field
                        :label="labels.user.NewPassword + ' *'"
                        v-model="userForm.NewPassword"
                        :placeholder="labels.user.NewPassword"
                        :rules="rules.min(6).concat(rules.max(14))"
                        hide-details="auto"
                        type="password"
                    >
                    </v-text-field>
                </v-col>
            </v-row>
        </FormView>
    </Layout>
</template>
