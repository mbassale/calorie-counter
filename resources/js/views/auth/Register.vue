<template>
    <b-container>
        <div class="card o-hidden border-0 shadow-lg my-5">
            <div class="card-body p-0">
                <!-- Nested Row within Card Body -->
                <div class="row">
                    <div class="col-lg-5 d-none d-lg-block bg-register-image"></div>
                    <div class="col-lg-7">
                        <div class="p-5">
                            <div class="text-center">
                                <h1 class="h4 text-gray-900 mb-4">Create an Account!</h1>
                            </div>
                            <form class="user">
                                <div class="form-group row">
                                    <div class="col-sm-6 mb-3 mb-sm-0">
                                        <input type="text" class="form-control form-control-user" placeholder="First Name"
                                               v-model="first_name" :disabled="disabled">
                                    </div>
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control form-control-user" placeholder="Last Name"
                                               v-model="last_name" :disabled="disabled">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <input type="email" class="form-control form-control-user" placeholder="Email Address"
                                           v-model="email" :disabled="disabled">
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-6 mb-3 mb-sm-0">
                                        <input type="password" class="form-control form-control-user" placeholder="Password"
                                               v-model="password" :disabled="disabled">
                                    </div>
                                    <div class="col-sm-6">
                                        <input type="password" class="form-control form-control-user" placeholder="Repeat Password"
                                               v-model="password_confirmation" :disabled="disabled">
                                    </div>
                                </div>
                                <button class="btn btn-primary btn-user btn-block" @click="handleRegister"
                                        :disabled="isProcessing || invalid">
                                    <fa-icon v-if="isProcessing" icon="spinner" spin /> Register Account
                                </button>
                            </form>
                            <hr>
                            <div class="text-center">
                                <button class="btn btn-link" @click="$router.push({ name: 'forgotPassword' })"
                                        :disabled="disabled">
                                    Forgot Password?
                                </button>
                            </div>
                            <div class="text-center">
                                <button class="btn btn-link" @click="$router.push({ name: 'login' })"
                                        :disabled="disabled">
                                    Already have an account? Login!
                                </button>
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
    import {LOGIN} from "../../store/actions";

    export default {
        name: 'Register',
        data() {
            return {
                isProcessing: false,
                first_name: null,
                last_name: null,
                email: null,
                password: null,
                password_confirmation: null
            }
        },
        computed: {
            disabled() {
                return this.isProcessing;
            },
            invalid() {
                return _.isEmpty(this.first_name) || _.isEmpty(this.last_name) ||
                    _.isEmpty(this.email) || _.isEmpty(this.password) || _.isEmpty(this.password_confirmation) ||
                    this.password !== this.password_confirmation;
            }
        },
        methods: {
            handleRegister() {
                this.isProcessing = true;
                let data = _.pick(this, ['first_name', 'last_name', 'email', 'password', 'password_confirmation']);
                axios.post('/api/register', data).then(response => {
                    if (response.data) {
                        return this.$store.dispatch(LOGIN, {
                            token: response.data.token,
                        }).then(() => this.$router.push({name: 'dashboard'}))
                            .finally(() => this.isProcessing = false);
                    }
                }).catch(error => {
                    this.isProcessing = false;
                });
            }
        }
    }
</script>

<style scoped>

</style>
