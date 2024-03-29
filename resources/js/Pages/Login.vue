<script setup>
import { ref } from "vue";
import Layout from "@/Layouts/Guest.vue";
import { router } from "@inertiajs/vue3";
import { rules } from "@/helpers";

defineProps({
    canResetPassword: Boolean,
    errors: Object,
});

const form = ref({
    username: "",
    password: "",
    remember: false,
});
const processing = ref(false);
const alert = ref(false);

function submit() {
    processing.value = true;
    router.post("/login", form.value, {
        onSuccess: (page) => {},
        onError: (error) => {
            if (Object.keys(error).length > 0) {
                alert.value = true;
            }
        },
        onFinish: () => {
            form.value.password = "";
            processing.value = false;
        },
    });
}
</script>
<template>
    <Layout>
        <div class="auth-wrapper auth-v1">
            <div class="auth-inner">
                <v-card class="auth-card" :elevation="5">
                    <!-- title -->
                    <v-card-title class="text-2xl">
                        Bienvenido a xCTP</v-card-title
                    >
                    <v-card-subtitle>
                        Por favor ingresa tu usuario y contraseña
                    </v-card-subtitle>
                    <!-- login form -->
                    <form @submit.prevent="submit" :disabled="processing">
                        <v-card-text>
                            <v-alert
                                closable
                                class="mb-4"
                                v-if="Object.keys(errors).length > 0"
                                v-model="alert"
                                type="error"
                            >
                                <ul class="text-sm">
                                    <li
                                        v-for="(error, key) in errors"
                                        :key="key"
                                    >
                                        {{ error }}
                                    </li>
                                </ul>
                            </v-alert>

                            <div>
                                <v-text-field
                                    label="Usuario"
                                    color="primary"
                                    v-model="form.username"
                                    type="text"
                                    hide-details="auto"
                                    :rules="rules.required"
                                ></v-text-field>
                            </div>
                            <div class="mt-3">
                                <v-text-field
                                    label="Contraseña"
                                    v-model="form.password"
                                    color="primary"
                                    type="password"
                                    hide-details="auto"
                                    :rules="rules.required"
                                ></v-text-field>
                            </div>
                            <div
                                class="d-flex align-center justify-space-between flex-wrap"
                            >
                                <v-checkbox
                                    label="Remember Me"
                                    hide-details
                                    class="me-3 mt-1"
                                    color="primary"
                                >
                                </v-checkbox>
                            </div>
                        </v-card-text>
                        <v-card-actions>
                            <v-btn
                                block
                                color="primary"
                                type="submit"
                                :loading="processing"
                                variant="flat"
                                size="large"
                            >
                                Ingresar
                                <template v-slot:loader>
                                    <span>Ingresando...</span>
                                </template>
                            </v-btn>
                        </v-card-actions>
                    </form>
                </v-card>
            </div>
        </div>
    </Layout>
</template>

<style lang="scss">
.auth-wrapper {
    display: flex;
    min-height: calc(var(--vh, 1vh) * 100);
    width: 100%;
    flex-basis: 100%;
    align-items: center;

    // common style for both v1 and v2
    a {
        text-decoration: unset;
    }

    // auth v1
    &.auth-v1 {
        align-items: center;
        justify-content: center;
        overflow: hidden;
        padding: 1.5rem;

        .auth-mask-bg {
            position: absolute;
            bottom: 0;
            width: 100%;
        }

        .auth-tree,
        .auth-tree-3 {
            position: absolute;
        }

        .auth-tree {
            bottom: 0;
            left: 0;
        }

        .auth-tree-3 {
            bottom: 0;
            right: 0;
        }

        // auth card
        .auth-inner {
            width: 28rem;
            z-index: 1;

            .auth-card {
                padding: 0.9375rem 0.875rem;
            }
        }
    }
}

@media (max-width: 600px) {
    // auth bg and tree hide in sm screen
    .auth-v1 {
        .auth-tree,
        .auth-tree-3,
        .auth-mask-bg {
            display: none;
        }
    }
}
</style>
