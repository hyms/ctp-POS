<template>
    <div class="all-wrapper menu-side with-pattern" >
        <div class="auth-box-w">
            <div class="logo-w">
                <a href="#"><img alt="" src=""/></a>
            </div>
            <h4 class="auth-header">
                Ingresar
            </h4>
            <div v-if="m_error" class="col">
                <b-alert :show="Object.values(errors).length>0" variant="danger" dismissible>
                    {{ Object.values(errors) }}
                </b-alert>
            </div>
            <form @submit.prevent="submit">
                <div class="input-group mb-3">
                    <b-form-input
                        v-model="form.username"
                        placeholder="Usuario"
                        trim
                        type="text"
                    ></b-form-input>
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-envelope"></span>
                        </div>
                    </div>
                </div>
                <div class="input-group mb-3">
                    <b-form-input
                        v-model="form.password"
                        placeholder="Contraseña"
                        trim
                        type="password"
                    ></b-form-input>
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-lock"></span>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-8">
                        <div class="icheck-primary">
                            <input id="remember" v-model="form.remember" type="checkbox">
                            <label for="remember">
                                Remember Me
                            </label>
                        </div>
                    </div>
                    <!-- /.col -->
                    <div class="col-4">
                        <loading-button :loading="sending" class="btn-indigo" type="submit">Login
                        </loading-button>
                    </div>
                    <!-- /.col -->
                </div>
            </form>
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
                username: 'helier',
                password: '123456',
                remember: null,
            },
        }
    },
    methods: {
        submit() {
            const data = {
                username: this.form.username,
                password: this.form.password,
                remember: this.form.remember,
            }

            this.$inertia.post('/login', data, {
                onStart: () => this.sending = true,
                onFinish: () => this.sending = false,
            })
            console.log(Object.values(this.errors))
        },
    },
}
</script>
