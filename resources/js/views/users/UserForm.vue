<template>
    <b-form @submit="onSubmit" @reset="onReset">
        <b-form-group v-if="isAdmin" label="Role" label-for="role_id">
            <b-form-select id="role_id" v-model="role_id" :options="selectRoles" required
                           :disabled="disabled"/>
        </b-form-group>

        <b-form-group label="First Name" label-for="first_name">
            <b-form-input id="first_name" v-model="first_name" required placeholder="Enter first name"
                          :disabled="disabled"/>
        </b-form-group>

        <b-form-group label="Last Name" label-for="last_name">
            <b-form-input id="last_name" v-model="last_name" required placeholder="Enter last name"
                          :disabled="disabled"/>
        </b-form-group>

        <b-form-group label="Email" label-for="email">
            <b-form-input id="email" type="email" v-model="email" required placeholder="Enter email" :disabled="disabled"/>
        </b-form-group>

        <b-button type="submit" variant="primary" :disabled="disabled">
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
    import {LOAD_ROLES, UPDATE_USER} from '../../store/actions';
    import ToastMixin from '../../mixins/ToastMixin';

    export default {
        name: 'UserForm',
        mixins: [ToastMixin],
        props: {
            user: {
                required: false,
                validator: val => _.isNull(val) || _.isObject(val),
                default: () => null,
            }
        },
        data() {
            return {
                isLoading: false,
                isProcessing: false,
                id: null,
                role_id: null,
                first_name: null,
                last_name: null,
                email: null,
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
            ...mapState(['roles']),
            ...mapGetters(['isAdmin'])
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
                if (this.user) {
                    this.id = this.user.id;
                    this.role_id = this.user.role_id;
                    this.first_name = this.user.first_name;
                    this.last_name = this.user.last_name;
                    this.email = this.user.email;
                }
                if (_.isEmpty(this.roles)) {
                    this.isLoading = true;
                    this.$store.dispatch(LOAD_ROLES).finally(() => this.isLoading = false);
                }
            },
            onSubmit(evt) {
                evt.preventDefault();
                this.isProcessing = true;
                this.$store.dispatch(UPDATE_USER, _.pick(this, ['id', 'role_id', 'first_name', 'last_name', 'email']))
                    .then(() => this.showSuccess('User Updated'))
                    .catch(error => this.showNetworkError(error))
                    .finally(() => this.isProcessing = false);
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
