<template>
    <b-container>
        <div class="row">
            <div class="col-auto mr-auto">
                <h1><fa-icon icon="users" /> Users</h1>
            </div>
            <div class="col-auto">
                <b-form-input class="search-input" type="text" v-model="search" placeholder="Search.." />
                <b-button v-if="isAdmin || isManager" type="button" variant="primary" @click="handleCreate">
                    <fa-icon icon="plus" /> New User
                </b-button>
                <b-button type="button" variant="secondary" title="Refresh" @click="loadData">
                    <fa-icon v-if="isLoading" icon="spinner" spin />
                    <fa-icon v-else icon="sync" />
                </b-button>
            </div>
        </div>
        <b-table striped hover responsive="md" :items="orderedUsers" :fields="fields" :filter="search" :busy="isLoading">
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
                    <fa-icon v-if="data.item.id && isDeletingId === data.item.id" icon="spinner" spin/>
                    <fa-icon v-else icon="trash"/>
                </b-button>
            </template>
            <template v-slot:row-details="row">
                <b-card>
                    <user-form :user="row.item" @cancel="handleFormCancel(row)"/>
                </b-card>
            </template>
        </b-table>
    </b-container>
</template>

<script>
    import _ from 'lodash';
    import { mapState, mapGetters } from 'vuex';
    import { DELETE_USER, LOAD_USERS } from '../../store/actions';
    import UserForm from './UserForm.vue';
    import ToastMixin from '../../mixins/ToastMixin';
    import ConfirmModalMixin from '../../mixins/ConfirmModalMixin';
    import {ROLE_USER} from "../../store/roles";

    export default {
        name: 'Users',
        mixins: [ToastMixin, ConfirmModalMixin],
        components: {
            UserForm
        },
        data() {
            return {
                isLoading: false,
                isCreating: false,
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
                ],
                search: null
            };
        },
        computed: {
            disabled() {
                return this.isLoading;
            },
            orderedUsers() {
                let users = _.cloneDeep(this.users).map(user => {
                    user._showDetails = false;
                    return user;
                });
                if (this.isCreating) {
                    users.unshift({
                        _showDetails: true,
                        role_id: ROLE_USER,
                        id: null,
                        first_name: null,
                        last_name: null,
                        email: null
                    });
                }
                return users;
            },
            ...mapState(['user', 'users']),
            ...mapGetters(['isAdmin', 'isManager'])
        },
        watch: {
            users() {
                this.isCreating = false;
            }
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
            handleCreate() {
                this.isCreating = true;
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
            },
            handleFormCancel(row) {
                row.toggleDetails();
                if (!row.item.id && this.isCreating) {
                    this.isCreating = false;
                }
            }
        }
    }
</script>

<style scoped>
    .search-input {
        max-width: 200px;
        display:inline;
    }
</style>
