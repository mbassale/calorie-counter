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
                                        <input type="text" class="form-control form-control-user"
                                               placeholder="First Name"
                                               :disabled="disabled">
                                    </div>
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control form-control-user"
                                               placeholder="Last Name"
                                               :disabled="disabled">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <input type="email" class="form-control form-control-user"
                                           placeholder="Email Address" :disabled="disabled">
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-6 mb-3 mb-sm-0">
                                        <input type="password" class="form-control form-control-user"
                                               placeholder="Password" :disabled="disabled">
                                    </div>
                                    <div class="col-sm-6">
                                        <input type="password" class="form-control form-control-user"
                                               placeholder="Repeat Password" :disabled="disabled">
                                    </div>
                                </div>
                                <button class="btn btn-primary btn-user btn-block" @click="handleRegister"
                                        :disabled="isProcessing">
                                    Register Account
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
    export default {
        name: 'Register',
        data() {
            return {
                isProcessing: false
            }
        },
        computed: {
            disabled() {
                return this.isProcessing;
            }
        },
        methods: {
            handleRegister() {
                this.isProcessing = true;
            }
        }
    }
</script>

<style scoped>

</style>
