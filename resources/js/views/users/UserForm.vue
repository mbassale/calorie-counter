<template>
    <b-form @submit="onSubmit" @reset="onReset">
        <b-alert variant="danger" dismissible :show="error">{{ error }}</b-alert>
        <b-form-row>
            <b-col>
                <b-form-group label="Role" label-for="role_id"
                              :state="$v.role_id.$error ? false : null">
                    <b-form-select id="role_id" v-model="role_id" :options="selectRoles" :disabled="disabled"
                                   :state="$v.role_id.$error ? false : null"/>
                </b-form-group>
            </b-col>
            <b-col>
                <b-form-group label="First Name" label-for="first_name"
                              :state="$v.first_name.$error ? false : null">
                    <b-form-input id="first_name" v-model="first_name" placeholder="Enter first name"
                                  :disabled="disabled" :state="$v.first_name.$error ? false : null"/>
                </b-form-group>
            </b-col>
            <b-col>
                <b-form-group label="Last Name" label-for="last_name" :state="$v.last_name.$error ? false : null">
                    <b-form-input id="last_name" v-model="last_name" placeholder="Enter last name"
                                  :disabled="disabled" :state="$v.last_name.$error ? false : null"/>
                </b-form-group>
            </b-col>
        </b-form-row>

        <b-form-row>
            <b-col>
                <b-form-group label="Email" label-for="email" :state="$v.email.$error ? false : null">
                    <b-form-input id="email" type="email" v-model="email" placeholder="Enter email"
                                  :disabled="disabled" :state="$v.email.$error ? false : null"/>
                </b-form-group>
            </b-col>
            <b-col>
                <b-form-group label="Password" label-for="email" :state="$v.password.$error ? false : null">
                    <b-form-input id="email" type="password" v-model="password" placeholder="Password"
                                  :disabled="disabled" :state="$v.password.$error ? false : null"/>
                </b-form-group>
            </b-col>
            <b-col>
                <b-form-group label="Repeat Password" label-for="email"
                              :state="$v.password_confirmation.$error ? false : null">
                    <b-form-input id="email" type="password" v-model="password_confirmation"
                                  :disabled="disabled" :state="$v.password_confirmation.$error ? false : null"
                                  placeholder="Repeat Password"/>
                </b-form-group>
            </b-col>
        </b-form-row>

        <b-form-row>
            <b-col cols="4">
                <b-form-group label="Calories Per Day" laber-for="calories_per_day"
                              :state="$v.calories_per_day.$error ? false : null">
                    <b-form-input id="calories_per_day" type="number" v-model="calories_per_day"
                                  placeholder="Calories Per Day" min="0" step="1"
                                  :disabled="disabled" :state="$v.calories_per_day.$error ? false : null"/>
                </b-form-group>
            </b-col>
        </b-form-row>

        <b-button type="submit" variant="primary" :disabled="disabled || $v.$anyError">
            <fa-icon v-if="isProcessing" icon="spinner" spin/>
            <fa-icon v-else icon="save"/>
            Save
        </b-button>
        <b-button type="reset" variant="danger" :disabled="disabled">
            <fa-icon icon="eraser"/>
            Reset
        </b-button>
        <b-button variant="secondary" @click="$emit('cancel')" :disabled="disabled">
            Cancel
        </b-button>
    </b-form>
</template>

<script>
    import _ from 'lodash';
    import {mapState, mapGetters} from 'vuex';
    import {CREATE_USER, LOAD_ROLES, UPDATE_USER} from '../../store/actions';
    import ToastMixin from '../../mixins/ToastMixin';
    import ValidationMixin from '../../mixins/ValidationMixin';
    import {minLength, required, email, sameAs, integer, minValue} from 'vuelidate/lib/validators';

    export default {
        name: 'UserForm',
        mixins: [ToastMixin, ValidationMixin],
        props: {
            user: {
                required: true,
                type: Object
            }
        },
        data() {
            return {
                isLoading: false,
                isProcessing: false,
                error: null,
                id: null,
                role_id: null,
                first_name: null,
                last_name: null,
                email: null,
                password: null,
                password_confirmation: null,
                calories_per_day: null
            };
        },
        computed: {
            disabled() {
                return this.isLoading || this.isProcessing;
            },
            selectRoles() {
                return this.roles.map(role => {
                    return {
                        value: role.id,
                        text: _.capitalize(role.name)
                    };
                });
            },
            ...mapState(['roles'])
        },
        validations() {
            const schema = {
                role_id: {
                    required
                },
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
                    minLength: minLength(6)
                },
                password_confirmation: {
                    sameAsPassword: sameAs('password')
                },
                calories_per_day: {
                    integer,
                    minValue: minValue(1)
                }
            };
            if (!this.user.id) {
                _.assign(schema, {
                    password: {
                        required,
                        minLength: minLength(6)
                    },
                    password_confirmation: {
                        required,
                        sameAsPassword: sameAs('password')
                    }
                });
            }
            return schema;
        },
        mounted() {
            this.$nextTick(() => this.resetForm());
        },
        methods: {
            resetForm() {
                this.id = null;
                this.role_id = null;
                this.first_name = null;
                this.last_name = null;
                this.email = null;
                this.password = null;
                this.password_confirmation = null;
                this.calories_per_day = null;
                if (this.user.id) {
                    this.id = this.user.id || null;
                    this.role_id = this.user.role_id || null;
                    this.first_name = this.user.first_name || null;
                    this.last_name = this.user.last_name || null;
                    this.email = this.user.email || null;
                    this.calories_per_day = this.user.calories_per_day || null;
                }
                if (_.isEmpty(this.roles)) {
                    this.isLoading = true;
                    this.$store.dispatch(LOAD_ROLES).finally(() => this.isLoading = false);
                }
            },
            onSubmit(evt) {
                evt.preventDefault();
                this.$v.$touch();
                if (this.$v.$invalid) {
                    return;
                }
                this.isProcessing = true;
                const userData = _.pick(this, ['id', 'role_id', 'first_name', 'last_name', 'email', 'calories_per_day']);
                if (this.user.id) {
                    if (this.password) {
                        _.assign(userData, _.pick(this, ['password', 'password_confirmation']));
                    }
                    this.$store.dispatch(UPDATE_USER, userData)
                        .then(() => this.showSuccess('User Updated'))
                        .catch(error => {
                            const errorData = this.getValidationErrors(error);
                            if (errorData && errorData.email) {
                                this.error = errorData.email;
                                return;
                            }
                            this.showNetworkError(error);
                        }).finally(() => this.isProcessing = false);
                } else {
                    _.assign(userData, _.pick(this, ['password', 'password_confirmation']));
                    this.$store.dispatch(CREATE_USER, userData)
                        .then(() => this.showSuccess('User Created'))
                        .catch(error => {
                            const errorData = this.getValidationErrors(error);
                            if (errorData && errorData.email) {
                                this.error = errorData.email;
                                return;
                            }
                            this.showNetworkError(error);
                        }).finally(() => this.isProcessing = false);
                }
            },
            onReset(evt) {
                evt.preventDefault();
                this.resetForm();
            }
        }
    }
</script>

<style scoped>

</style>
