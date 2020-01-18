<template>
    <b-container>
        <b-table striped hover responsive="md" :items="users" :fields="fields">
            <template v-slot:cell(role.name)="data">
                {{ data.value | capitalize }}
            </template>
            <template v-slot:cell(actions)="data">
                <b-button variant="primary" size="sm" title="Edit" @click="handleEdit">
                    <fa-icon icon="edit" />
                </b-button>
                <b-button variant="primary" size="sm" title="Delete" @click="handleDelete">
                    <fa-icon icon="trash" />
                </b-button>
            </template>
        </b-table>
    </b-container>
</template>

<script>
    import _ from 'lodash';
    import { mapState } from 'vuex';
    import {LOAD_USERS} from "../../store/actions";

    export default {
        name: 'Users',
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
            handleEdit(user) {

            },
            handleDelete(user) {

            }
        }
    }
</script>

<style scoped>

</style>
