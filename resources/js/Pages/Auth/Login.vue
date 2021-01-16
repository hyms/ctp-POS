<template>
    <div class="container">
        <div class="row">
            <div class="col-sm-12">

                <div class="wrapper-page">
                    <div class="m-t-40 card-box">
                        <div class="text-center">
                            <h2 class="text-uppercase m-t-0 m-b-30">
                                Ingresar
                            </h2>
                        </div>
                        <div class="col">
                            <b-alert :show="Object.values(errors).length>0" variant="danger" dismissible>
                                <ul>
                                    <li v-for="(value,key) in errors">
                                        {{key}}:{{value}}
                                    </li>
                                </ul>
                            </b-alert>
                        </div>
                        <div class="account-content">
                            <form class="form-horizontal" @submit.prevent="submit">
                                <div class="form-group m-b-20">
                                    <div class="col-12">
                                        <label for="username">Username</label>
                                        <b-form-input
                                            id="username"
                                            v-model="form.username"
                                            placeholder="Usuario"
                                            trim
                                            type="text"
                                        ></b-form-input>
                                    </div>
                                </div>
                                <div class="form-group m-b-20">
                                    <div class="col-12">
                                        <label for="password">Contraseña</label>
                                        <b-form-input
                                            id="password"
                                            v-model="form.password"
                                            placeholder="Contraseña"
                                            trim
                                            type="password"
                                        ></b-form-input>
                                    </div>
                                </div>
                                <div class="form-group m-b-30">
                                    <div class="col-8">
                                        <div class="icheck-primary">
                                            <input id="remember" v-model="form.remember" type="checkbox">
                                            <label for="remember">
                                                Remember Me
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group account-btn text-center m-t-10">
                                    <!-- /.col -->
                                    <div class="col-12">
                                        <loading-button :loading="sending" class="btn btn-lg btn-primary btn-block" type="submit">Login
                                        </loading-button>
                                    </div>
                                    <!-- /.col -->
                                </div>
                            </form>
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
        },
    },
}
</script>
