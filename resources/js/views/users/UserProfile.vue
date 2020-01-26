<template>
    <b-container>
        <div class="row">
            <div class="col-auto mr-auto">
                <h1><fa-icon icon="users" /> User Profile</h1>
            </div>
        </div>
        <user-form :user="user" @cancel="$router.back()" />
    </b-container>
</template>

<script>
    import { mapState } from 'vuex';
    import UserForm from './UserForm.vue';

    export default {
        name: 'UserProfile',
        components: {
            UserForm
        },
        computed: {
            ...mapState(['user'])
        }
    }
</script>

<style scoped>

</style>
