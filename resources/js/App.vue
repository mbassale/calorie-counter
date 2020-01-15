<template>
    <div>
        <b-navbar toggleable="lg" type="dark" variant="dark">
            <b-navbar-brand href="#"><b-icon icon="graph-down" /> Calorie Counter</b-navbar-brand>

            <b-navbar-toggle target="nav-collapse"></b-navbar-toggle>

            <b-collapse id="nav-collapse" is-nav>
                <b-navbar-nav>
                    <b-nav-item href="#">Link</b-nav-item>
                    <b-nav-item href="#" disabled>Disabled</b-nav-item>
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
                        <b-dropdown-item href="#">Italiano</b-dropdown-item>
                    </b-nav-item-dropdown>

                    <b-nav-item-dropdown right>
                        <!-- Using 'button-content' slot -->
                        <template v-slot:button-content>
                            <b-icon icon="person-fill" />User
                        </template>
                        <b-dropdown-item href="#"><b-icon icon="toggles" /> Profile</b-dropdown-item>
                        <b-dropdown-item href="#"><b-icon icon="box-arrow-left" /> Sign Out</b-dropdown-item>
                    </b-nav-item-dropdown>
                </b-navbar-nav>
            </b-collapse>
        </b-navbar>
        <b-container class="mt-3">
            <router-view></router-view>
        </b-container>
    </div>
</template>

<script>
    export default {
        name: 'App'
    }
</script>

<style scoped>

</style>
