<template>
    <b-navbar toggleable="lg" type="dark" variant="dark">
        <b-navbar-brand :to="{ name: 'dashboard' }"><b-icon icon="graph-down" /> Calorie Counter</b-navbar-brand>

        <b-navbar-toggle target="nav-collapse"></b-navbar-toggle>

        <b-collapse id="nav-collapse" is-nav>
            <b-navbar-nav>
                <b-nav-item :to="{ name: 'dashboard' }">Dashboard</b-nav-item>
                <b-nav-item v-if="isAdmin || isManager" :to="{ name: 'users' }">Users</b-nav-item>
            </b-navbar-nav>

            <!-- Right aligned nav items -->
            <b-navbar-nav class="ml-auto">
                <b-nav-form>
                    <b-form-input size="sm" class="mr-sm-2" placeholder="Search" />
                    <b-button size="sm" class="my-2 my-sm-0" type="submit">Search</b-button>
                </b-nav-form>

                <b-nav-item-dropdown right>
                    <template v-slot:button-content>
                        <b-icon icon="flag-fill" /> EN
                    </template>
                    <b-dropdown-item href="#">English</b-dropdown-item>
                    <b-dropdown-item href="#">Español</b-dropdown-item>
                </b-nav-item-dropdown>

                <b-nav-item-dropdown right>
                    <!-- Using 'button-content' slot -->
                    <template v-slot:button-content>
                        <b-icon icon="person-fill" /> User
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
