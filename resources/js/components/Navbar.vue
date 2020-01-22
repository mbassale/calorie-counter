<template>
    <b-navbar toggleable="lg" type="dark" variant="dark">
        <b-navbar-brand :to="{ name: 'dashboard' }"><b-icon icon="graph-down" /> Calorie Counter</b-navbar-brand>

        <b-navbar-toggle target="nav-collapse"></b-navbar-toggle>

        <b-collapse id="nav-collapse" is-nav>
            <b-navbar-nav>
                <b-nav-item v-if="isAdmin || isManager" :to="{ name: 'users' }">
                    <fa-icon icon="users" /> Users
                </b-nav-item>
                <b-nav-item v-if="isAdmin || isUser" :to="{ name: 'meals' }">
                    <fa-icon icon="utensils" /> Meals
                </b-nav-item>
            </b-navbar-nav>

            <!-- Right aligned nav items -->
            <b-navbar-nav class="ml-auto">
                <b-nav-item-dropdown right>
                    <!-- Using 'button-content' slot -->
                    <template v-slot:button-content>
                        <b-icon icon="person-fill" /> {{ userFirstName }}
                    </template>
                    <b-dropdown-item href="#"><b-icon icon="toggles" /> Profile</b-dropdown-item>
                    <b-dropdown-item @click="handleLogout">
                        <b-icon icon="box-arrow-left" /> Sign Out
                    </b-dropdown-item>
                </b-nav-item-dropdown>
            </b-navbar-nav>
        </b-collapse>
    </b-navbar>
</template>

<script>
    import { mapState, mapGetters } from 'vuex';
    import { LOGOUT } from '../store/actions';

    export default {
        name: 'Navbar',
        computed: {
            userFirstName() {
                return this.user ? this.user.first_name : 'User';
            },
            ...mapState(['user']),
            ...mapGetters(['isAdmin', 'isManager', 'isUser'])
        },
        methods: {
            handleLogout() {
                this.$store.dispatch(LOGOUT).then(() => this.$router.push({ name: 'login' }));
            }
        }
    }
</script>

<style scoped>

</style>
