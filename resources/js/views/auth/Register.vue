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
                            <b-form class="user">
                                <b-form-row>
                                    <div class="col-sm-6 mb-3 mb-sm-0">
                                        <b-form-group :state="$v.first_name.$error ? false : null">
                                            <b-input type="text" class="form-control-user" placeholder="First Name"
                                                     v-model="first_name" :state="$v.first_name.$error ? false : null"
                                                     :disabled="disabled"/>
                                        </b-form-group>
                                    </div>
                                    <div class="col-sm-6">
                                        <b-form-group :state="$v.last_name.$error ? false : null">
                                            <b-input type="text" class="form-control-user" placeholder="Last Name"
                                                     v-model="last_name" :state="$v.last_name.$error ? false : null"
                                                     :disabled="disabled"/>
                                        </b-form-group>
                                    </div>
                                </b-form-row>
                                <b-form-group :state="$v.email.$error ? false : null">
                                    <b-input type="email" class="form-control-user" placeholder="Email Address"
                                             v-model="email" :state="$v.email.$error ? false : null"
                                             :disabled="disabled"/>
                                </b-form-group>
                                <b-form-row>
                                    <div class="col-sm-6 mb-3 mb-sm-0">
                                        <b-form-group :state="$v.password.$error ? false : null">
                                            <b-input type="password" class="form-control-user" placeholder="Password"
                                                     v-model="password" :state="$v.password.$error ? false : null"
                                                     :disabled="disabled"/>
                                        </b-form-group>
                                    </div>
                                    <div class="col-sm-6">
                                        <b-form-group :state="$v.password_confirmation.$error ? false : null">
                                            <b-input type="password" class="form-control-user"
                                                     placeholder="Repeat Password"
                                                     v-model="password_confirmation"
                                                     :state="$v.password_confirmation.$error ? false : null"
                                                     :disabled="disabled"/>
                                        </b-form-group>
                                    </div>
                                </b-form-row>
                                <button class="btn btn-primary btn-user btn-block" @click="handleRegister"
                                        :disabled="isProcessing || $v.$anyError">
                                    <fa-icon v-if="isProcessing" icon="spinner" spin/>
                                    Register Account
                                </button>
                            </b-form>
                            <hr>
                            <div class="text-center">
                                <button class="btn btn-link" :disabled="true">
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
    import {required, minLength, email, sameAs} from 'vuelidate/lib/validators'
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
            }
        },
        validations: {
            first_name: {
                required,
                minLength: minLength(2)
            },
            last_name: {
                required,
                minLength: minLength(2)
            },
            email: {
                required,
                email
            },
            password: {
                required,
                minLength: minLength(6)
            },
            password_confirmation: {
                required,
                sameAsPassword: sameAs('password')
            }
        },
        methods: {
            handleRegister(evt) {
                evt.preventDefault();
                this.$v.$touch();
                if (this.$v.$invalid) {
                    return;
                }
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
