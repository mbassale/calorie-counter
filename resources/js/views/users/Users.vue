<template>
    <b-container>
        <h1>Users</h1>
        <b-table striped hover responsive="md" :items="orderedUsers" :fields="fields">
            <template v-slot:cell(role.name)="data">
                {{ data.value | capitalize }}
            </template>
            <template v-slot:cell(actions)="data">
                <b-button variant="primary" size="sm" title="Edit" @click="data.toggleDetails">
                    <fa-icon icon="edit" />
                </b-button>
                <b-button variant="primary" size="sm" title="Delete" @click="handleDelete(data.item)">
                    <fa-icon icon="trash" />
                </b-button>
            </template>
            <template v-slot:row-details="row">
                <b-card>
                    <user-form :user="row.item" @cancel="row.toggleDetails" />
                </b-card>
            </template>
        </b-table>
    </b-container>
</template>

<script>
    import _ from 'lodash';
    import { mapState } from 'vuex';
    import {LOAD_USERS} from "../../store/actions";
    import UserForm from './UserForm.vue';

    export default {
        name: 'Users',
        components: {
            UserForm
        },
        data() {
            return {
                isLoading: false,
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
            orderedUsers() {
                return this.users.map(user => {
                    user = _.cloneDeep(user);
                    user._showDetails = false;
                    return user;
                });
            },
            ...mapState(['users'])
        },
        mounted() {
            this.$nextTick(() => this.loadData());
        },
        methods: {
            loadData() {
                this.isLoading = true;
                return this.$store.dispatch(LOAD_USERS).finally(() => this.isLoading = false);
            },
            handleDelete(user) {
            }
        }
    }
</script>

<style scoped>

</style>
