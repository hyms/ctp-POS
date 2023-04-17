<script setup>
import { ref } from "vue";
import Layout from "@/Layouts/Authenticated.vue";
import { router } from "@inertiajs/vue3";
import ruleForm from "@/rules";
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
const userLabels = ref({
    firstname: "Nombre",
    lastname: "Apellido",
    username: "Usuario",
    password: "Contraseña",
    NewPassword: "Nueva Contraseña",
    email: "Correo",
    phone: "Telefono",
    ci: "CI",
    statut: "Estado",
    role: "Role",
    is_all_warehouses: "Todas las sucursales",
});

//------------- Submit Update Profile
async function Submit_Profile() {
    const validate = await form.value.validate();
    if (validate.valid) Update_Profile();
}

//------------------ Update Profile ----------------------\\
function Update_Profile() {
    loading.value = true;
    snackbar.value = false;
    // Start the progress bar.

    axios
        .put("/update_user_profile/" + props.user.id, {
            firstname: props.user.firstname,
            lastname: props.user.lastname,
            email: props.user.email,
            NewPassword: props.user.NewPassword,
            phone: props.user.phone,
        })
        .then(({ data }) => {
            snackbar.value = true;
            snackbarColor.value = "success";
            snackbarText.value = "Actualizacion exitosa";
            router.reload();
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
    <Layout :loading="loading">
        <snackbar
            :snackbar="snackbar"
            :snackbarColor="snackbarColor"
            :snackbarText="snackbarText"
        >
        </snackbar>

        <v-card>
            <v-card-text>
                <!--  Profile -->
                <v-form @submit.prevent="Submit_Profile" ref="form">
                    <v-row>
                        <!-- First name -->
                        <v-col md="6" sm="12">
                            <v-text-field
                                :label="userLabels.firstname + ' *'"
                                v-model="user.firstname"
                                :placeholder="userLabels.firstname"
                                :rules="
                                    ruleForm.required
                                        .concat(ruleForm.max(20))
                                        .concat(ruleForm.min(4))
                                "
                                variant="outlined"
                                density="comfortable"
                                hide-details="auto"
                            >
                            </v-text-field>
                        </v-col>

                        <!-- Last name -->
                        <v-col md="6" sm="12">
                            <v-text-field
                                :label="userLabels.lastname + ' *'"
                                v-model="user.lastname"
                                :placeholder="userLabels.lastname"
                                :rules="
                                    ruleForm.required
                                        .concat(ruleForm.max(20))
                                        .concat(ruleForm.min(4))
                                "
                                variant="outlined"
                                density="comfortable"
                                hide-details="auto"
                            >
                            </v-text-field>
                        </v-col>

                        <!-- Phone -->
                        <v-col md="6" sm="12">
                            <v-text-field
                                :label="userLabels.phone + ' *'"
                                v-model="user.phone"
                                :placeholder="userLabels.phone"
                                :rules="ruleForm.required"
                                variant="outlined"
                                density="comfortable"
                                hide-details="auto"
                            >
                            </v-text-field>
                        </v-col>

                        <!-- Email -->
                        <v-col md="6" sm="12">
                            <v-text-field
                                :label="userLabels.email + ' *'"
                                v-model="user.email"
                                :placeholder="userLabels.email"
                                :rules="ruleForm.required"
                                variant="outlined"
                                density="comfortable"
                                hide-details="auto"
                                type="mail"
                            >
                            </v-text-field>
                        </v-col>

                        <!-- New Password -->
                        <v-col md="6">
                            <v-text-field
                                :label="userLabels.NewPassword + ' *'"
                                v-model="user.NewPassword"
                                :placeholder="userLabels.NewPassword"
                                :rules="
                                    ruleForm.min(6).concat(ruleForm.max(14))
                                "
                                variant="outlined"
                                density="comfortable"
                                hide-details="auto"
                                type="password"
                            >
                            </v-text-field>
                        </v-col>
                    </v-row>
                    <v-row>
                        <v-col md="12" class="mt-3">
                            <v-btn
                                variant="elevated"
                                type="submit"
                                color="primary"
                                :loading="loading"
                                :disabled="loading"
                                >Guardar
                            </v-btn>
                        </v-col>
                    </v-row>
                </v-form>
            </v-card-text>
        </v-card>
    </Layout>
</template>
