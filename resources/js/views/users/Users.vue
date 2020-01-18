<template>
    <b-container>
        <h1>Users</h1>
        <b-table striped hover responsive="md" :items="orderedUsers" :fields="fields" :busy="isLoading">
            <template v-slot:cell(role.name)="data">
                {{ data.value | capitalize }}
            </template>
            <template v-slot:cell(actions)="data">
                <b-button variant="primary" size="sm" title="Edit" @click="data.toggleDetails"
                          :disabled="disabled || isDeletingId === data.item.id">
                    <fa-icon icon="edit"/>
                </b-button>
                <b-button v-if="user.id !== data.item.id" variant="danger" size="sm" title="Delete"
                          @click="handleDelete(data.item)" :disabled="disabled || isDeletingId === data.item.id">
                    <fa-icon v-if="isDeletingId === data.item.id" icon="spinner" spin/>
                    <fa-icon v-else icon="trash"/>
                </b-button>
            </template>
            <template v-slot:row-details="row">
                <b-card>
                    <user-form :user="row.item" @cancel="row.toggleDetails"/>
                </b-card>
            </template>
        </b-table>
    </b-container>
</template>

<script>
    import _ from 'lodash';
    import {mapState} from 'vuex';
    import {DELETE_USER, LOAD_USERS} from '../../store/actions';
    import UserForm from './UserForm.vue';
    import ToastMixin from '../../mixins/ToastMixin';
    import ConfirmModalMixin from '../../mixins/ConfirmModalMixin';

    export default {
        name: 'Users',
        mixins: [ToastMixin, ConfirmModalMixin],
        components: {
            UserForm
        },
        data() {
            return {
                isLoading: false,
                isDeletingId: null,
                fields: [
                    {
                        key: 'role.name',
                        label: 'Role',
                        sortable: true
                    },
                    {
                        key: 'first_name',
                        label: 'First Name',
                        sortable: true
                    },
                    {
                        key: 'last_name',
                        label: 'Last Name',
                        sortable: true
                    },
                    {
                        key: 'email',
                        label: 'Email',
                        sortable: true
                    },
                    {
                        key: 'actions',
                        label: '',
                        sortable: false
                    }
                ]
            };
        },
        computed: {
            disabled() {
                return this.isLoading;
            },
            orderedUsers() {
                return this.users.map(user => {
                    user = _.cloneDeep(user);
                    user._showDetails = false;
                    return user;
                });
            },
            ...mapState(['user', 'users'])
        },
        mounted() {
            this.$nextTick(() => this.loadData());
        },
        methods: {
            loadData() {
                this.isLoading = true;
                return this.$store.dispatch(LOAD_USERS)
                    .catch(error => this.showNetworkError(error))
                    .finally(() => this.isLoading = false);
            },
            handleDelete(user) {
                return this.showConfirmDeletionModal('Are you sure to delete this user?')
                    .then(value => {
                        if (value) {
                            this.isDeletingId = user.id;
                            return this.$store.dispatch(DELETE_USER, user)
                                .then(() => this.showSuccess('User Deleted'))
                                .catch(error => this.showNetworkError(error))
                                .finally(() => this.isDeletingId = null);
                        }
                    });

            }
        }
    }
</script>

<style scoped>

</style>
