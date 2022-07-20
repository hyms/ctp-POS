<template>
    <v-app>
        <v-main>
            <div class="auth-wrapper auth-v1">
                <div class="auth-inner">
                    <v-card class="auth-card">
                        <!-- title -->
                        <v-card-text>
                            <p class="text-2xl font-weight-semibold text--primary mb-2">
                                Bienvenido a xCTP
                            </p>
                            <p class="mb-2">
                                Por favor ingresa tu usuario y contraseña
                            </p>
                            <v-alert
                                dismissible
                                class="mb-4"
                                v-if="Object.keys(errors).length > 0"
                                v-model="alert"
                                color="red"
                                outlined
                                text
                            >
                                <ul class="text-sm">
                                    <li v-for="(error, key) in errors" :key="key">{{ key }}: {{ error }}</li>
                                </ul>
                            </v-alert>

                            <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
                                {{ status }}
                            </div>
                        </v-card-text>
                        <!-- login form -->
                        <v-card-text>
                            <form @submit.prevent="submit">
                                <div>
                                    <v-text-field
                                        dense
                                        label="Usuario"
                                        outlined
                                        id="username"
                                        v-model="form.username"
                                        type="text"
                                        hide-details="auto"
                                    ></v-text-field>
                                </div>
                                <div class="mt-3">
                                    <v-text-field
                                        dense
                                        label="Contraseña"
                                        outlined
                                        id="password"
                                        v-model="form.password"
                                        type="password"
                                        hide-details="auto"
                                    ></v-text-field>
                                </div>

                                <div class="d-flex align-center justify-space-between flex-wrap">
                                    <v-checkbox
                                        label="Remember Me"
                                        hide-details
                                        class="me-3 mt-1"
                                    >
                                    </v-checkbox>
                                </div>

                                <v-btn
                                    class="mt-6"
                                    block
                                    color="primary"
                                    type="submit"
                                    :disabled="form.processing">
                                    Ingresar
                                    <template v-slot:loader>
                                        <span>Ingresando...</span>
                                    </template>
                                </v-btn>
                            </form>
                        </v-card-text>
                    </v-card>
                </div>
            </div>
        </v-main>
    </v-app>
</template>
<script>
export default {
    props: {
        canResetPassword: Boolean,
        status: String,
        errors: Object,
    },
    data() {
        return {
            form: this.$inertia.form({
                username: '',
                password: '',
                remember: false
            }),
            alert: false
        }
    },
    methods: {
        submit() {
            console.log('login')
            this.form.post('/login', {
                onFinish: () => {
                    this.form.reset('password')
                    if (Object.keys(this.errors).length > 0) {
                        this.alert = true;
                    }
                },
            });
        },
        state(value) {
            if (value === undefined || Object.keys(this.errors).length === 0) {
                return null;
            }
            return !value;
        },
    }
}

</script>
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
