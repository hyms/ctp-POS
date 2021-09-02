<template>
    <div class="hold-transition bg-img">
        <div class="container h-p100">
            <div class="row align-items-center justify-content-md-center h-p100">
                <div class="col-12">
                    <div class="row no-gutters justify-content-md-center">
                        <div class="col-lg-5 col-md-5 col-12">
                            <div class="p-40 bg-white content-bottom h-p100">
                                <b-alert variant="danger" dismissible :show="!!errors.usuario">
                                    {{ errors.usuario }}
                                </b-alert>
                                <form class="form-element" @submit.prevent="submit">
                                    <div class="input-group mb-3">
                                        <b-form-input
                                            id="username"
                                            v-model="form.username"
                                            placeholder="Usuario"
                                            trim
                                            type="text"
                                            :state="state(errors.username)"
                                            :invalid-feedback="errors.username"
                                        ></b-form-input>
                                    </div>
                                    <div class="input-group mb-4">
                                        <b-form-input
                                            id="password"
                                            v-model="form.password"
                                            placeholder="Contraseña"
                                            trim
                                            type="password"
                                            :state="state(errors.password)"
                                            :invalid-feedback="errors.password"
                                        ></b-form-input>
                                    </div>
                                    <div class="form-group text-left">
                                        <div class="checkbox checkbox-fill d-inline">
                                            <input id="remember" v-model="form.remember" type="checkbox">
                                            <label for="remember" class="cr">
                                                Remember Me
                                            </label>
                                        </div>
                                    </div>
                                    <div class="form-group account-btn text-center m-t-10">
                                        <div class="col-12">
                                            <loading-button :loading="sending"
                                                            class="btn btn-block btn-info shadow-2 mb-4"
                                                            type="submit" :text="'Ingresar'" :textLoad="'Ingresando'">
                                                Login
                                            </loading-button>
                                        </div>
                                    </div>
                                </form>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import LoadingButton from '@/Shared/LoadingButton'

export default {
    components: {
        LoadingButton,
    },
    props: {
        errors: Object,
    },
    data() {
        return {
            sending: false,
            form: {
                username: '',
                password: '',
                remember: null,
            },
        }
    },
    methods: {
        submit() {
            this.errors = [];
            this.sending = true
            this.$inertia.post('/login', this.form, {
                onFinish: () => this.sending = false,
            })
        },
        state(value) {
            if (value === undefined || Object.keys(this.errors).length === 0) {
                return null;
            }
            return !value;
        }
    },

}
</script>
