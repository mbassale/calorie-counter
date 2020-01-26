<template>
    <b-container>
        <!-- Outer Row -->
        <div class="row justify-content-center">
            <div class="col-xl-10 col-lg-12 col-md-9">
                <div class="card o-hidden border-0 shadow-lg my-5">
                    <div class="card-body p-0">
                        <!-- Nested Row within Card Body -->
                        <div class="row">
                            <div class="col-lg-6 d-none d-lg-block bg-login-image"></div>
                            <div class="col-lg-6">
                                <div class="p-5">
                                    <div class="text-center">
                                        <h1 class="h4 text-gray-900 mb-4">Welcome Back!</h1>
                                    </div>
                                    <b-alert :show="error" dismissible variant="danger">{{ error }}</b-alert>
                                    <form class="user">
                                        <div class="form-group">
                                            <input type="email" class="form-control form-control-user"
                                                   placeholder="Enter Email Address..."
                                                   v-model="email" :disabled="disabled"
                                                   @keydown.enter="handleLogin">
                                        </div>
                                        <div class="form-group">
                                            <input type="password" class="form-control form-control-user"
                                                   placeholder="Password"
                                                   v-model="password" :disabled="disabled"
                                                   @keydown.enter="handleLogin">
                                        </div>
                                        <button type="button" class="btn btn-primary btn-user btn-block"
                                                @click="handleLogin" :disabled="disabled || invalid">
                                            <fa-icon v-if="isProcessing" icon="spinner" spin /> Login
                                        </button>
                                    </form>
                                    <hr>
                                    <div class="text-center">
                                        <button type="button" class="btn btn-link" :disabled="true">
                                            Forgot Password?
                                        </button>
                                    </div>
                                    <div class="text-center">
                                        <button type="button" class="btn btn-link"
                                                @click="$router.push({name: 'register'})"
                                                :disabled="disabled">
                                            Create an Account!
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </b-container>
</template>

<script>
    import _ from 'lodash';
    import axios from 'axios';
    import { LOGIN } from '../../store/actions';

    export default {
        name: 'Login',
        data() {
            return {
                isProcessing: false,
                email: null,
                password: null,
                error: null,
                invalidEmail: false,
                invalidPassword: false
            };
        },
        computed: {
            disabled() {
                return this.isProcessing;
            },
            invalid() {
                return _.isEmpty(this.email) || _.isEmpty(this.password);
            }
        },
        methods: {
            handleLogin() {
                if (this.invalid) return;
                this.error = null;
                this.isProcessing = true;
                axios.post('/api/login', {
                    email: this.email,
                    password: this.password
                }).then(response => {
                    if (response.data) {
                        return this.$store.dispatch(LOGIN, {
                            token: response.data.token
                        }).then(() => this.$router.push({ name: 'dashboard' }))
                            .finally(() => this.isProcessing = false);
                    }
                }).catch(error => {
                    this.error = 'Invalid credentials';
                    this.isProcessing = false;
                });
            }
        }
    }
</script>

<style scoped>

</style>
